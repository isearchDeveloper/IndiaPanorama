import styles from "./RouteHighlights.module.css";

interface HighlightItem {
  title: string;
  description?: string;
}

interface Props {
  title?: string;
  items: HighlightItem[];
}

export default function RouteHighlights({ title, items }: Props) {
  if (!items.length) return null;
  return (
    <div className={styles.section}>
      {title && <h2 className={styles.heading}>{title}</h2>}
      <div className={styles.grid}>
        {items.map((item, i) => (
          <div key={i} className={styles.card}>
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img src="/images/tick-double-04.svg" alt="" className={styles.icon} aria-hidden="true" />
            <h3 className={styles.title}>{item.title}</h3>
            {item.description && <p className={styles.desc}>{item.description}</p>}
          </div>
        ))}
      </div>
    </div>
  );
}
