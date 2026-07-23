"use client";

import { useRef } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import Image from "next/image";
import Link from "next/link";
import type { ActivityPackageItem } from "@/services/activitiesService";
import styles from "./ActivityPopularInIndia.module.css";

interface Props {
  title: string;
  items: ActivityPackageItem[];
}

export default function ActivityPopularInIndia({ title, items }: Props) {
  const paginationRef = useRef<HTMLDivElement>(null);
  if (!items.length) return null;

  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>
      {/* No-JS fallback: visible only when JS disabled */}
      <noscript>
        <div className={styles.noscriptGrid}>
          {items.map((item) => (
            <Link key={item.slug} href={`/tour-packages/${item.slug}`} className={styles.card}>
              <div className={styles.imgWrap}>
                {item.image ? (
                  <Image src={item.image} alt={item.image_alt ?? item.title} fill sizes="25vw" className={styles.img} />
                ) : (
                  <div className={styles.imgPlaceholder} />
                )}
              </div>
              <div className={styles.body}>
                <p className={styles.label}>{item.title}</p>
                <p className={styles.meta}>{item.duration_days} Days / {item.duration_nights} Nights · {item.location}</p>
                <span className={styles.bookBtn}>Book Now</span>
              </div>
            </Link>
          ))}
        </div>
      </noscript>

      {/* JS swiper */}
      <Swiper
        modules={[Autoplay, Pagination]}
        spaceBetween={20}
        loop={items.length > 4}
        autoplay={{ delay: 3000, disableOnInteraction: false, pauseOnMouseEnter: true }}
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
          0:    { slidesPerView: 1.1, spaceBetween: 12 },
          480:  { slidesPerView: 1.3, spaceBetween: 14 },
          640:  { slidesPerView: 2,   spaceBetween: 16 },
          900:  { slidesPerView: 3,   spaceBetween: 18 },
          1200: { slidesPerView: 4,   spaceBetween: 20 },
        }}
        className={styles.swiper}
      >
        {items.map((item) => (
          <SwiperSlide key={item.slug} className={styles.slide}>
            <Link href={`/tour-packages/${item.slug}`} className={styles.card}>
              <div className={styles.imgWrap}>
                {item.image ? (
                  <Image
                    src={item.image}
                    alt={item.image_alt ?? item.title}
                    fill
                    sizes="(max-width:640px) 88vw, (max-width:900px) 46vw, 25vw"
                    className={styles.img}
                  />
                ) : (
                  <div className={styles.imgPlaceholder} />
                )}
              </div>
              <div className={styles.body}>
                <p className={styles.label}>{item.title}</p>
                <p className={styles.meta}>{item.duration_days} Days / {item.duration_nights} Nights · {item.location}</p>
                <span className={styles.bookBtn}>Book Now</span>
              </div>
            </Link>
          </SwiperSlide>
        ))}
      </Swiper>

      <div ref={paginationRef} className={styles.pagination} />
    </section>
  );
}
