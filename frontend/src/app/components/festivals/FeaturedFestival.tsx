import Link from "next/link";
import styles from "./FeaturedFestival.module.css";

const FALLBACK = "/images/about-banner-pages.jpg";

type Festival = {
  slug: string; name: string; image: string; bannerImage?: string;
  description: string; tagline?: string; location: string; duration: string;
  bestTime: string; stateName?: string; categoryLabel?: string;
  month?: string; location_text?: string; month_text?: string;
};

interface Props {
  festival: Festival;
  label?: string;
}

export default function FeaturedFestival({ festival, label = "Featured Festival" }: Props) {
  return (
    <section className={styles.section}>
      <div className={styles.card}>
        <div className={styles.imgWrap}>
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img
            src={festival.image || FALLBACK}
            alt={festival.name}
            className={styles.img}
          />
        </div>
        <div className={styles.body}>
          <span className={styles.label}>{label}</span>
          <h2 className={styles.name}>{festival.name}</h2>
          <ul className={styles.meta}>
            <li><span className={styles.metaKey}>Location:</span> {festival.location}</li>
            <li><span className={styles.metaKey}>Duration:</span> {festival.duration}</li>
            <li><span className={styles.metaKey}>Best Time:</span> {festival.bestTime}</li>
          </ul>
          <p className={styles.desc}>{festival.description.slice(0, 200)}…</p>
          <Link href={`/festivals/${festival.slug}`} className={styles.btn}>
            Explore Festival
          </Link>
        </div>
      </div>
    </section>
  );
}

