/* eslint-disable @typescript-eslint/no-explicit-any */
// Sab enquiry forms (home + sidebar) ka shared helper — category auto-detect current URL se,
// aur /api/enquiry route ko submit (wahi reCAPTCHA verify + backend forward karta hai).

export type EnquiryCategory = "Holidays" | "Experiences" | "Destination" | "Activities" | "Car Rental" | "General";

// site ke non-destination top-level routes — inko kabhi /{state} nahi samajhna
const KNOWN_TOP_LEVEL = [
  "about-us", "contact-us", "our-team", "privacy-policy", "terms-and-conditions",
  "cancellation-refund-policy", "awards-achievements", "faq", "blog", "tour-packages",
  "experiences", "activities", "car-rental", "tourist-attractions", "festivals", "api",
];

// pathname se category — order matters (zyada specific pehle)
export function detectCategory(pathname: string): EnquiryCategory {
  if (pathname.startsWith("/experiences") || /\/experiences(\/|$)/.test(pathname)) return "Experiences";
  if (pathname.startsWith("/car-rental")) return "Car Rental";
  if (pathname.startsWith("/activities") || /\/activities(\/|$)/.test(pathname)) return "Activities";
  if (pathname.startsWith("/tour-packages") || /\/tour-packages(\/|$)/.test(pathname)) return "Holidays";
  if (pathname.startsWith("/tourist-attractions") || /\/tourist-attractions(\/|$)/.test(pathname)) {
    return "Destination";
  }

  // /{state} ya /{state}/{city} — sirf jab pehla segment koi known static route na ho
  const segments = pathname.split("/").filter(Boolean);
  if (segments.length >= 1 && segments.length <= 2 && !KNOWN_TOP_LEVEL.includes(segments[0])) {
    return "Destination";
  }

  return "General";
}

interface EnquiryPayload {
  name: string;
  email: string;
  phone: string;
  country: string;
  budget: string;
  no_of_persons: string;
  travel_date: string;
  arrival_city: string;
  departure_city?: string;
  message: string;
  captchaToken: string;
}

export async function submitEnquiry(payload: EnquiryPayload): Promise<{ success: boolean; message?: string }> {
  const pathname = typeof window !== "undefined" ? window.location.pathname : "/";
  const category = detectCategory(pathname);
  const sourceUrl = typeof window !== "undefined" ? window.location.href : "";

  try {
    const res = await fetch("/api/enquiry", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        category: category.toLowerCase().replace(/\s+/g, "_"),
        enquiry_type: "Plan Trip Enquiries",
        name: payload.name,
        email: payload.email,
        phone: payload.phone,
        country: payload.country,
        budget: payload.budget,
        no_of_persons: payload.no_of_persons,
        travel_date: payload.travel_date,
        arrival_city: payload.arrival_city,
        departure_city: payload.departure_city ?? "",
        message: payload.message,
        source_url: sourceUrl,
        captchaToken: payload.captchaToken,
      }),
    });
    const json = await res.json();
    return json;
  } catch (error) {
    console.error("Enquiry submit error:", error);
    return { success: false, message: "Network error" };
  }
}
