import type { ActivitySeasonalItem } from "@/services/activitiesService";
import styles from "./ActivitySeasonalSection.module.css";

interface Props {
  title: string;
  items: ActivitySeasonalItem[];
}

export default function ActivitySeasonalSection({ title, items }: Props) {
  if (!items.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>
      <div className={styles.grid}>
        {items.map((s) => (
          <div key={s.season_label} className={styles.card}>
            <p className={styles.seasonRow}>
              <strong className={styles.seasonName}>{s.season_label}</strong>
              {s.period_text && <span className={styles.months}> ({s.period_text})</span>}
            </p>
            <p className={styles.activities}>{s.activities_text}</p>
          </div>
        ))}
      </div>
    </section>
  );
}
