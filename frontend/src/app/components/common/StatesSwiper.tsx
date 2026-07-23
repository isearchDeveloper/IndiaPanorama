"use client";

import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import Link from "next/link";
import styles from "./StatesSwiper.module.css";

export type StatesSwiperItem = {
  id: string | number;
  name: string;
  image: string | null;
  href: string;
  toursCount?: number | null;
  description?: string | null;
  popular?: string[];
};

type Props = {
  title: string;
  viewAllHref?: string;
  items: StatesSwiperItem[];
  ctaLabel?: string;
};

export default function StatesSwiper({ title, viewAllHref, items, ctaLabel = "View Tours →" }: Props) {
  if (!items.length) return null;
  const shouldLoop = items.length > 3;

  return (
    <section className={styles.section}>
      <div className={styles.header}>
        <h2 className={styles.heading}>{title}</h2>
        {viewAllHref && (
          <Link href={viewAllHref} className={styles.viewAll}>View All</Link>
        )}
      </div>

      <Swiper
        modules={[Autoplay, Pagination]}
        className={styles.swiper}
        spaceBetween={20}
        loop={shouldLoop}
        pagination={{ clickable: true, dynamicBullets: true }}
        autoplay={shouldLoop ? { delay: 3000, disableOnInteraction: false, pauseOnMouseEnter: true } : false}
        breakpoints={{
          0:   { slidesPerView: 1.15, spaceBetween: 14 },
          480: { slidesPerView: 1.8,  spaceBetween: 16 },
          640: { slidesPerView: 2.4,  spaceBetween: 18 },
          900: { slidesPerView: 3,    spaceBetween: 20 },
        }}
      >
        {items.map((item, i) => (
          <SwiperSlide key={item.id} className={styles.slide}>
            <StateCard item={item} eager={i < 3} ctaLabel={ctaLabel} />
          </SwiperSlide>
        ))}
      </Swiper>

      <noscript>
        <div className={styles.noscriptGrid}>
          {items.map((item) => (
            <StateCard key={item.id} item={item} eager={false} ctaLabel={ctaLabel} />
          ))}
        </div>
      </noscript>
    </section>
  );
}

function StateCard({ item, eager, ctaLabel }: { item: StatesSwiperItem; eager: boolean; ctaLabel: string }) {
  return (
    <Link href={item.href} className={styles.card}>
      <div className={styles.imgWrap}>
        {item.image ? (
          // eslint-disable-next-line @next/next/no-img-element
          <img
            src={item.image}
            alt={item.name}
            className={styles.img}
            loading={eager ? "eager" : "lazy"}
            decoding="async"
          />
        ) : (
          <div className={styles.imgPlaceholder} />
        )}
        {item.toursCount != null && (
          <span className={styles.badge}>
            {String(item.toursCount).padStart(2, "0")} Tours
          </span>
        )}
      </div>
      <div className={styles.body}>
        <p className={styles.name}>{item.name}</p>
        {item.description && (
          <p className={styles.desc}>{item.description}</p>
        )}
        {item.popular && item.popular.length > 0 && (
          <p className={styles.popular}>
            <strong>Popular: </strong>{item.popular.join(" | ")}
          </p>
        )}
        <span className={styles.cta}>{ctaLabel}</span>
      </div>
    </Link>
  );
}
