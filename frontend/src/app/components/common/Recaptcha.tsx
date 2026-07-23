"use client";

import { useEffect, useRef, useState } from "react";
import Script from "next/script";

// Google reCAPTCHA v2 checkbox ("I'm not a robot") — sab enquiry forms me reuse.
// Site key public hai (.env NEXT_PUBLIC_RECAPTCHA_SITE_KEY), secret sirf server route me.

const SITE_KEY = process.env.NEXT_PUBLIC_RECAPTCHA_SITE_KEY;
const WIDGET_WIDTH = 304; // Google widget ki fixed default width

declare global {
  interface Window {
    grecaptcha?: {
      render: (
        container: HTMLElement,
        opts: { sitekey: string; callback: (token: string) => void; "expired-callback"?: () => void }
      ) => number;
      reset: (id?: number) => void;
    };
    onRecaptchaLoad?: () => void;
  }
}

interface Props {
  onChange: (token: string | null) => void;
  resetKey?: unknown; // ye value badalne par widget reset hota hai (submit ke baad)
}

export default function Recaptcha({ onChange, resetKey }: Props) {
  const outerRef = useRef<HTMLDivElement>(null);
  const containerRef = useRef<HTMLDivElement>(null);
  const widgetIdRef = useRef<number | null>(null);
  const [scale, setScale] = useState(1);

  useEffect(() => {
    if (!SITE_KEY) {
      console.error("Missing NEXT_PUBLIC_RECAPTCHA_SITE_KEY");
      return;
    }

    function renderWidget() {
      if (!containerRef.current || !window.grecaptcha || widgetIdRef.current !== null) return;
      widgetIdRef.current = window.grecaptcha.render(containerRef.current, {
        sitekey: SITE_KEY as string,
        callback: (token: string) => onChange(token),
        "expired-callback": () => onChange(null),
      });
    }

    if (window.grecaptcha) {
      renderWidget();
    } else {
      window.onRecaptchaLoad = renderWidget;
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  // parent form submit ke baad widget reset karna chahe to resetKey badal do
  useEffect(() => {
    if (widgetIdRef.current !== null && window.grecaptcha) {
      window.grecaptcha.reset(widgetIdRef.current);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [resetKey]);

  // narrow cards (sidebar form) me 304px fixed widget overflow na kare — parent
  // ki actual width dekh ke shrink karo, kabhi bada mat karo (max scale 1)
  useEffect(() => {
    if (!outerRef.current) return;
    const el = outerRef.current;
    const update = () => {
      const available = el.clientWidth;
      if (!available) return;
      setScale(Math.min(1, available / WIDGET_WIDTH));
    };
    update();
    const ro = new ResizeObserver(update);
    ro.observe(el);
    return () => ro.disconnect();
  }, []);

  return (
    <div ref={outerRef} style={{ width: "100%", overflow: "hidden" }}>
      <Script
        src="https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=explicit"
        strategy="lazyOnload"
      />
      <div
        style={{
          transform: `scale(${scale})`,
          transformOrigin: "0 0",
          width: WIDGET_WIDTH,
          height: scale < 1 ? 78 * scale : undefined,
        }}
      >
        <div ref={containerRef} />
      </div>
    </div>
  );
}
