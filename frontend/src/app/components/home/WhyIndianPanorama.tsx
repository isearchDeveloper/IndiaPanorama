import Image from "next/image";
import type { HomeData } from "@/services/homeService";
import styles from "./WhyIndianPanorama.module.css";

interface Props {
  data: HomeData["why_indian_panorama"] | null;
}

export default function WhyIndianPanorama({ data }: Props) {
  if (!data) return null;

  return (
    <section className={styles.section}>
      <Image
        src="/images/indian-essential-spices-terracotta-pots-arranged-textured-background-selective-focus.png"
        alt="Indian Spices Decoration"
        width={300}
        height={300}
        className={styles.topRightImage}
        aria-hidden="true"
      />

      <div className={styles.headerContainer}>
        <div className={styles.textContent}>
          <h2 className={styles.heading}>{data.title}</h2>
          <p className={styles.paragraph}>{data.subtitle}</p>
        </div>
      </div>

      <div className={styles.imageContainer}>
        <div className={styles.timelineWrapper}>
          <Image
            src={data.image || "/images/graph 1.png"}
            alt={data.image_alt || "Why Indian Panorama Timeline Graph"}
            width={1200}
            height={400}
            style={{ width: "100%", height: "auto", display: "block" }}
          />
        </div>
      </div>
    </section>
  );
}
