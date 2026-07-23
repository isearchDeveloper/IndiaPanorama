export interface ThemeQuickInfoItem {
  label: string;
  value: string;
}
import styles from "./ThemeQuickInfo.module.css";

interface Props {
  heading?: string;
  items: ThemeQuickInfoItem[];
}

export default function ThemeQuickInfo({ heading = "Quick Information", items }: Props) {
  if (!items.length) return null;

  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{heading}</h2>
      <div className={styles.grid}>
        {items.map((item) => (
          <div key={item.label} className={styles.box}>
            <span className={styles.label}>{item.label}</span>
            <span className={styles.value}>{item.value}</span>
          </div>
        ))}
      </div>
    </section>
  );
}
