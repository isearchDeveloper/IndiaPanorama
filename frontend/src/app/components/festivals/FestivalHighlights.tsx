import styles from "./FestivalHighlights.module.css";

interface Props {
  highlights: string[];
  heading?: string;
}

export default function FestivalHighlights({ highlights, heading }: Props) {
  if (!highlights.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{heading ?? "Festival Highlights"}</h2>
      <ul className={styles.list}>
        {highlights.map((item, i) => (
          <li key={i} className={styles.item}>
            <span className={styles.checkIcon} aria-hidden="true">✓</span>
            {item}
          </li>
        ))}
      </ul>
    </section>
  );
}
