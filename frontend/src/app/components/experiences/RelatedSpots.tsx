import Image from "next/image";
import Link from "next/link";
export type RelatedSpot = {
  slug: string;
  title: string;
  image: string;
  themeSlug: string;
  stateSlug: string;
  citySlug?: string;
};
import styles from "./RelatedSpots.module.css";

interface Props {
  spots: RelatedSpot[];
  heading?: string;
}

function buildHref(spot: RelatedSpot): string {
  // unified detail pattern: /{state}/{city}/{slug}-experience
  if (spot.citySlug) return `/${spot.stateSlug}/${spot.citySlug}/${spot.slug}-experience`;
  // city na ho to state experience hub
  return `/${spot.stateSlug}/experiences`;
}

export default function RelatedSpots({
  spots,
  heading = "Related Experiences",
}: Props) {
  if (!spots.length) return null;
  return (
    <section className={styles.section}>
      <div className={styles.inner}>
        <h2 className={styles.heading}>{heading}</h2>
        <div className={styles.grid}>
          {spots.map((spot) => (
            <Link key={spot.slug} href={buildHref(spot)} className={styles.card}>
              <div className={styles.imgWrap}>
                <Image
                  src={spot.image}
                  alt={spot.title}
                  fill
                  sizes="(max-width: 576px) 100vw, (max-width: 900px) 50vw, 33vw"
                  className={styles.img}
                />
                <span className={styles.badge}>{spot.themeSlug.replace(/-/g, " ")}</span>
              </div>
              <div className={styles.body}>
                <h3 className={styles.title}>{spot.title}</h3>
                <span className={styles.cta}>Explore →</span>
              </div>
            </Link>
          ))}
        </div>
      </div>
    </section>
  );
}
