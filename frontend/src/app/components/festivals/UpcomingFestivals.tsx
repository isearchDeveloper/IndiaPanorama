"use client";

import { Swiper, SwiperSlide } from "swiper/react";
import { Navigation, Pagination, A11y } from "swiper/modules";
import Link from "next/link";
import styles from "./UpcomingFestivals.module.css";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";

const FALLBACK = "/images/about-banner-pages.jpg";

type Festival = {
  slug: string; name: string; image: string; bannerImage?: string;
  description: string; tagline?: string; location: string; duration: string;
  bestTime: string; stateName?: string; categoryLabel?: string;
  month?: string; location_text?: string; month_text?: string;
};

interface Props {
  festivals: Festival[];
  heading?: string;
}

export default function UpcomingFestivals({ festivals, heading }: Props) {
  if (!festivals.length) return null;

  return (
    <section className={styles.section}>
      {heading && <h2 className={styles.heading}>{heading}</h2>}
      <div className={styles.sliderWrap}>
        <Swiper
          modules={[Pagination, A11y]}
          spaceBetween={20}
          slidesPerView={3}
         
          pagination={{ clickable: true, dynamicBullets: true }}
          breakpoints={{
            0: { slidesPerView: 1, spaceBetween: 12 },
            640: { slidesPerView: 2, spaceBetween: 16 },
            1024: { slidesPerView: 3, spaceBetween: 20 },
          }}
          className={styles.swiper}
        >
          {festivals.map((festival) => (
            <SwiperSlide key={festival.slug} className={styles.slide}>
              <Link href={`/festivals/${festival.slug}`} className={styles.card}>
                <div className={styles.imgWrap}>
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img
                    src={festival.image || FALLBACK}
                    alt={festival.name}
                    className={styles.img}
                    onError={(e) => { e.currentTarget.src = FALLBACK; }}
                  />
                </div>
                <div className={styles.body}>
                  <h3 className={styles.name}>{festival.name}</h3>
                  <p className={styles.location}>{festival.location}</p>
                  <p className={styles.desc}>{festival.description.slice(0, 100)}…</p>
                </div>
              </Link>
            </SwiperSlide>
          ))}
        </Swiper>
      </div>
    </section>
  );
}

