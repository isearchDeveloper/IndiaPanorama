import SafeImage from "@/app/components/common/SafeImage";
import styles from "./WhyVisit.module.css";

interface Props {
  title: string;
  description: string;
  points: string[];
  image: string | null;
}

export default function WhyVisit({ title, description, points, image }: Props) {
  const half = Math.ceil(points.length / 2);
  const col1 = points.slice(0, half);
  const col2 = points.slice(half);

  return (
    <section className={styles.section}>
      <div className={styles.inner}>
        <div className={styles.imgWrap}>
          <SafeImage
            src={image}
            alt={title}
            fill
            sizes="(max-width: 768px) 100vw, 420px"
            className={styles.img}
          />
        </div>
        <div className={styles.content}>
          <h2 className={styles.title}>{title}</h2>
          <p className={styles.desc}>{description}</p>
          <div className={styles.pointsGrid}>
            <ul className={styles.list}>
              {col1.map((p) => (
                <li key={p} className={styles.point}>
                  <span className={styles.bullet}>•</span>
                  {p}
                </li>
              ))}
            </ul>
            <ul className={styles.list}>
              {col2.map((p) => (
                <li key={p} className={styles.point}>
                  <span className={styles.bullet}>•</span>
                  {p}
                </li>
              ))}
            </ul>
          </div>
        </div>
      </div>
    </section>
  );
}
