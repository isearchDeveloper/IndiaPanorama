"use client";

import { useRef } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import Link from "next/link";
import SafeImage from "@/app/components/common/SafeImage";
type FestivalItem = { name?: string; title?: string; slug: string; image: string; image_alt?: string };
import styles from "./FairsAndFestivals.module.scss";

interface Props {
  festivalsSectionTitle: string;
  festivalsSubTitle: string;
  sliderTitle: string;
  festivals: FestivalItem[];
}

export default function FairsAndFestivals({ festivalsSectionTitle, festivalsSubTitle, sliderTitle, festivals }: Props) {
  const paginationRef = useRef<HTMLDivElement>(null);

  if (!festivals || festivals.length === 0) return null;

  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{festivalsSectionTitle}</h2>
      {festivalsSubTitle && (
        <div className={`${styles.body} cms-content`} dangerouslySetInnerHTML={{ __html: festivalsSubTitle }} />
      )}

      {festivals.length > 0 && (
        <>
          <div className={styles.sliderHeader}>
            <h2 className={styles.sliderHeading}>{sliderTitle}</h2>
            <Link href="/india-festivals" className={styles.viewAll}>
              View All
            </Link>
          </div>

          <Swiper
            modules={[Autoplay, Pagination]}
            spaceBetween={20}
            loop={festivals.length > 4}
            autoplay={{ delay: 2800, disableOnInteraction: false, pauseOnMouseEnter: true }}
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
              0:    { slidesPerView: 1.2, spaceBetween: 12 },
              480:  { slidesPerView: 2,   spaceBetween: 14 },
              640:  { slidesPerView: 2,   spaceBetween: 16 },
              900:  { slidesPerView: 3,   spaceBetween: 18 },
              1200: { slidesPerView: 4,   spaceBetween: 10 },
            }}
            className={styles.swiper}
          >
            {festivals.map((fest) => (
              <SwiperSlide key={fest.slug} className={styles.slide}>
                <Link href={`/india-festivals/${fest.slug}`} className={styles.card} aria-label={fest.title ?? fest.name}>
                  <div className={styles.imgWrap}>
                    <SafeImage
                      src={fest.image}
                      alt={fest.image_alt || fest.title || fest.name || ""}
                      fill
                      sizes="(max-width: 480px) 80vw, (max-width: 900px) 45vw, 25vw"
                      className={styles.img}
                    />
                  </div>
                  <div className={styles.cardBody}>
                    <span className={styles.cardTitle}>{fest.title ?? fest.name}</span>
                  </div>
                </Link>
              </SwiperSlide>
            ))}
          </Swiper>

          <div ref={paginationRef} className={styles.pagination} />
        </>
      )}
    </section>
  );
}
