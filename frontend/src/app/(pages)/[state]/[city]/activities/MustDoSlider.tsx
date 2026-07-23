"use client";

import { useRef } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Pagination, Autoplay } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import Image from "next/image";
import Link from "next/link";
import type { CityTopActivityItem } from "@/services/activitiesService";
import styles from "./page.module.css";

interface Props {
  title: string;
  stateSlug: string;
  citySlug: string;
  items: CityTopActivityItem[];
}

export default function MustDoSlider({ title, stateSlug, citySlug, items }: Props) {
  const paginationRef = useRef<HTMLDivElement>(null);

  return (
    <section>
      <div className={styles.mustDoHeader}>
        <h2 className={styles.sectionHeading}>{title}</h2>
        <Link href={`/${stateSlug}/${citySlug}/activities`} className={styles.mustDoViewAll}>
          View All
        </Link>
      </div>

      {/* No-JS fallback */}
      <noscript>
        <div className={styles.mustDoNojs}>
          {items.map((item) => (
            <a
              key={item.slug}
              href={`/${stateSlug}/${citySlug}/${item.slug}`}
              className={styles.mustDoCard}
            >
              <div className={styles.mustDoImgWrap}>
                {item.image
                  ? <img src={item.image} alt={item.image_alt ?? item.name} className={styles.mustDoImgEl} />
                  : <div className={styles.mustDoImgPlaceholder} />}
                {item.location_name && (
                  <span className={styles.mustDoBadge}>{item.location_name}</span>
                )}
              </div>
              <div className={styles.mustDoBody}>
                <p className={styles.mustDoName}>{item.name}</p>
                {item.description && (
                  <div className={styles.mustDoDesc} dangerouslySetInnerHTML={{ __html: item.description }} />
                )}
                <span className={styles.mustDoBtn}>View Tours →</span>
              </div>
            </a>
          ))}
        </div>
      </noscript>

      <Swiper
        modules={[Pagination, Autoplay]}
        autoplay={{ delay: 3000, disableOnInteraction: false, pauseOnMouseEnter: true }}
        loop={true}
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
          0:    { slidesPerView: 1.1, spaceBetween: 14 },
          480:  { slidesPerView: 1.5, spaceBetween: 16 },
          640:  { slidesPerView: 2,   spaceBetween: 18 },
          900:  { slidesPerView: 3,   spaceBetween: 20 },
          1200: { slidesPerView: 3,   spaceBetween: 22 },
        }}
      >
        {items.map((item) => (
          <SwiperSlide key={item.slug} style={{ height: "auto" }}>
            <Link
              href={`/${stateSlug}/${citySlug}/${item.slug}`}
              className={styles.mustDoCard}
            >
              <div className={styles.mustDoImgWrap}>
                {item.image ? (
                  <Image
                    src={item.image}
                    alt={item.image_alt ?? item.name}
                    fill
                    sizes="(max-width:640px) 88vw, (max-width:900px) 45vw, 30vw"
                    className={styles.mustDoImg}
                  />
                ) : (
                  <div className={styles.mustDoImgPlaceholder} />
                )}
                {item.location_name && (
                  <span className={styles.mustDoBadge}>{item.location_name}</span>
                )}
              </div>
              <div className={styles.mustDoBody}>
                <p className={styles.mustDoName}>{item.name}</p>
                {item.description && (
                  <div
                    className={styles.mustDoDesc}
                    dangerouslySetInnerHTML={{ __html: item.description }}
                  />
                )}
                <span className={styles.mustDoBtn}>View Tours →</span>
              </div>
            </Link>
          </SwiperSlide>
        ))}
      </Swiper>

      <div ref={paginationRef} className={styles.mustDoPagination} />
    </section>
  );
}
