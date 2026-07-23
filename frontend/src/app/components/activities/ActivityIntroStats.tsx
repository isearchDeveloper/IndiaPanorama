import type { ActivityStatItem } from "@/services/activitiesService";
import styles from "./ActivityIntroStats.module.css";

interface Props {
  shortDescription: string | null;
  stats: { image: string | null; image_alt: string | null; items: ActivityStatItem[] };
}

export default function ActivityIntroStats({ shortDescription, stats }: Props) {
  const items = stats.items ?? [];
  const hasStats = items.length > 0;
  const hasImage = !!stats.image;
  const hasRightCol = hasStats || hasImage;

  if (!shortDescription && !hasRightCol) return null;

  return (
    <div className={hasRightCol ? styles.textStats : styles.textOnly}>
      {/* Stats float right; must come before the text in DOM so the
          text wraps beside them, then flows full width below — no gap */}
      {hasRightCol && (
        <div className={styles.statsGrid}>
          {hasStats && (
            <div className={styles.statsTopRow}>
              {items.slice(0, 3).map((s) => (
                <div key={s.label} className={styles.statBox}>
                  <span className={styles.statValue}>{s.stat}</span>
                  <span className={styles.statLabel}>{s.label}</span>
                </div>
              ))}
            </div>
          )}

          {(items[3] || hasImage) && (
            <div className={hasImage ? styles.statsBottomRow : styles.statsBottomRowFull}>
              {items[3] && (
                <div className={styles.statBox}>
                  <span className={styles.statValue}>{items[3].stat}</span>
                  <span className={styles.statLabel}>{items[3].label}</span>
                </div>
              )}
              {hasImage && (
                <div className={styles.illustrationBox}>
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img
                    src={stats.image!}
                    alt={stats.image_alt ?? ""}
                    className={styles.illustration}
                    loading="lazy"
                  />
                </div>
              )}
            </div>
          )}
        </div>
      )}

      {/* Intro text from API (HTML) — wraps around the floated stats */}
      {shortDescription && (
        <div
          className={styles.introHtml}
          dangerouslySetInnerHTML={{ __html: shortDescription }}
        />
      )}
    </div>
  );
}
