"use client";

import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay } from "swiper/modules";
import "swiper/css";
import styles from "./page.module.css";

interface WaterfallItem { label: string; image: string | null; }

interface Props {
  title: string;
  items: WaterfallItem[];
}

export default function WaterfallGallery({ title, items }: Props) {
  return (
    <section className={styles.waterfallSection}>
      <h2 className={styles.sectionHeading}>{title}</h2>

      {/* No-JS fallback */}
      <noscript>
        <div className={styles.waterfallNojs}>
          {items.map((item, i) => (
            <div key={i} className={styles.waterfallCircleWrap}>
              <div className={styles.waterfallCircle}>
                {item.image
                  ? <img src={item.image} alt={item.label} className={styles.waterfallImg} />
                  : <div className={styles.waterfallImgPlaceholder} />}
              </div>
              <p className={styles.waterfallLabel}>{item.label}</p>
            </div>
          ))}
        </div>
      </noscript>

      <Swiper
        modules={[Autoplay]}
        autoplay={{ delay: 2800, disableOnInteraction: false }}
        breakpoints={{
          0:   { slidesPerView: 2.2, spaceBetween: 14 },
          480: { slidesPerView: 3.2, spaceBetween: 16 },
          640: { slidesPerView: 4,   spaceBetween: 18 },
          900: { slidesPerView: 5,   spaceBetween: 20 },
          1200:{ slidesPerView: 6,   spaceBetween: 20 },
        }}
        loop={items.length > 5}
        className={styles.waterfallSwiper}
      >
        {items.map((item, i) => (
          <SwiperSlide key={i} className={styles.waterfallSlide}>
            <div className={styles.waterfallCircleWrap}>
              <div className={styles.waterfallCircle}>
                {item.image
                  ? <img src={item.image} alt={item.label} className={styles.waterfallImg} />
                  : <div className={styles.waterfallImgPlaceholder} />}
              </div>
              <p className={styles.waterfallLabel}>{item.label}</p>
            </div>
          </SwiperSlide>
        ))}
      </Swiper>
    </section>
  );
}
