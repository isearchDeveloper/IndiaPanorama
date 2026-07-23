import ReadMoreHtml from "../../common/ReadMoreHtml";
import styles from "./FestivalDetailIntro.module.css";

interface Props {
  h1: string;
  shortDescription: string | null;
  introImage: string | null;
  introImageAlt: string | null;
  bannerTitle: string;
}

export default function FestivalDetailIntro({ h1, shortDescription, introImage, introImageAlt, bannerTitle }: Props) {
  return (
    <section className={styles.section}>
      <div className={styles.textCol}>
        <h1 className={styles.h1}>{h1}</h1>
        {shortDescription && (
          <ReadMoreHtml html={shortDescription} className={styles.desc} />
        )}
      </div>
      {introImage && (
        <div className={styles.imgCol}>
          <div className={styles.imgWrap}>
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img
              src={introImage}
              alt={introImageAlt ?? bannerTitle}
              className={styles.img}
              loading="lazy"
            />
          </div>
        </div>
      )}
    </section>
  );
}
