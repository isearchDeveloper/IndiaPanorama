import styles from "./FestivalHistory.module.css";

interface Props {
  history: string;
  heading?: string;
}

export default function FestivalHistory({ history, heading }: Props) {
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{heading ?? "History & Origins"}</h2>
      <p className={styles.text}>{history}</p>
    </section>
  );
}
