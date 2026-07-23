"use client";

import { useRef } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import Link from "next/link";
import SafeImage from "@/app/components/common/SafeImage";
import styles from "./TourSlider.module.scss";

export interface TourSliderItem {
  slug: string;
  title: string;
  image: string;
  image_alt?: string;
  tours_count?: number;
  description?: string;
  popular_spots?: string;
}

interface Props {
  heading: string;
  viewAllHref: string;
  items: TourSliderItem[];
}

export default function TourSlider({ heading, viewAllHref, items }: Props) {
  const paginationRef = useRef<HTMLDivElement>(null);

  if (!items.length) return null;

  return (
    <section className={styles.section}>
      <div className={styles.header}>
        <h2 className={styles.heading}>{heading}</h2>
        <Link href={viewAllHref} className={styles.viewAll}>
          View All
        </Link>
      </div>

      <Swiper
        modules={[Autoplay, Pagination]}
        spaceBetween={16}
        loop={items.length > 3}
        autoplay={{ delay: 3400, disableOnInteraction: false, pauseOnMouseEnter: true }}
        pagination={{ clickable: true, el: paginationRef.current, dynamicBullets: true }}
        onSwiper={(swiper) => {
          if (paginationRef.current) {
            // @ts-expect-error swiper internal
            swiper.params.pagination.el = paginationRef.current;
            swiper.pagination.init();
            swiper.pagination.render();
            swiper.pagination.update();
          }
        }}
        breakpoints={{
          0:   { slidesPerView: 1.2, spaceBetween: 12 },
          480: { slidesPerView: 1.6, spaceBetween: 14 },
          640: { slidesPerView: 2,   spaceBetween: 16 },
          900: { slidesPerView: 3,   spaceBetween: 16 },
        }}
        className={styles.swiper}
      >
        {items.map((item) => (
          <SwiperSlide key={item.slug} className={styles.slide}>
            <div className={styles.card}>
              <div className={styles.imgWrap}>
                <SafeImage
                  src={item.image}
                  alt={item.image_alt || item.title}
                  fill
                  sizes="(max-width: 640px) 80vw, 32vw"
                  className={styles.img}
                />
                {item.tours_count !== undefined && (
                  <span className={styles.badge}>
                    {String(item.tours_count).padStart(2, "0")} Tours
                  </span>
                )}
              </div>
              <div className={styles.cardBody}>
                <h3 className={styles.cardTitle}>{item.title}</h3>
                {item.description && (
                  <p className={styles.cardDesc}>{item.description}</p>
                )}
                {item.popular_spots && (
                  <p className={styles.popular}>
                    <span className={styles.popularLabel}>Popular: </span>
                    {item.popular_spots}
                  </p>
                )}
                <Link href={`/${item.slug}/tour-packages`} className={styles.viewTours}>
                  View Tours →
                </Link>
              </div>
            </div>
          </SwiperSlide>
        ))}
      </Swiper>

      <div ref={paginationRef} className={styles.pagination} />
    </section>
  );
}
