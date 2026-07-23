"use client";

import { useRef } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import Link from "next/link";
import SafeImage from "@/app/components/common/SafeImage";
type DestinationItem = { name: string; slug: string; image: string; image_alt?: string; tours_count?: number };
import styles from "./DestinationsSlider.module.scss";

interface Props {
  heading: string;
  destinations: DestinationItem[];
}

export default function DestinationsSlider({ heading, destinations }: Props) {
  const paginationRef = useRef<HTMLDivElement>(null);

  if (!destinations.length) return null;

  return (
    <section className={styles.section}>
      <div className={styles.header}>
        <h2 className={styles.heading}>{heading}</h2>
        <Link href="/tour-packages" className={styles.viewAll}>
          View All
        </Link>
      </div>

      <Swiper
        modules={[Autoplay, Pagination]}
        spaceBetween={16}
        loop={destinations.length > 3}
        autoplay={{ delay: 3200, disableOnInteraction: false, pauseOnMouseEnter: true }}
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
          480: { slidesPerView: 1.5, spaceBetween: 14 },
          640: { slidesPerView: 2,   spaceBetween: 16 },
          900: { slidesPerView: 3,   spaceBetween: 16 },
        }}
        className={styles.swiper}
      >
        {destinations.map((dest) => (
          <SwiperSlide key={dest.slug} className={styles.slide}>
            <Link href={`/${dest.slug}/tour-packages`} className={styles.card} aria-label={dest.name}>
              <div className={styles.imgWrap}>
                <SafeImage
                  src={dest.image}
                  alt={dest.image_alt || dest.name}
                  fill
                  sizes="(max-width: 640px) 80vw, 32vw"
                  className={styles.img}
                />
                <span className={styles.badge}>{String(dest.tours_count).padStart(2, "0")} Tours</span>
              </div>
              <div className={styles.cardBody}>
                <span className={styles.cardTitle}>{dest.name}</span>
                <span className={styles.viewTours}>View Tours →</span>
              </div>
            </Link>
          </SwiperSlide>
        ))}
      </Swiper>

      <div ref={paginationRef} className={styles.pagination} />
    </section>
  );
}
