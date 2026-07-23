import Link from "next/link";
import styles from "./CarDestinationTags.module.css";

interface DestItem {
  label: string;
  slug: string;
  url: string;
}

interface Props {
  title: string;
  items: DestItem[];
}

export default function CarDestinationTags({ title, items }: Props) {
  if (!items.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>
      <div className={styles.tags}>
        {items.map((item) => (
          <Link key={item.slug} href={item.url} className={styles.tag}>
            {item.label}
          </Link>
        ))}
      </div>
    </section>
  );
}
