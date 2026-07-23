"use client";

import { useRef, useState } from "react";
import Link from "next/link";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay } from "swiper/modules";
import type { Swiper as SwiperType } from "swiper";
import type { ExpSignatureItem } from "@/data/experiences/rootPageData";
import Image from "next/image";
import "swiper/css";
import styles from "./SignatureExperiences.module.css";

interface Props {
  heading: string;
  items: ExpSignatureItem[];
  /** card link ka text — default "View Tours →" */
  linkLabel?: string;
  /** heading ke right me View All button */
  viewAllHref?: string;
}

const MAX_DOTS = 5;

export default function SignatureExperiences({
  heading,
  items,
  linkLabel = "View Tours →",
  viewAllHref,
}: Props) {
  const swiperRef = useRef<SwiperType | null>(null);
  const [activeIndex, setActiveIndex] = useState(0);

  if (!items.length) return null;

  // desktop pe 3 cards dikhte hain — sliding positions utni hi hoti hain
  const positions = Math.max(1, items.length - 2);
  const dotCount = Math.min(MAX_DOTS, positions);
  const activeDot =
    positions <= 1
      ? 0
      : Math.round(
          (Math.min(activeIndex, positions - 1) / (positions - 1)) * (dotCount - 1)
        );
  const dotToIndex = (dot: number) =>
    dotCount <= 1 ? 0 : Math.round((dot / (dotCount - 1)) * (positions - 1));

  return (
    <section className={styles.section}>
      <div className={styles.headerRow}>
        <h2 className={styles.heading}>{heading}</h2>
        {viewAllHref && (
          <Link href={viewAllHref} className={styles.viewAll}>View All</Link>
        )}
      </div>

      <Swiper
        modules={[Autoplay]}
        spaceBetween={18}
        slidesPerView={1}
        breakpoints={{
          640: { slidesPerView: 2 },
          1024: { slidesPerView: 3 },
        }}
        autoplay={{ delay: 4000, disableOnInteraction: false }}
        loop={false}
        onSwiper={(s) => (swiperRef.current = s)}
        onSlideChange={(s) => setActiveIndex(s.activeIndex)}
        className={styles.swiper}
      >
        {items.map((item) => (
          <SwiperSlide key={item.slug} className={styles.slide}>
            {/* pura card clickable */}
            <Link href={item.href} className={styles.card}>
              <div className={styles.imgWrap}>
                <Image
                  src={item.image}
                  alt={item.image_alt || ""}
                  fill
                  sizes="(max-width: 640px) 100vw, 33vw"
                  className={styles.img}
                />
                {item.toursCount && <span className={styles.badge}>{item.toursCount}</span>}
              </div>
              <div className={styles.body}>
                <h3 className={styles.name}>{item.title}</h3>
                {item.description && <p className={styles.desc}>{item.description}</p>}
                {item.popularTag && (
                  <p className={styles.popular}>
                    <strong>Popular:</strong> {item.popularTag}
                  </p>
                )}
                <span className={styles.viewTours}>{linkLabel}</span>
              </div>
            </Link>
          </SwiperSlide>
        ))}
      </Swiper>

      {dotCount > 1 && (
        <div className={styles.dots}>
          {Array.from({ length: dotCount }).map((_, dot) => (
            <button
              key={dot}
              type="button"
              aria-label={`Go to slide group ${dot + 1}`}
              className={`${styles.dot} ${dot === activeDot ? styles.active : ""}`}
              onClick={() => swiperRef.current?.slideTo(dotToIndex(dot))}
            />
          ))}
        </div>
      )}
    </section>
  );
}
