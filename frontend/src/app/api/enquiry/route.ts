/* eslint-disable @typescript-eslint/no-explicit-any */
import { NextRequest, NextResponse } from "next/server";

// Sab enquiry forms (home + sidebar) yahi route hit karte hain.
// Kaam do step me: (1) reCAPTCHA token Google se verify (secret key sirf yahan, server-side),
// (2) verify pass hone par payload backend CRM ko forward — category + source_url + client IP ke saath.

const API_BASE_URL = process.env.API_BASE_URL;
const API_TOKEN = process.env.API_TOKEN;
const RECAPTCHA_SECRET_KEY = process.env.RECAPTCHA_SECRET_KEY;

async function verifyCaptcha(token: string, ip: string | null): Promise<boolean> {
  if (!RECAPTCHA_SECRET_KEY) {
    console.error("Missing RECAPTCHA_SECRET_KEY");
    return false;
  }
  try {
    const params = new URLSearchParams({
      secret: RECAPTCHA_SECRET_KEY,
      response: token,
      ...(ip ? { remoteip: ip } : {}),
    });
    const res = await fetch("https://www.google.com/recaptcha/api/siteverify", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: params.toString(),
    });
    const json = await res.json();
    return !!json?.success;
  } catch (error) {
    console.error("reCAPTCHA verify error:", error);
    return false;
  }
}

function getClientIp(req: NextRequest): string | null {
  // proxy/CDN ke peeche real IP forwarded header me aata hai
  const forwarded = req.headers.get("x-forwarded-for");
  if (forwarded) return forwarded.split(",")[0].trim();
  return req.headers.get("x-real-ip");
}

// UI ka min-date restriction sirf browser attribute hai — DevTools/direct API call
// se bypass ho sakta hai, isliye server pe bhi wahi rule dobara check karo.
function isValidTravelDate(travelDate: string): boolean {
  if (!travelDate) return false;
  const date = new Date(travelDate + "T00:00:00");
  if (Number.isNaN(date.getTime())) return false;

  const minDate = new Date();
  minDate.setHours(0, 0, 0, 0);
  minDate.setDate(minDate.getDate() + 2);

  return date >= minDate;
}

export async function POST(req: NextRequest) {
  if (!API_BASE_URL) {
    return NextResponse.json({ success: false, message: "Server misconfigured" }, { status: 500 });
  }

  let body: any;
  try {
    body = await req.json();
  } catch {
    return NextResponse.json({ success: false, message: "Invalid request" }, { status: 400 });
  }

  const { captchaToken, ...enquiry } = body ?? {};

  if (!captchaToken) {
    return NextResponse.json({ success: false, message: "Captcha token missing" }, { status: 400 });
  }

  if (!isValidTravelDate(enquiry.travel_date)) {
    return NextResponse.json({ success: false, message: "Invalid travel date" }, { status: 400 });
  }

  const ip = getClientIp(req);

  const captchaOk = await verifyCaptcha(captchaToken, ip);
  if (!captchaOk) {
    return NextResponse.json({ success: false, message: "Captcha verification failed" }, { status: 400 });
  }

  try {
    const res = await fetch(`${API_BASE_URL}/enquiries`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-Public-Token": API_TOKEN ?? "",
      },
      body: JSON.stringify({ ...enquiry, ip_address: ip ?? undefined }),
    });

    if (!res.ok) {
      console.error(`Enquiry submit failed: ${res.status}`);
      return NextResponse.json({ success: false, message: "Submission failed" }, { status: 502 });
    }

    return NextResponse.json({ success: true });
  } catch (error) {
    console.error("Enquiry submit error:", error);
    return NextResponse.json({ success: false, message: "Submission failed" }, { status: 500 });
  }
}
