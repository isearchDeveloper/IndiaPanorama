import styles from "./QuickFacts.module.css";

interface FactItem {
  title: string;
  value: string;
}

interface QuickFactsProps {
  heading: string;
  items: FactItem[];
}

export default function QuickFacts({
  heading,
  items,
}: QuickFactsProps) {
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{heading}</h2>

      <div className={styles.grid}>
        {items.map((item) => (
          <div key={item.title} className={styles.card}>
            <h3>{item.title}</h3>

            <p>{item.value}</p>
          </div>
        ))}
      </div>
    </section>
  );
}