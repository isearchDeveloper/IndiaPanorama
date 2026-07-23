import Link from "next/link";
import styles from "./CarPopularLocations.module.css";

interface LocationItem {
  title: string;
  slug: string;
  url: string;
}

interface Props {
  title: string;
  description?: string;
  items: LocationItem[];
}

export default function CarPopularLocations({ title, description, items }: Props) {
  if (!items.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>
      {description && <p className={styles.desc}>{description}</p>}
      <div className={styles.grid}>
        {items.map((item) => (
          <Link key={item.slug} href={item.url} className={styles.card}>
            <span className={styles.dot} aria-hidden="true" />
            {item.title}
            <span className={styles.arrow} aria-hidden="true">›</span>
          </Link>
        ))}
      </div>
    </section>
  );
}
