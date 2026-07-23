import Image from "next/image";
import ReadMoreHtml from "../common/ReadMoreHtml";
import styles from "./WhatWeDoSection.module.css";

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export default function WhatWeDoSection({ data }: { data: any }) {
  if (!data) return null;

  const { heading, image, image_alt, body } = data.content;

  return (
    <section className={styles.section}>
      <div className={styles.inner}>
        <div className={styles.textCol}>
          {heading && <h2 className={styles.heading}>{heading}</h2>}
          {body && <ReadMoreHtml html={body} className={styles.bodyContent} />}
        </div>
        {image && (
          <div className={styles.imageCol}>
            <div className={styles.imageWrap}>
              <Image src={image} alt={image_alt ?? ""} fill sizes="(max-width: 768px) 100vw, 45vw" className={styles.image} />
            </div>
          </div>
        )}
      </div>
    </section>
  );
}
