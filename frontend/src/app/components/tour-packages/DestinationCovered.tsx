import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import styles from "./DestinationCovered.module.css";

interface Props {
  destinations: { title: string; highlights: string }[];
  description?: string | null;
}

export default function DestinationCovered({ destinations, description }: Props) {
  if (!destinations.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>Destination Covered</h2>
      {description && (
        <ReadMoreHtml html={description} className={styles.description} />
      )}
      <div className={styles.grid}>
        {destinations.map((dest) => (
          <div key={dest.title} className={styles.card}>
            <h3 className={styles.destName}>{dest.title}</h3>
            <p className={styles.highlights}>{dest.highlights}</p>
          </div>
        ))}
      </div>
    </section>
  );
}
