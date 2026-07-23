import type { ExpStat } from "@/data/experiences/rootPageData";
import styles from "./ExperienceStats.module.css";

export default function ExperienceStats({ items }: { items: ExpStat[] }) {
  if (!items.length) return null;

  return (
    <div className={styles.row}>
      {items.map((s) => (
        <div key={s.label} className={styles.box}>
          <span className={styles.value}>{s.stat}</span>
          <span className={styles.label}>{s.label}</span>
        </div>
      ))}
    </div>
  );
}
