import Image from "next/image";
import Link from "next/link";
import { tourCategories, sectionHeading } from "./indiaPageData";
import styles from "./IndiaTourCategories.module.css";

export default function IndiaTourCategories() {
  return (
    <section className={styles.section}>
      <div className={styles.sectionHeader}>
        <h2 className={styles.heading}>{sectionHeading.title}</h2>
        <p className={styles.description}>{sectionHeading.description}</p>
      </div>

      <div className={styles.grid}>
        {tourCategories.map((cat) => (
          <Link key={cat.id} href={cat.url} className={styles.card} aria-label={cat.title}>
            <div className={styles.imgWrap}>
              <Image
                src={cat.image}
                alt={cat.title}
                fill
                sizes="(max-width: 576px) 100vw, (max-width: 1024px) 50vw, 33vw"
                className={styles.img}
              />
              <span className={styles.badge}>{cat.tourCount} Tours</span>
            </div>
            <div className={styles.cardBody}>
              <span className={styles.cardTitle}>{cat.title}</span>
            </div>
          </Link>
        ))}
      </div>
    </section>
  );
}
