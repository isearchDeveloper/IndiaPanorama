"use client";

import Script from "next/script";
import { useEffect, useState } from "react";
import { usePathname } from "next/navigation";
import styles from "./GoogleReviews.module.css";

// Elfsight Google Reviews widget — har page pe PartnerSlider ke upar,
// company/legal pages pe nahi (PopularPackages wali hi exclusion list)
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

export default function GoogleReviews() {
  const pathname = usePathname();
  const [is404, setIs404] = useState(false);

  // 404 UI pe bhi layout render hota hai — marker dekh ke hide
  useEffect(() => {
    setIs404(!!document.querySelector("[data-page-not-found]"));
  }, [pathname]);

  const hidden = EXCLUDED.some((p) => pathname === p || pathname.startsWith(`${p}/`));
  if (hidden || is404) return null;

  return (
    <section className={styles.section} aria-label="Google Reviews">
      <div className={styles.inner}>
        <Script src="https://elfsightcdn.com/platform.js" strategy="lazyOnload" />
        {/* Elfsight Google Reviews | Indian Panorama Copy */}
        <div
          className="elfsight-app-63a56754-3107-49fd-9f82-253cbacecc7f"
          data-elfsight-app-lazy
        />
      </div>
    </section>
  );
}
