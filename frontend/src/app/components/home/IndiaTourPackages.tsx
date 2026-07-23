"use client";

import Link from "next/link";
import SafeImage from "@/app/components/common/SafeImage";
import type { HomeData } from "@/services/homeService";
import styles from "./IndiaTourPackages.module.css";

interface Props {
  data: HomeData["india_tour_packages"] | null;
}

export default function IndiaTourPackages({ data }: Props) {
  if (!data) return null;

  return (
    <section className={styles.section}>
      <div className={styles.container}>

        <div className={styles.header}>
          <h2 className={styles.heading}>{data.title}</h2>
          <p className={styles.subtext}>{data.subtitle}</p>
        </div>

        <div className={styles.cardList}>
          {data.regions.map((region, index) => (
            <div
              key={region.id}
              className={`${styles.card} ${index % 2 !== 0 ? styles.reverse : ""}`}
            >
              <div className={styles.textSide}>
                <span className={styles.number}>{String(index + 1).padStart(2, "0")}</span>
                <h3 className={styles.title}>{region.title}</h3>
                {region.description && (
                  <div
                    className={`${styles.description} cms-content`}
                    dangerouslySetInnerHTML={{ __html: region.description }}
                  />
                )}
                <Link href={`/${region.slug}`} className={styles.exploreBtn}>
                  {data.button_text || "Explore Now"} <span>→</span>
                </Link>
              </div>

              <div className={styles.imageSide}>
                <SafeImage
                  src={region.banner}
                  alt={region.banner_alt}
                  fill
                  className="object-cover"
                  sizes="(max-width: 768px) 100vw, (max-width: 1400px) 50vw, 700px"
                  quality={90}
                  priority={false}
                />
              </div>
            </div>
          ))}
        </div>

      </div>
    </section>
  );
}
