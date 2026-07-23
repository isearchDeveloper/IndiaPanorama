import styles from "./CarChecklist.module.css";

interface Props {
  title: string;
  items: string[];
}

export default function CarChecklist({ title, items }: Props) {
  if (!items.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>
      <div className={styles.list}>
        {items.map((item, i) => (
          <div key={i} className={styles.item}>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true" className={styles.icon}>
              <circle cx="12" cy="12" r="11" fill="#2e7d32" />
              <path d="M7 12.5l3.5 3.5 6.5-7" stroke="#fff" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
            </svg>
            <span>{item}</span>
          </div>
        ))}
      </div>
    </section>
  );
}
