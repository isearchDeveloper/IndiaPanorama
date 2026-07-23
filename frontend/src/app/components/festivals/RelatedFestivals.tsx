import FestivalCard from "./FestivalCard";
import styles from "./RelatedFestivals.module.css";

type Festival = {
  slug: string; name: string; image: string; bannerImage?: string;
  description: string; tagline?: string; location: string; duration: string;
  bestTime: string; stateName?: string; categoryLabel?: string;
  month?: string; location_text?: string; month_text?: string;
};

interface Props {
  festivals: Festival[];
  heading?: string;
}

export default function RelatedFestivals({ festivals, heading }: Props) {
  if (!festivals.length) return null;
  return (
    <section className={styles.section}>
      <div className={styles.inner}>
        <h2 className={styles.heading}>{heading ?? "Related Festivals"}</h2>
        <div className={styles.grid}>
          {festivals.map((festival) => (
            <FestivalCard key={festival.slug} festival={festival} />
          ))}
        </div>
      </div>
    </section>
  );
}

