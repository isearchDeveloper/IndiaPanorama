import styles from "./AttractionGrid.module.css";

type CityGuideAttraction = { id: number; name: string; image: string; description: string; category: string; href?: string };

interface Props {
  attractions: CityGuideAttraction[];
  heading?: string;
}

export default function AttractionGrid({ attractions, heading = "Top Tourist Places to Visit" }: Props) {
  if (!attractions.length) return null;
  return (
    <div className={styles.wrapper}>
      <h2 className={styles.heading}>{heading}</h2>
      <div className={styles.list}>
        {attractions.map((item, i) => (
          <div key={item.id} className={styles.item}>
            <span className={styles.number}>{String(i + 1).padStart(2, "0")}</span>
            <div className={styles.content}>
              <p className={styles.name}>{item.name}</p>
              <p className={styles.desc}>{item.description}</p>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

