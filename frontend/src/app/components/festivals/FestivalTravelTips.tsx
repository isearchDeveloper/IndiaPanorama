import styles from "./FestivalTravelTips.module.css";

interface Props {
  tips: string[];
  heading?: string;
}

export default function FestivalTravelTips({ tips, heading }: Props) {
  if (!tips.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{heading ?? "Travel Tips"}</h2>
      <ol className={styles.list}>
        {tips.map((tip, i) => (
          <li key={i} className={styles.item}>
            <span className={styles.number}>{i + 1}</span>
            <span className={styles.text}>{tip}</span>
          </li>
        ))}
      </ol>
    </section>
  );
}
