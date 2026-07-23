"use client";

import { useRef, useState } from "react";
import Image from "next/image";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay } from "swiper/modules";
import "swiper/css";
import type { Swiper as SwiperType } from "swiper";
import ReadMoreHtml from "../common/ReadMoreHtml";
import styles from "./FairsAndFestivals.module.css";

const FALLBACK = "/images/about-banner-pages.jpg";
const MAX_DOTS = 5;

export interface FestivalCard {
  id: number;
  name: string;
  image: string;
}

interface Props {
  heading: string;
  description?: string;
  festivalHeading?: string;
  festivals: FestivalCard[];
  viewAllHref?: string;
}

export default function FairsAndFestivals({
  heading,
  description,
  festivalHeading,
  festivals,
  viewAllHref = "/tour-packages",
}: Props) {
  const [activeIndex, setActiveIndex] = useState(0);
  const swiperRef = useRef<SwiperType | null>(null);

  if (!festivals || festivals.length === 0) return null;

  const positions = Math.max(1, festivals.length - 3);
  const dotCount = Math.min(MAX_DOTS, positions);
  const activeDot = positions <= 1
    ? 0
    : Math.round((Math.min(activeIndex, positions - 1) / (positions - 1)) * (dotCount - 1));
  const dotToIndex = (dot: number) =>
    dotCount <= 1 ? 0 : Math.round((dot / (dotCount - 1)) * (positions - 1));

  return (
    <div className={styles.wrapper}>
      <h2 className={styles.heading}>{heading}</h2>

      {description && <ReadMoreHtml html={description} className={styles.desc} />}

      {festivals.length > 0 && (
        <div className={styles.swiperSection}>
          <div className={styles.swiperHeader}>
            <h3 className={styles.swiperHeading}>
              {festivalHeading ?? `${heading} Festivals`}
            </h3>
            <a href={viewAllHref} className={styles.viewAll}>
              View All
            </a>
          </div>

          <Swiper
            modules={[Autoplay]}
            autoplay={{ delay: 3500, disableOnInteraction: false }}
            spaceBetween={16}
            slidesPerView={1}
            breakpoints={{
              480: { slidesPerView: 2 },
              768: { slidesPerView: 3 },
            }}
            onSwiper={(s) => { swiperRef.current = s; }}
            onSlideChange={(s) => setActiveIndex(s.activeIndex)}
            className={styles.swiper}
          >
            {festivals.map((f) => (
              <SwiperSlide key={f.id}>
                <div className={styles.card}>
                  <div className={styles.imgWrap}>
                    <Image
                      src={f.image || FALLBACK}
                      alt={f.name}
                      fill
                      sizes="(max-width: 480px) 100vw, 33vw"
                      className={styles.img}
                    />
                  </div>
                  <p className={styles.name}>{f.name}</p>
                </div>
              </SwiperSlide>
            ))}
          </Swiper>

          <div className={styles.dots}>
            {Array.from({ length: dotCount }).map((_, i) => (
              <button
                key={i}
                className={`${styles.dot} ${i === activeDot ? styles.active : ""}`}
                onClick={() => swiperRef.current?.slideTo(dotToIndex(i))}
                aria-label={`Page ${i + 1}`}
              />
            ))}
          </div>
        </div>
      )}
    </div>
  );
}
