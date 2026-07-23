import styles from "./PackageHighlights.module.css";

interface Group {
  name: string;
  attractions: string[];
}

interface Props {
  heading?: string;
  groups: Group[];
}

export default function PackageHighlights({ heading = "Route Highlights", groups }: Props) {
  if (!groups.length) return null;
  return (
    <div className={styles.section}>
      <h2 className={styles.heading}>{heading}</h2>
      <div className={styles.grid}>
        {groups.map((group, i) => (
          <div key={i} className={styles.group}>
            <div className={styles.groupHeader}>
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img src="/images/tick-double-04.svg" alt="" width={25} height={25} aria-hidden="true" />
              <h3 className={styles.groupName}>{group.name}</h3>
            </div>
            <ul className={styles.list}>
              {group.attractions.map((a, j) => (
                <li key={j} className={styles.item}>
                  <span className={styles.dot} aria-hidden="true" />
                  {a}
                </li>
              ))}
            </ul>
          </div>
        ))}
      </div>
    </div>
  );
}
