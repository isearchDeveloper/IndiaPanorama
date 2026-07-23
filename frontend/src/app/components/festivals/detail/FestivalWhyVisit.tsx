import styles from "./FestivalWhyVisit.module.css";

interface Item {
  title: string;
  description: string;
}

interface Props {
  title: string;
  items: Item[];
}

export default function FestivalWhyVisit({ title, items }: Props) {
  if (!items.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>
      <div className={styles.grid}>
        {items.map((item, i) => (
          <div key={i} className={styles.card}>
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img
              src="/images/tick-double-04.svg"
              alt=""
              aria-hidden="true"
              className={styles.icon}
            />
            <h3 className={styles.title}>{item.title}</h3>
            <p className={styles.desc}>{item.description}</p>
          </div>
        ))}
      </div>
    </section>
  );
}
