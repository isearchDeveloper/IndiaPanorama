import Image from "next/image";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import styles from "./StateActivityIntroAPI.module.css";

interface Props {
  shortDescription: string | null;
  aboutImage: string | null;
  aboutImageAlt: string | null;
}

export default function StateActivityIntroAPI({ shortDescription, aboutImage, aboutImageAlt }: Props) {
  return (
    <div className={styles.wrap}>
      <div className={styles.textBlock}>
        {shortDescription ? (
          <ReadMoreHtml html={shortDescription} className={styles.desc} />
        ) : null}
      </div>
      {aboutImage && (
        <div className={styles.imgBlock}>
          <Image
            src={aboutImage}
            alt={aboutImageAlt ?? ""}
            width={380}
            height={300}
            className={styles.illustration}
          />
        </div>
      )}
    </div>
  );
}
