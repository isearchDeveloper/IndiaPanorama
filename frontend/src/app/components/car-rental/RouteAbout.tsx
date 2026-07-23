"use client";

import styles from "./RouteAbout.module.css";

interface Stats {
  distance?: string;
  duration?: string;
  route?: string;
  best_season?: string;
}

interface Props {
  image?: string;
  imageAlt?: string;
  title?: string;
  description?: string;
  stats?: Stats;
}

export default function RouteAbout({ image, imageAlt, title, description, stats }: Props) {
  const hasStats = stats && (stats.distance || stats.duration || stats.route || stats.best_season);
  const statItems = hasStats
    ? [
        stats.distance   && { label: "Distance",    value: stats.distance },
        stats.duration   && { label: "Duration",    value: stats.duration },
        stats.route      && { label: "Route",       value: stats.route },
        stats.best_season && { label: "Best Season", value: stats.best_season },
      ].filter(Boolean) as { label: string; value: string }[]
    : [];

  return (
    // image na aaye to single-column full width, warna 2-column (image + text)
    <div className={image ? styles.wrap : styles.wrapFull}>
      {image && (
        // eslint-disable-next-line @next/next/no-img-element
        <img src={image} alt={imageAlt ?? title ?? ""} className={styles.img} />
      )}
      <div className={styles.content}>
        {title && <h2 className={styles.heading}>{title}</h2>}
        {description && <p className={styles.desc}>{description}</p>}
        {statItems.length > 0 && (
          <div className={styles.statsRow}>
            {statItems.map((s, i) => (
              <div key={i} className={styles.statItem}>
                <span className={styles.statLabel}>{s.label}</span>
                <span className={styles.statVal}>{s.value}</span>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
