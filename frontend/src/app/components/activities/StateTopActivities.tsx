import Image from "next/image";
import Link from "next/link";
import type { StateTopActivityItem } from "@/services/activitiesService";
import styles from "./StateTopActivities.module.css";

interface Props {
  title: string;
  stateSlug: string;
  items: StateTopActivityItem[];
}

export default function StateTopActivities({ title, stateSlug, items }: Props) {
  if (!items.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>
      <div className={styles.grid}>
        {items.map((item) => (
          <Link
            key={item.slug}
            href={`/${stateSlug}/${item.location_slug}/${item.slug}`}
            className={styles.card}
          >
            <div className={styles.imgWrap}>
              {item.image ? (
                <Image
                  src={item.image}
                  alt={item.image_alt ?? item.name}
                  fill
                  sizes="(max-width:640px) 90vw, 33vw"
                  className={styles.img}
                />
              ) : (
                <div className={styles.imgPlaceholder} />
              )}
            </div>
            <div className={styles.body}>
              <p className={styles.cityRow}>
                <span className={styles.cityName}>{item.location_name}</span>
                <span className={styles.activityTag}> ({item.name})</span>
              </p>
              <span className={styles.exploreBtn}>Explore Now →</span>
            </div>
          </Link>
        ))}
      </div>
    </section>
  );
}
