"use client";

import { useRef } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import Image from "next/image";
import Link from "next/link";
import type { TAAttractionItem } from "@/services/touristAttractions";
import styles from "./page.module.css";

interface Props {
  title: string;
  stateSlug: string;
  citySlug: string;
  items: TAAttractionItem[];
}

export default function AttractionsSlider({ title, stateSlug, citySlug, items }: Props) {
  const paginationRef = useRef<HTMLDivElement>(null);
  return (
    <section>
      <h2 className={styles.sectionHeading}>{title}</h2>

      {/* No-JS fallback */}
      <noscript>
        <div style={{ display: "grid", gridTemplateColumns: "repeat(3,1fr)", gap: 16 }}>
          {items.map((item) => (
            <Link key={item.slug} href={`/${stateSlug}/${citySlug}/${item.slug}`} className={styles.sliderCard}>
              <div className={styles.sliderImgWrap}>
                {item.image
                  ? <Image src={item.image} alt={item.image_alt ?? item.name} fill sizes="33vw" className={styles.sliderImg} />
                  : <div className={styles.sliderImgPlaceholder} />}
              </div>
              <div className={styles.sliderBody}>
                <p className={styles.sliderName}>{item.name}</p>
                <p className={styles.sliderDesc}>{item.description}</p>
                <span className={styles.sliderExplore}>Explore Now →</span>
              </div>
            </Link>
          ))}
        </div>
      </noscript>

      <Swiper
        modules={[Pagination]}
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
          0:   { slidesPerView: 1.1, spaceBetween: 12 },
          480: { slidesPerView: 1.5, spaceBetween: 14 },
          640: { slidesPerView: 2,   spaceBetween: 16 },
          900: { slidesPerView: 3,   spaceBetween: 16 },
        }}
      >
        {items.map((item) => (
          <SwiperSlide key={item.slug} style={{ height: "auto" }}>
            <Link href={`/${stateSlug}/${citySlug}/${item.slug}`} className={styles.sliderCard}>
              <div className={styles.sliderImgWrap}>
                {item.image
                  ? <Image src={item.image} alt={item.image_alt ?? item.name} fill sizes="(max-width:640px) 80vw, 33vw" className={styles.sliderImg} />
                  : <div className={styles.sliderImgPlaceholder} />}
              </div>
              <div className={styles.sliderBody}>
                <p className={styles.sliderName}>{item.name}</p>
                {item.description && <p className={styles.sliderDesc}>{item.description}</p>}
                <span className={styles.sliderExplore}>Explore Now →</span>
              </div>
            </Link>
          </SwiperSlide>
        ))}
      </Swiper>

      <div ref={paginationRef} className={styles.pagination} />
    </section>
  );
}
