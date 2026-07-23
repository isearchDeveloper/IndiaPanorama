import ReadMoreHtml from "../common/ReadMoreHtml";
import styles from "./CityGuideAbout.module.css";


type CityGuideAboutProps = {
  title: string;
  content: string;
};

export default function CityGuideAbout({
  title,
  content,
}: CityGuideAboutProps) {
  return (
    <section className={styles.section}>
      <div className="container">
        <div className={styles.wrapper}>
          <div className={styles.content}>
            <h1>{title}</h1>

            <ReadMoreHtml html={content} />
          </div>
        </div>
      </div>
    </section>
  );
}