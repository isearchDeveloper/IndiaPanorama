import styles from "./CarRouteHighlights.module.css";

function PinIcon() {
  return (
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <path
        d="M12 2C8.686 2 6 4.686 6 8c0 4.5 6 12 6 12s6-7.5 6-12c0-3.314-2.686-6-6-6z"
        stroke="currentColor"
        strokeWidth="1.8"
        strokeLinejoin="round"
      />
      <circle cx="12" cy="8" r="2" fill="currentColor" />
    </svg>
  );
}

interface HighlightItem {
  title?: string;
  name?: string;
  description?: string;
  desc?: string;
  icon?: string | null;
}

interface Props {
  title: string;
  items: HighlightItem[] | string[];
}

export default function CarRouteHighlights({ title, items }: Props) {
  if (!items || items.length === 0) return null;

  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>
      <div className={styles.grid}>
        {items.map((item, i) => {
          if (typeof item === "string") {
            return (
              <div key={i} className={styles.card}>
                <span className={styles.icon}><PinIcon /></span>
                <p className={styles.name}>{item}</p>
              </div>
            );
          }
          const name = item.title ?? item.name ?? "";
          const desc = item.description ?? item.desc ?? "";
          return (
            <div key={i} className={styles.card}>
              <span className={styles.icon}><PinIcon /></span>
              <div>
                <p className={styles.name}>{name}</p>
                {desc && <p className={styles.desc}>{desc}</p>}
              </div>
            </div>
          );
        })}
      </div>
    </section>
  );
}
