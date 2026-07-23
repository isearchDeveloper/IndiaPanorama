import styles from "./PageLoader.module.css";

interface PageLoaderProps {
  /** Number of skeleton cards to show in the grid */
  cards?: number;
  /** Accessible label for the loading state */
  label?: string;
}

export default function PageLoader({
  cards = 9,
  label = "Loading page content",
}: PageLoaderProps) {
  return (
    <div className={styles.wrapper} aria-label={label} role="status">
      <div className={styles.heroBanner} aria-hidden="true" />

      <div className={styles.inner}>
        <div className={styles.headingSkeleton} aria-hidden="true" />
        <div className={styles.textSkeleton} aria-hidden="true" />

        <div className={styles.grid} aria-hidden="true">
          {Array.from({ length: cards }).map((_, i) => (
            <div key={i} className={styles.cardSkeleton} />
          ))}
        </div>
      </div>
    </div>
  );
}
