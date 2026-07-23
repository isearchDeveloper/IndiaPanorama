import { contentSection } from "./indiaPageData";
import styles from "./IndiaContentSection.module.css";

export default function IndiaContentSection() {
  return (
    <div className={styles.section}>
      <h2 className={styles.heading}>{contentSection.heading}</h2>
      <div className={styles.body}>
        {contentSection.paragraphs.map((para, i) => (
          <p key={i} className={styles.para}>{para}</p>
        ))}
      </div>
    </div>
  );
}
