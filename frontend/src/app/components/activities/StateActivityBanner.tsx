import Image from "next/image";
import styles from "./StateActivityBanner.module.css";

interface Props {
  title: string;
  subtitle: string;
  bgImage: string;
}

export default function StateActivityBanner({ title, subtitle, bgImage }: Props) {
  // text hidden hai — props "used" rakhe hain taaki lint error na aaye
  void title; void subtitle;
  return (
    <section className={styles.banner}>
      <Image
        src={bgImage}
        alt=""
        role="presentation"
        fill
        priority
        sizes="100vw"
        className={styles.bgImg}
      />
      {/* overlay + text hidden — uncomment to restore
      <div className={styles.overlay} />
      <div className={styles.content}>
        <h1 className={styles.title}>{title}</h1>
        <p className={styles.sub}>Indian Panorama</p>
      </div>
      */}
    </section>
  );
}
