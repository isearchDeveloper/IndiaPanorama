import Link from "next/link";
import styles from "./CarPackageLinks.module.css";

interface PkgItem {
  label: string;
  slug: string;
  url: string;
}

interface Props {
  title: string;
  items: PkgItem[];
}

export default function CarPackageLinks({ title, items }: Props) {
  if (!items.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>
      <div className={styles.grid}>
        {items.map((item) => (
          <Link key={item.slug} href={item.url} className={styles.card}>
            <span className={styles.label}>{item.label}</span>
            <span className={styles.arrow} aria-hidden="true">→</span>
          </Link>
        ))}
      </div>
    </section>
  );
}
