"use client";

import { useRef, useState } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay } from "swiper/modules";
import "swiper/css";
import type { Swiper as SwiperType } from "swiper";
import styles from "./DestinationCards.module.css";

import Image from "next/image";
const FALLBACK = "/images/about-banner-pages.jpg";
const MAX_DOTS = 5;

type CityGuideAttraction = { id: number; name: string; image: string; description: string; category: string; href?: string };

interface Props {
  heading: string;
  destinations: CityGuideAttraction[];
  viewAllHref?: string;
  showBadge?: boolean;
}

export default function DestinationCards({ heading, destinations, viewAllHref = "/tour-packages", showBadge = true }: Props) {
  const [activeIndex, setActiveIndex] = useState(0);
  const swiperRef = useRef<SwiperType | null>(null);

  if (!destinations.length) return null;

  // total slide positions, but never more than 5 dots — dots represent
  // proportional progress through the slider, not one dot per slide
  const positions = Math.max(1, destinations.length - 2);
  const dotCount = Math.min(MAX_DOTS, positions);
  const activeDot = positions <= 1
    ? 0
    : Math.round((Math.min(activeIndex, positions - 1) / (positions - 1)) * (dotCount - 1));
  const dotToIndex = (dot: number) =>
    dotCount <= 1 ? 0 : Math.round((dot / (dotCount - 1)) * (positions - 1));

  return (
    <div className={styles.wrapper}>
      <div className={styles.header}>
        <h2 className={styles.heading}>{heading}</h2>
        <a href={viewAllHref} className={styles.viewAll}>View All</a>
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
        {destinations.map((dest) => (
          <SwiperSlide key={dest.id}>
            {dest.href ? (
              <a href={dest.href} className={styles.card}>
                <div className={styles.imgWrap}>
                  <Image
                    src={dest.image || FALLBACK}
                    alt={dest.name}
                    fill
                    sizes="(max-width: 480px) 100vw, 33vw"
                    className={styles.img}
                  />
                  {showBadge && <span className={styles.badge}>{dest.category}</span>}
                </div>
                <div className={styles.body}>
                  <span className={styles.name}>{dest.name}</span>
                </div>
              </a>
            ) : (
              <div className={styles.card}>
                <div className={styles.imgWrap}>
                  <Image
                    src={dest.image || FALLBACK}
                    alt={dest.name}
                    fill
                    sizes="(max-width: 480px) 100vw, 33vw"
                    className={styles.img}
                  />
                  {showBadge && <span className={styles.badge}>{dest.category}</span>}
                </div>
                <div className={styles.body}>
                  <span className={styles.name}>{dest.name}</span>
                </div>
              </div>
            )}
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
  );
}
