import styles from "./ThingsToDo.module.css";

interface ThingToDoItem {
  id: number;
  title: string;
  description: string;
  icon?: string;
}

interface Props {
  heading: string;
  items: ThingToDoItem[];
}

export default function ThingsToDo({ heading, items }: Props) {
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{heading}</h2>
      <div className={styles.grid}>
        {items.map((item) => (
          <div key={item.id} className={styles.card}>
            {item.icon && <span className={styles.icon}>{item.icon}</span>}
            <h3 className={styles.title}>{item.title}</h3>
            <p className={styles.desc}>{item.description}</p>
          </div>
        ))}
      </div>
    </section>
  );
}
