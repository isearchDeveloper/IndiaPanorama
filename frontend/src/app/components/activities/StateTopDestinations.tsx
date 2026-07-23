"use client";

import { useRef } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Pagination, Autoplay } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import Image from "next/image";
import Link from "next/link";
import type { StateTopDestinationItem } from "@/services/activitiesService";
import styles from "./StateTopDestinations.module.css";

interface Props {
  title: string;
  stateSlug: string;
  items: StateTopDestinationItem[];
}

export default function StateTopDestinations({ title, stateSlug, items }: Props) {
  const paginationRef = useRef<HTMLDivElement>(null);
  if (!items.length) return null;

  return (
    <section className={styles.section}>
      <div className={styles.header}>
        <h2 className={styles.heading}>{title}</h2>
        <Link href={`/${stateSlug}/activities`} className={styles.viewAll}>View All</Link>
      </div>

      {/* No-JS fallback */}
      <noscript>
        <div className={styles.noscriptGrid}>
          {items.map((d) => (
            <Link key={d.city_slug} href={`/${stateSlug}/${d.city_slug}/activities`} className={styles.card}>
              <div className={styles.imgWrap}>
                {d.image ? <Image src={d.image} alt={d.image_alt ?? d.city_name} fill sizes="33vw" className={styles.img} /> : <div className={styles.imgPlaceholder} />}
                <span className={styles.badge}>{d.tours_count} Tours</span>
              </div>
              <div className={styles.body}>
                <p className={styles.cityName}>{d.city_name}</p>
                {d.popular_activities.length > 0 && (
                  <p className={styles.popular}><strong>Popular:</strong> {d.popular_activities.slice(0,2).join(", ")}</p>
                )}
                <span className={styles.viewBtn}>View Tours →</span>
              </div>
            </Link>
          ))}
        </div>
      </noscript>

      <Swiper
        modules={[Pagination, Autoplay]}
        autoplay={{ delay: 3000, disableOnInteraction: false, pauseOnMouseEnter: true }}
        loop={items.length > 3}
        spaceBetween={16}
        pagination={{ clickable: true, el: paginationRef.current, dynamicBullets: true }}
        onSwiper={(swiper) => {
          if (paginationRef.current) {
            // @ts-expect-error swiper pagination el reassign
            swiper.params.pagination.el = paginationRef.current;
            swiper.pagination.init();
            swiper.pagination.render();
            swiper.pagination.update();
          }
        }}
        breakpoints={{
          0:   { slidesPerView: 1.1 },
          480: { slidesPerView: 1.5 },
          640: { slidesPerView: 2 },
          900: { slidesPerView: 3 },
        }}
        className={styles.swiper}
      >
        {items.map((d) => (
          <SwiperSlide key={d.city_slug} className={styles.slide}>
            <Link href={`/${stateSlug}/${d.city_slug}/activities`} className={styles.card}>
              <div className={styles.imgWrap}>
                {d.image ? (
                  <Image src={d.image} alt={d.image_alt ?? d.city_name} fill sizes="(max-width:640px) 80vw, 33vw" className={styles.img} />
                ) : (
                  <div className={styles.imgPlaceholder} />
                )}
                <span className={styles.badge}>{d.tours_count} Tours</span>
              </div>
              <div className={styles.body}>
                <p className={styles.cityName}>{d.city_name}</p>
                <div
                  className={styles.desc}
                  dangerouslySetInnerHTML={{ __html: d.description }}
                />
                {d.popular_activities.length > 0 && (
                  <p className={styles.popular}><strong>Popular:</strong> {d.popular_activities.slice(0,2).join(", ")}</p>
                )}
                <span className={styles.viewBtn}>View Tours →</span>
              </div>
            </Link>
          </SwiperSlide>
        ))}
      </Swiper>

      <div ref={paginationRef} className={styles.pagination} />
    </section>
  );
}
