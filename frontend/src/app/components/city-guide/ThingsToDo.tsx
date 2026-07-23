import styles from "./ThingsToDo.module.css";

export interface ThingsToDoItem {
  id: number;
  title: string;
  description: string;
  duration?: string;
  bestFor?: string;
  cost?: string;
}

interface Props {
  heading: string;
  items: ThingsToDoItem[];
}

export default function ThingsToDo({ heading, items }: Props) {
  if (!items.length) return null;
  return (
    <div className={styles.wrapper}>
      <h2 className={styles.heading}>{heading}</h2>
      <div className={styles.list}>
        {items.map((item) => (
          <div key={item.id} className={styles.item}>
            <span className={styles.arrow}>→</span>
            <div className={styles.content}>
              <p className={styles.title}>{item.title}</p>
              {/* description can be CMS HTML (activity/attraction detail) */}
              <div
                className={`${styles.desc} cms-content`}
                dangerouslySetInnerHTML={{ __html: item.description }}
              />
              {item.duration && (
                <p className={styles.meta}><strong>Duration &amp; Timing:</strong> {item.duration}</p>
              )}
              {item.bestFor && (
                <p className={styles.meta}><strong>Best For:</strong> {item.bestFor}</p>
              )}
              {item.cost && (
                <p className={styles.meta}><strong>Approximate Cost:</strong> {item.cost}</p>
              )}
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
