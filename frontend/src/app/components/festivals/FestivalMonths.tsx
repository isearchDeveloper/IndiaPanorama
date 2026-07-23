"use client";

import { useState } from "react";
import Link from "next/link";
import styles from "./FestivalMonths.module.css";

const FALLBACK = "/images/about-banner-pages.jpg";

type Festival = {
  slug: string; name: string; image: string; bannerImage?: string;
  description: string; tagline?: string; location: string; duration: string;
  bestTime: string; stateName?: string; categoryLabel?: string;
  month?: string; location_text?: string; month_text?: string;
};

type FestivalMonth = { slug: string; name: string; shortName: string; festivals: string[] };

interface Props {
  months: FestivalMonth[];
  festivals: Festival[];
  heading?: string;
}

export default function FestivalMonths({ months, festivals, heading }: Props) {
  const activeMonths = months.filter((m) => m.festivals.length > 0);
  const [activeSlug, setActiveSlug] = useState<string>(
    months[0]?.slug
  );

  const activeFestivals = festivals.filter((f) => f.month === activeSlug);

  if (!months || months.length === 0 || !festivals || festivals.length === 0) return null;

  return (
    <section className={styles.section}>
      {heading && <h2 className={styles.heading}>{heading}</h2>}

      <noscript>
        <div className={styles.noJsFallback}>
          {activeMonths.map((month) => {
            const mf = festivals.filter((f) => f.month === month.slug);
            return (
              <div key={month.slug}>
                <h3>{month.name}</h3>
                <ul>
                  {mf.map((f) => (
                    <li key={f.slug}>
                      <a href={`/festivals/${f.slug}`}>{f.name}</a>
                    </li>
                  ))}
                </ul>
              </div>
            );
          })}
        </div>
      </noscript>

      {/* Month pill tabs */}
      <div className={styles.tabs} role="tablist">
        {months.map((month) => (
          <button
            key={month.slug}
            role="tab"
            aria-selected={activeSlug === month.slug}
            onClick={() => setActiveSlug(month.slug)}
            className={`${styles.tab} ${activeSlug === month.slug ? styles.tabActive : ""}`}
          >
            {month.shortName}
          </button>
        ))}
      </div>

      {/* Circular festival images */}
      <div className={styles.panel}>
        {activeFestivals.length > 0 ? (
          <div className={styles.circleRow}>
            {activeFestivals.map((festival) => (
              <Link
                key={festival.slug}
                href={`/festivals/${festival.slug}`}
                className={styles.circleItem}
              >
                <div className={styles.circle}>
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img src={festival.image || FALLBACK} alt={festival.name} className={styles.circleImg} />
                </div>
                <span className={styles.circleName}>{festival.name}</span>
              </Link>
            ))}
          </div>
        ) : (
          <p className={styles.emptyMsg}>No festivals listed for this month yet.</p>
        )}
      </div>
    </section>
  );
}

