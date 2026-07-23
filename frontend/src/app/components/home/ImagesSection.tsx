import Image from "next/image";
import type { HomeData } from "@/services/homeService";
import styles from "./ImagesSection.module.css";

interface Props {
  data: HomeData["promo_banner"] | null;
}

export default function ImagesSection({ data }: Props) {
  const image = data?.image || "/images/experience-india.jpg";
  const alt = data?.image_alt || "Experience India Banner";

  return (
    <section className={styles.section}>
      <div className={styles.container}>
        <div className={styles.imageWrapper}>
          <Image
            src={image}
            alt={alt}
            fill
            className={styles.bannerImage}
            sizes="100vw"
            priority
          />
          {/* <button className={styles.exploreBtn}>Explore with us</button> */}
        </div>
      </div>
    </section>
  );
}
