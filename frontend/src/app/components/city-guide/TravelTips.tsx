import styles from "./TravelTips.module.css";

interface Props {
  heading: string;
  tips: string[];
}

export default function TravelTips({ heading, tips }: Props) {
  if (!tips.length) return null;

  // API se single string HTML/entities ke saath aati hai (&bull;, <br />, &mdash;)
  // chahe wo "<" se shuru na ho — hamesha HTML ki tarah render karo
  const single = tips.length === 1;

  return (
    <div className={styles.wrapper}>
      <h2 className={styles.heading}>{heading}</h2>
      {single ? (
        <div
          className={styles.list}
          dangerouslySetInnerHTML={{ __html: tips[0] }}
        />
      ) : (
        <ul className={styles.list}>
          {tips.map((tip, i) => (
            <li
              key={i}
              className={styles.item}
              dangerouslySetInnerHTML={{ __html: tip }}
            />
          ))}
        </ul>
      )}
    </div>
  );
}
