"use client";

import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay } from "swiper/modules";
import "swiper/css";
import Image from "next/image";
import styles from "./StateWaterfallGallery.module.css";

interface WaterfallItem { label: string; image: string | null; }

interface Props {
  title: string;
  items: WaterfallItem[];
}

export default function StateWaterfallGallery({ title, items }: Props) {
  if (!items || items.length === 0) return null;

  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>

      <noscript>
        <div className={styles.nojs}>
          {items.map((item, i) => (
            <div key={i} className={styles.circleWrap}>
              <div className={styles.circle}>
                {item.image
                  ? <img src={item.image} alt={item.label} className={styles.img} />
                  : <div className={styles.imgPlaceholder} />}
              </div>
              <p className={styles.label}>{item.label}</p>
            </div>
          ))}
        </div>
      </noscript>

      <Swiper
        modules={[Autoplay]}
        autoplay={{ delay: 2800, disableOnInteraction: false }}
        breakpoints={{
          0:    { slidesPerView: 2.2, spaceBetween: 14 },
          480:  { slidesPerView: 3.2, spaceBetween: 16 },
          640:  { slidesPerView: 4,   spaceBetween: 18 },
          900:  { slidesPerView: 5,   spaceBetween: 20 },
          1200: { slidesPerView: 6,   spaceBetween: 20 },
        }}
        loop={items.length > 5}
        className={styles.swiper}
      >
        {items.map((item, i) => (
          <SwiperSlide key={i} className={styles.slide}>
            <div className={styles.circleWrap}>
              <div className={styles.circle}>
                {item.image
                  ? <Image
                      src={item.image}
                      alt={item.label}
                      fill
                      sizes="150px"
                      className={styles.img}
                      priority={i < 3}
                    />
                  : <div className={styles.imgPlaceholder} />}
              </div>
              <p className={styles.label}>{item.label}</p>
            </div>
          </SwiperSlide>
        ))}
      </Swiper>
    </section>
  );
}
