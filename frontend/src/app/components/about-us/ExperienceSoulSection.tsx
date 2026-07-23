import Image from "next/image";
import Link from "next/link";
import styles from "./ExperienceSoulSection.module.css";

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export default function ExperienceSoulSection({ data }: { data: any }) {
  if (!data) return null;

  const { heading, subheading, button_label, button_url, image, image_alt } = data.content;

  return (
    <section className={styles.section}>
      <div className={styles.bgWrap}>
        <Image src={image ?? "/images/about-banner-pages.jpg"} alt={image_alt ?? ""} fill priority sizes="100vw" className={styles.bgImg} />
        <div className={styles.overlay} />
      </div>
      <div className={styles.contentBox}>
        <div className={styles.logoRow}>
          <div className={styles.elephantWrap}>
            <Image src="/images/indian-panorama-logo.png" alt="Indian Panorama Logo" width={180} height={52} className={styles.logoImg} />
          </div>
        </div>
        <p className={styles.unit}>(A Unit of Cholan Tours Pvt Ltd)</p>
        {heading && <h2 className={styles.heading}>{heading}</h2>}
        {subheading && <p className={styles.subtitle}>{subheading}</p>}
        {button_label && <Link href={button_url ?? "/"} className={styles.btn}>{button_label}</Link>}
      </div>
    </section>
  );
}
