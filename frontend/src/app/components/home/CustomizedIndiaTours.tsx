"use client";

import Image from "next/image";
import Link from "next/link";
import { useRef, useState } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay } from "swiper/modules";
import "swiper/css";
import type { HomeData } from "@/services/homeService";
import styles from "./CustomizedIndiaTours.module.css";
import type { Swiper as SwiperType } from "swiper";

const MAX_DOTS = 5;

interface Props {
  data: HomeData["customized_tours"] | null;
}

export default function CustomizedIndiaTours({ data }: Props) {
  const [activeIndex, setActiveIndex] = useState(0);
  const swiperRef = useRef<SwiperType | null>(null);

  if (!data || !data.packages.length) return null;

  const positions = Math.max(1, data.packages.length - 3);
  const dotCount = Math.min(MAX_DOTS, positions);
  const activeDot = positions <= 1
    ? 0
    : Math.round((Math.min(activeIndex, positions - 1) / (positions - 1)) * (dotCount - 1));
  const dotToIndex = (dot: number) =>
    dotCount <= 1 ? 0 : Math.round((dot / (dotCount - 1)) * (positions - 1));

  return (
    <section className={styles.section}>
      <div className={styles.container}>

        <div className={styles.header}>
          <div className={styles.headerLeft}>
            <h2 className={styles.heading}>{data.title}</h2>
          </div>
          <div className={styles.headerRight}>
            <p className={styles.subtext}>{data.subtitle}</p>
            <Link href="/tour-packages" className={styles.viewAllLink}>
              View All Packages &rarr;
            </Link>
          </div>
        </div>

        <noscript>
          <div className="noscript-grid">
            {data.packages.map((pkg) => (
              <Link key={pkg.id} href={`/tour-packages/${pkg.slug}`} className={styles.card}>
                <div className={styles.cardImage} style={{ position: "relative", height: 180 }}>
                  {pkg.image ? (
                    <Image src={pkg.image} alt={pkg.image_alt || pkg.title} fill className="object-cover" sizes="220px" />
                  ) : (
                    <div style={{ width: "100%", height: "100%", backgroundColor: "#e5e7eb" }} />
                  )}
                </div>
                <div className={styles.cardBody}>
                  <p className={styles.cardTitle}>{pkg.title.slice(0, 50)}{pkg.title.length > 50 ? "..." : ""}</p>
                  <p className={styles.cardMeta}>{pkg.duration_days} Days | {pkg.duration_nights} Nights</p>
                </div>
              </Link>
            ))}
          </div>
        </noscript>

        <div className={styles.carouselWrapper}>
          <Swiper
            modules={[Autoplay]}
            autoplay={{ delay: 3500, disableOnInteraction: false }}
            spaceBetween={16}
            slidesPerView={1}
            breakpoints={{
              480: { slidesPerView: 2 },
              768: { slidesPerView: 3 },
              1100: { slidesPerView: 4 },
            }}
            onSwiper={(s) => { swiperRef.current = s; }}
            onSlideChange={(s) => setActiveIndex(s.activeIndex)}
            className={styles.swiperTrack}
          >
            {data.packages.map((pkg) => (
              <SwiperSlide key={pkg.id}>
                <Link href={`/tour-packages/${pkg.slug}`} className={styles.card}>
                  <div className={styles.cardImage}>
                    {pkg.image ? (
                      <Image
                        src={pkg.image}
                        alt={pkg.image_alt || pkg.title}
                        fill
                        className="object-cover"
                        sizes="(max-width: 768px) 50vw, 25vw"
                      />
                    ) : (
                      <div style={{ width: "100%", height: "100%", backgroundColor: "#e5e7eb" }} />
                    )}
                  </div>
                  <div className={styles.cardBody}>
                    <p className={styles.cardTitle}>{pkg.title.slice(0, 50)}{pkg.title.length > 50 ? "..." : ""}</p>
                    <p className={styles.cardMeta}>
                      {pkg.duration_days} Days | {pkg.duration_nights} Nights
                    </p>
                  </div>
                </Link>
              </SwiperSlide>
            ))}
          </Swiper>
        </div>

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

      <div className={styles.elephantDecor}>
        <Image
          src="/images/colorful-decorated-elephants-thailand-white-background.jpg"
          alt="Elephant Decoration"
          width={180}
          height={200}
          className="object-contain"
          style={{ width: "auto", height: "auto" }}
        />
      </div>
    </section>
  );
}
