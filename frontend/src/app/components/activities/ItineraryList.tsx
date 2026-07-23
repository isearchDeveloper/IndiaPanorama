import styles from "./ItineraryList.module.css";

interface Item {
  id: number;
  title: string;
  description: string;
}

interface Props {
  heading: string;
  items: Item[];
}

export default function ItineraryList({ heading, items }: Props) {
  if (!items.length) return null;

  return (
    <div className={styles.wrapper}>
      <h2 className={styles.heading}>{heading}</h2>
      <div className={styles.list}>
        {items.map((item) => (
          <div key={item.id} className={styles.item}>
            <span className={styles.num}>{String(item.id).padStart(2, "0")}</span>
            <div className={styles.content}>
              <p className={styles.title}>{item.title}</p>
              <p className={styles.desc}>{item.description}</p>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
