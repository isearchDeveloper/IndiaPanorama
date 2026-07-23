"use client";

import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import Link from "next/link";
import Image from "next/image";
import styles from "./NearbySwiper.module.css";

const FALLBACK = "/images/about-banner-pages.jpg";

export type NearbySwiperItem = {
  id: number;
  name: string;
  image: string;
  description?: string;
  href: string;
};

type Props = {
  heading: string;
  items: NearbySwiperItem[];
};

export default function NearbySwiper({ heading, items }: Props) {
  if (!items.length) return null;
  const shouldLoop = items.length > 3;

  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{heading}</h2>

      <Swiper
        modules={[Autoplay, Pagination]}
        className={styles.swiper}
        loop={shouldLoop}
        pagination={{ clickable: true, dynamicBullets: true }}
        autoplay={shouldLoop ? { delay: 3000, disableOnInteraction: false, pauseOnMouseEnter: true } : false}
        breakpoints={{
          0:   { slidesPerView: 1.15, spaceBetween: 14 },
          480: { slidesPerView: 1.8,  spaceBetween: 16 },
          640: { slidesPerView: 2,    spaceBetween: 18 },
          900: { slidesPerView: 3,    spaceBetween: 20 },
        }}
      >
        {items.map((item, i) => (
          <SwiperSlide key={item.id} className={styles.slide}>
            <Link href={item.href} className={styles.card}>
              <div className={styles.imgWrap}>
                <Image
                  src={item.image || FALLBACK}
                  alt={item.name}
                  fill
                  priority={i < 3}
                  sizes="(max-width: 768px) 50vw, 33vw"
                  className={styles.img}
                />
                <span className={styles.arrow}>↗</span>
              </div>
              <div className={styles.body}>
                <h3 className={styles.name}>{item.name}</h3>
                {item.description && (
                  <div className={`${styles.desc} cms-content`} dangerouslySetInnerHTML={{ __html: item.description }} />
                )}
              </div>
            </Link>
          </SwiperSlide>
        ))}
      </Swiper>

      <noscript>
        <div className={styles.noscriptGrid}>
          {items.map((item) => (
            <Link key={item.id} href={item.href} className={styles.card}>
              <div className={styles.imgWrap}>
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img src={item.image || FALLBACK} alt={item.name} className={styles.img} loading="lazy" decoding="async" />
                <span className={styles.arrow}>↗</span>
              </div>
              <div className={styles.body}>
                <h3 className={styles.name}>{item.name}</h3>
                {item.description && (
                  <div className={`${styles.desc} cms-content`} dangerouslySetInnerHTML={{ __html: item.description }} />
                )}
              </div>
            </Link>
          ))}
        </div>
      </noscript>
    </section>
  );
}
