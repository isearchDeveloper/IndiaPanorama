import Link from "next/link";
import SafeImage from "@/app/components/common/SafeImage";
import styles from "./ActivityGrid.module.css";

type Activity = {
  slug: string; name: string; image: string; bannerImage?: string;
  description: string; tagline?: string; categoryLabel?: string;
  duration: string; difficulty: string; bestTime?: string;
  groupSize?: string; price?: string; overview?: string;
  highlights?: string[]; includes?: string[]; excludes?: string[];
  thingsToCarry?: string[]; stateSlug: string; citySlug: string; cityName?: string;
  faqs?: { id: number; question: string; answer: string }[];
  packages?: { id: number; slug: string; title: string; image: string; nights: number; days: number }[];
};

interface Props {
  activities: Activity[];
  heading?: string;
}

export default function ActivityGrid({ activities, heading }: Props) {
  if (!activities.length) return null;
  return (
    <section className={styles.section}>
      {heading && <h2 className={styles.heading}>{heading}</h2>}
      <div className={styles.grid}>
        {activities.map((a) => (
          <Link
            key={a.slug}
            href={`/${a.stateSlug}/${a.citySlug}/${a.slug}`}
            className={styles.card}
          >
            <div className={styles.imgWrap}>
              <SafeImage
                src={a.image}
                alt={a.name}
                fill
                sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
                className={styles.img}
              />
              <span className={styles.categoryTag}>{a.categoryLabel}</span>
            </div>
            <div className={styles.body}>
              <h3 className={styles.name}>{a.name}</h3>
              <p className={styles.tagline}>{a.tagline}</p>
              <div className={styles.meta}>
                <span className={styles.metaItem}>
                  <span className={styles.metaIcon}>⏱</span>
                  {a.duration}
                </span>
                <span className={styles.metaItem}>
                  <span className={styles.metaIcon}>📍</span>
                  {a.cityName}
                </span>
                <span className={`${styles.metaItem} ${styles.difficulty} ${styles[`difficulty_${a.difficulty.toLowerCase()}`]}`}>
                  {a.difficulty}
                </span>
              </div>
              <div className={styles.footer}>
                <span className={styles.price}>{a.price}</span>
                <span className={styles.viewBtn}>View Details →</span>
              </div>
            </div>
          </Link>
        ))}
      </div>
    </section>
  );
}

