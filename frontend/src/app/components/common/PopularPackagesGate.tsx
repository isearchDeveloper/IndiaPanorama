"use client";

import { useEffect, useState } from "react";
import { usePathname } from "next/navigation";

// company/legal pages where the site-wide Popular Packages section is not needed
const EXCLUDED = [
  "/about-us",
  "/contact-us",
  "/our-team",
  "/privacy-policy",
  "/terms-and-conditions",
  "/cancellation-refund-policy",
  "/awards-achievements",
  "/faq",
  "/thank-you",
];

export default function PopularPackagesGate({ children }: { children: React.ReactNode }) {
  const pathname = usePathname();
  const [is404, setIs404] = useState(false);

  // notFound() 404 pe bhi ye layout render hota hai — 404 UI ka marker
  // dekh ke section hide karo (pathname se 404 detect nahi ho sakta)
  useEffect(() => {
    setIs404(!!document.querySelector("[data-page-not-found]"));
  }, [pathname]);

  const hidden = EXCLUDED.some((p) => pathname === p || pathname.startsWith(`${p}/`));
  if (hidden || is404) return null;
  return <>{children}</>;
}
