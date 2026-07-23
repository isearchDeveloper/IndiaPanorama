import Image from "next/image";
import type { ReactNode } from "react";
import styles from "./BestExperienceSection.module.css";

function getIcon(iconType: string): ReactNode {
  switch (iconType) {
    case "car":
      return (
        <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
          <path d="M7 26V28C7 29.1 7.9 30 9 30H11C12.1 30 13 29.1 13 28V26H27V28C27 29.1 27.9 30 29 30H31C32.1 30 33 29.1 33 28V26L35 18H5L7 26Z" stroke="#2d4a35" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round"/>
          <path d="M9 18L12 10H28L31 18" stroke="#2d4a35" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round"/>
          <circle cx="13" cy="22" r="2" stroke="#2d4a35" strokeWidth="1.8"/>
          <circle cx="27" cy="22" r="2" stroke="#2d4a35" strokeWidth="1.8"/>
        </svg>
      );
    case "user":
      return (
        <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
          <circle cx="20" cy="16" r="5" stroke="#2d4a35" strokeWidth="1.8"/>
          <path d="M8 32C8 26.477 13.373 22 20 22C26.627 22 32 26.477 32 32" stroke="#2d4a35" strokeWidth="1.8" strokeLinecap="round"/>
        </svg>
      );
    case "Comfortable":
      return (
        <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
          <circle cx="20" cy="10" r="3" stroke="#2d4a35" strokeWidth="1.8"/>
          <path d="M20 14V24M14 18H26M16 24L13 32M24 24L27 32" stroke="#2d4a35" strokeWidth="1.8" strokeLinecap="round"/>
        </svg>
      );
    default:
      return (
        <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
          <rect x="6" y="13" width="28" height="20" rx="3" stroke="#2d4a35" strokeWidth="1.8"/>
          <circle cx="20" cy="23" r="5" stroke="#2d4a35" strokeWidth="1.8"/>
          <path d="M14 13L16 9H24L26 13" stroke="#2d4a35" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round"/>
          <circle cx="29" cy="17" r="1.5" fill="#2d4a35"/>
        </svg>
      );
  }
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export default function BestExperienceSection({ data }: { data: any }) {
  if (!data) return null;

  const { heading, description, bg_image, bg_image_alt, items = [] } = data.content;

  return (
    <section className={styles.section}>
      <div className={styles.bgWrap}>
        <Image src={bg_image ?? "/images/about-banner-pages.jpg"} alt={bg_image_alt ?? ""} fill className={styles.bgImg} sizes="100vw" priority />
        <div className={styles.overlay} />
      </div>
      <div className={styles.inner}>
        <div className={styles.headingBlock}>
          {heading && <h2 className={styles.heading}>{heading}</h2>}
          {description && <p className={styles.subtext}>{description}</p>}
        </div>
        {items.length > 0 && (
          <div className={styles.cardsRow}>
            {items.map((item: any, idx: number) => (
              <div key={idx} className={styles.card}>
                <div className={styles.iconWrap}>{getIcon(item.icon ?? "")}</div>
                <p className={styles.cardLabel}>{item.title}</p>
              </div>
            ))}
          </div>
        )}
      </div>
    </section>
  );
}
