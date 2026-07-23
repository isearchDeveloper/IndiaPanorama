import styles from "./FestivalStats.module.css";

type FestivalStat = { id: number; label: string; value: string };

interface Props {
  stats: FestivalStat[];
}

export default function FestivalStats({ stats }: Props) {
  if (!stats.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>Stats</h2>
      <div className={styles.grid}>
        {stats.map((stat) => (
          <div key={stat.id} className={styles.card}>
            <span className={styles.value}>{stat.value}</span>
            <span className={styles.label}>{stat.label}</span>
          </div>
        ))}
      </div>
    </section>
  );
}

