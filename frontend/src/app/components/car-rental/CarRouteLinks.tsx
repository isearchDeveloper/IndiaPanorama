import Link from "next/link";
import styles from "./CarRouteLinks.module.css";

interface RouteItem {
  label: string;
  slug: string;
  url: string;
}

interface Props {
  title: string;
  items: RouteItem[];
}

export default function CarRouteLinks({ title, items }: Props) {
  if (!items.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>
      <div className={styles.tags}>
        {items.map((item) => (
          <Link key={item.slug} href={item.url} className={styles.tag}>
            <svg className={styles.tagIcon} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
              <path d="M3 12h18M13 5l7 7-7 7" />
            </svg>
            {item.label}
          </Link>
        ))}
      </div>
    </section>
  );
}
