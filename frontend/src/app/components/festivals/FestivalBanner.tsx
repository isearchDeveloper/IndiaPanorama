import styles from "./FestivalBanner.module.css";

type Festival = {
  slug: string; name: string; image: string; bannerImage?: string;
  description: string; tagline?: string; location: string; duration: string;
  bestTime: string; stateName?: string; categoryLabel?: string;
  month?: string; location_text?: string; month_text?: string;
};

interface Props {
  festival: Festival;
}

export default function FestivalBanner({ festival }: Props) {
  return (
    <section className={styles.banner} aria-label={`${festival.name} banner`}>
      {/* eslint-disable-next-line @next/next/no-img-element */}
      <img
        src={festival.bannerImage || "/images/about-banner-pages.jpg"}
        alt=""
        role="presentation"
        className={styles.bgImg}
        fetchPriority="high"
      />
      {/* banner text hidden — uncomment to restore
      <div className={styles.content}>
        <p className={styles.title}>{festival.name}</p>
        <p className={styles.line1}>Celebrate {festival.stateName}&apos;s {festival.categoryLabel}</p>
        <p className={styles.line2}>{festival.tagline}</p>
      </div>
      */}
    </section>
  );
}

