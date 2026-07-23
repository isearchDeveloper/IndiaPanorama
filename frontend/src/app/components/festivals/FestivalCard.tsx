import Link from "next/link";
import styles from "./FestivalCard.module.css";

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

export default function FestivalCard({ festival }: Props) {
  return (
    <Link href={`/festivals/${festival.slug}`} className={styles.card}>
      <div className={styles.imgWrap}>
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          src={festival.image || FALLBACK}
          alt={festival.name}
          className={styles.img}
        />
      </div>
      <div className={styles.body}>
        <h3 className={styles.name}>{festival.name}</h3>
        <span className={styles.cta}>View Details &rsaquo;</span>
      </div>
    </Link>
  );
}

