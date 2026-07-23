import styles from "./HowToReach.module.css";

export interface HowToReachItem {
  id: number;
  mode: string;
  description: string;
}

interface Props {
  heading: string;
  items: HowToReachItem[];
}

export default function HowToReach({ heading, items }: Props) {
  if (!items.length) return null;

  return (
    <div className={styles.wrapper}>
      <h2 className={styles.heading}>{heading}</h2>
      <div className={styles.list}>
        {items.map((item) => (
          <div key={item.id} className={styles.item}>
            <span className={styles.badge}>{item.mode} :</span>
            <p className={styles.desc}>{item.description}</p>
          </div>
        ))}
      </div>
    </div>
  );
}
