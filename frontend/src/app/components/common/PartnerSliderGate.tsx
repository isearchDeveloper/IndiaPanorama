"use client";

import { useEffect, useState } from "react";
import { usePathname } from "next/navigation";

// PartnerSlider (logo strip) — thank-you page pe nahi (PopularPackages/GoogleReviews wali hi list)
const EXCLUDED = ["/thank-you"];

export default function PartnerSliderGate({ children }: { children: React.ReactNode }) {
  const pathname = usePathname();
  const [is404, setIs404] = useState(false);

  useEffect(() => {
    setIs404(!!document.querySelector("[data-page-not-found]"));
  }, [pathname]);

  const hidden = EXCLUDED.some((p) => pathname === p || pathname.startsWith(`${p}/`));
  if (hidden || is404) return null;
  return <>{children}</>;
}
