import Link from "next/link";
import styles from "./StateFestivalCard.module.css";

const FALLBACK = "/images/about-banner-pages.jpg";

type Festival = {
  slug: string; name: string; image: string; bannerImage?: string;
  description: string; tagline?: string; location: string; duration: string;
  bestTime: string; stateName?: string; categoryLabel?: string;
  month?: string; location_text?: string; month_text?: string;
};

interface Props {
  festival: Festival;
}

export default function StateFestivalCard({ festival }: Props) {
  return (
    <Link href={`/festivals/${festival.slug}`} className={styles.card}>
      <div className={styles.imgWrap}>
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          src={festival.image || FALLBACK}
          alt={festival.name}
          className={styles.img}
        />
       {festival.location_text &&  <span className={styles.badge}>{festival.location_text}</span>}
         {festival.month_text &&  <span className={styles.badge1}>{festival.month_text}</span>}
      </div>
      <div className={styles.body}>
        <h3 className={styles.name}>{festival.name}</h3>
        <p className={styles.desc}>{festival.description.slice(0, 100)}…</p>
        <span className={styles.cta}>View Festival &rsaquo;</span>
      </div>
    </Link>
  );
}

