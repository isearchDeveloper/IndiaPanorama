import styles from "./QuickFacts.module.css";

interface FactItem {
  label: string;
  value: string;
}

interface Props {
  heading?: string;
  items: FactItem[];
}

export default function QuickFacts({ heading = "Quick Information", items }: Props) {
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{heading}</h2>
      <div className={styles.grid}>
        {items.map((item) => (
          <div key={item.label} className={styles.card}>
            <span className={styles.label}>{item.label}</span>
            <span className={styles.value}>{item.value}</span>
          </div>
        ))}
      </div>
    </section>
  );
}
