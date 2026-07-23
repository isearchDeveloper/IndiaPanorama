import type { Metadata } from "next";
import Link from "next/link";
import styles from "./page.module.css";

export const metadata: Metadata = {
  title: "Thank You | Indian Panorama",
  robots: { index: false, follow: false },
};

const CARDS = [
  {
    icon: "📍",
    title: "Explore Destinations",
    text: "Discover hidden gems across India",
    href: "/tourist-attractions",
  },
  {
    icon: "🎉",
    title: "Experiences",
    text: "Culture, heritage & spiritual journeys",
    href: "/experiences",
  },
  {
    icon: "🗓️",
    title: "Customized Holidays",
    text: "Tailor-made just for you",
    href: "/tour-packages",
  },
];

export default function ThankYouPage() {
  return (
    <section className={styles.section}>
      <div className={styles.inner}>
        <span className={styles.iconWrap} aria-hidden="true">
          <svg viewBox="0 0 24 24" width="34" height="34" fill="none">
            <path
              d="M12 21s-7.5-4.6-10-9.3C0.3 8.4 2 4.8 5.5 4c2.2-.5 4.3.6 6.5 3 2.2-2.4 4.3-3.5 6.5-3 3.5.8 5.2 4.4 3.5 7.7C19.5 16.4 12 21 12 21z"
              fill="#c0392b"
            />
          </svg>
        </span>

        <h1 className={styles.heading}>Thank You!</h1>
        <p className={styles.subheading}>We&apos;ve received your travel enquiry!</p>

        <p className={styles.body}>
          You&apos;re one step closer to your dream getaway! Our dedicated travel advisor will
          reach out within <strong className={styles.highlight}>24 hours</strong> to craft your
          perfect journey.
        </p>

        <p className={styles.exploreLabel}>While you wait, explore our curated experiences:</p>

        <div className={styles.cardGrid}>
          {CARDS.map((card) => (
            <Link key={card.title} href={card.href} className={styles.card}>
              <span className={styles.cardIcon} aria-hidden="true">{card.icon}</span>
              <h3 className={styles.cardTitle}>{card.title}</h3>
              <p className={styles.cardText}>{card.text}</p>
            </Link>
          ))}
        </div>

        <Link href="/" className={styles.backBtn}>
          ← Back to Home
        </Link>
      </div>
    </section>
  );
}
