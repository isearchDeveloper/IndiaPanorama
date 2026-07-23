import styles from "./FestivalStats.module.css";

interface StatItem {
  value: string;
  label: string;
}

interface Props {
  items: StatItem[];
}

export default function FestivalStats({ items }: Props) {
  if (!items.length) return null;
  return (
    <div className={styles.grid}>
      {items.map((stat, i) => (
        <div key={i} className={styles.card}>
          <span className={styles.value}>{stat.value}</span>
          <span className={styles.label}>{stat.label}</span>
        </div>
      ))}
    </div>
  );
}
