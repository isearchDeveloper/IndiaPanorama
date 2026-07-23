"use client";

import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay } from "swiper/modules";
import "swiper/css";
import Image from "next/image";
import Link from "next/link";
import type { ActivityCityExperienceItem } from "@/services/activitiesService";
import styles from "./ActivityPopularCities.module.css";

interface Props {
  title: string;
  items: ActivityCityExperienceItem[];
}

export default function ActivityPopularCities({ title, items }: Props) {
  if (!items.length) return null;

  const shouldLoop = items.length > 4;

  return (
    <section className={styles.section}>
      <div className={styles.header}>
        <h2 className={styles.heading}>{title}</h2>
        <Link href="/activities" className={styles.viewAll}>View All</Link>
      </div>

      <Swiper
        modules={[Autoplay]}
        className={styles.swiper}
        spaceBetween={20}
        loop={shouldLoop}
        autoplay={shouldLoop ? { delay: 3000, disableOnInteraction: false, pauseOnMouseEnter: true } : false}
        breakpoints={{
          0:    { slidesPerView: 1.15, spaceBetween: 14 },
          480:  { slidesPerView: 1.8,  spaceBetween: 16 },
          640:  { slidesPerView: 2.4,  spaceBetween: 18 },
          900:  { slidesPerView: 3,    spaceBetween: 20 },
          1200: { slidesPerView: 3,    spaceBetween: 20 },
        }}
      >
        {items.map((c, i) => {
          const popular = c.popular_activities.slice(0, 3).join(", ");
          return (
            <SwiperSlide key={c.city_slug} className={styles.slide}>
              <Link href={`/${c.state_slug}/${c.city_slug}/activities`} className={styles.card}>
                <div className={styles.imgWrap}>
                  {c.image ? (
                    <Image
                      src={c.image}
                      alt={c.image_alt ?? c.city_name}
                      fill
                      sizes="(max-width: 640px) 100vw, (max-width: 900px) 50vw, 33vw"
                      className={styles.img}
                      priority={i < 3}
                    />
                  ) : (
                    <div className={styles.imgPlaceholder} />
                  )}
                  <span className={styles.badge}>{c.tours_count} Tours</span>
                </div>
                <div className={styles.body}>
                  <p className={styles.name}>{c.city_name}</p>
                  {c.description && (
                    <div
                      className={styles.desc}
                      dangerouslySetInnerHTML={{ __html: c.description }}
                    />
                  )}
                  {popular && (
                    <p className={styles.popular}>
                      <strong>Popular:</strong> {popular}
                    </p>
                  )}
                  <span className={styles.viewBtn}>View Tours →</span>
                </div>
              </Link>
            </SwiperSlide>
          );
        })}
      </Swiper>
    </section>
  );
}
