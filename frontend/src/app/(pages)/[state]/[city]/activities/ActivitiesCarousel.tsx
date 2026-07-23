"use client";

import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import Image from "next/image";
import type { CityActivityItem } from "@/services/activitiesService";
import styles from "./page.module.css";

interface Props {
  title: string;
  subTitle: string | null;
  stateSlug: string;
  citySlug: string;
  items: CityActivityItem[];
}


export default function ActivitiesCarousel({ title, subTitle, items }: Props) {
  const validItems = items;

  // Agar items hi nahi hain tabhi return null
  if (!validItems.length) return null;

  return (
    <div className={styles.carouselLayout}>

      {/* Left panel — fixed width */}
      <div className={styles.carouselLeft}>
        <h2 className={styles.carouselHeading}>{title}</h2>
        {subTitle && <p className={styles.carouselSubtitle}>{subTitle}</p>}
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          src="/images/sidelogoofactivities.png"
          alt=""
          aria-hidden="true"
          className={styles.carouselDecor}
        />
      </div>

      {/* Right panel — takes remaining width, cards always same size */}
      <div className={styles.carouselRight}>
        <Swiper
          modules={[Autoplay, Pagination]}
          autoplay={{ delay: 3000, disableOnInteraction: false, pauseOnMouseEnter: true }}
          loop={false}
          spaceBetween={16}
          slidesPerView="auto"
          pagination={validItems.length > 3 ? { clickable: true, dynamicBullets: true } : false}
          className={styles.carouselSwiper}
        >
          {validItems.map((item) => (
            <SwiperSlide key={item.slug} className={styles.carouselSlide}>
              <div className={styles.carouselCard}>
                <div className={styles.carouselImgWrap}>
                  <Image
                    src={item.image!}
                    alt={item.image_alt ?? item.name}
                    fill
                    sizes="220px"
                    className={styles.carouselImg}
                  />
                </div>
                <p className={styles.carouselName}>{item.name}</p>
              </div>
            </SwiperSlide>
          ))}
        </Swiper>
      </div>

    </div>
  );
}
