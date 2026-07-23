import Image from "next/image";
import styles from "./StateActivityIntro.module.css";

interface Props {
  heading: string;
  description: string;
  illustration: string;
}

export default function StateActivityIntro({ heading, description, illustration }: Props) {
  return (
    <div className={styles.wrap}>
      <div className={styles.textBlock}>
        <h2 className={styles.heading}>{heading}</h2>
        <p className={styles.desc}>{description}</p>
      </div>
      <div className={styles.imgBlock}>
        <Image
          src={illustration}
          alt=""
          aria-hidden="true"
          width={160}
          height={120}
          className={styles.illustration}
        />
      </div>
    </div>
  );
}
