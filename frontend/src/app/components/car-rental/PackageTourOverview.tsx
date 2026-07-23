import styles from "./PackageTourOverview.module.css";

interface OverviewItem {
  label: string;
  value: string;
}

interface Props {
  heading?: string;
  items: OverviewItem[];
}

export default function PackageTourOverview({ heading = "Tour Overview", items }: Props) {
  if (!items.length) return null;
  return (
    <div className={styles.section}>
      <h2 className={styles.heading}>{heading}</h2>
      <div className={styles.box}>
        {items.map((item, i) => (
          <div key={i} className={styles.item}>
            <span className={styles.label}>{item.label}</span>
            <span className={styles.val}>{item.value}</span>
          </div>
        ))}
      </div>
    </div>
  );
}
