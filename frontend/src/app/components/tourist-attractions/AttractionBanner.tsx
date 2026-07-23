import Image from "next/image";
import styles from "./AttractionBanner.module.css";

interface AttractionBannerProps {
  title: string;
  subtitle?: string;
  image: string;
}

export default function AttractionBanner({ title, subtitle, image }: AttractionBannerProps) {
  // text hidden hai — props "used" rakhe hain taaki lint error na aaye
  void title; void subtitle;
  return (
    <section className={styles.banner}>
      <Image
        src={image}
        alt=""
        role="presentation"
        fill
        priority
        sizes="100vw"
        className={styles.bgImage}
      />
      {/* overlay + text hidden — uncomment to restore
      <div className={styles.overlay} />
      <div className={styles.content}>
        <h1 className={styles.title}>{title}</h1>
        {subtitle && <p className={styles.subtitle}>{subtitle}</p>}
      </div>
      */}
    </section>
  );
}
