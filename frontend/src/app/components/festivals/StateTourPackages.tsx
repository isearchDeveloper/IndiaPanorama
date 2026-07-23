"use client";

import { Swiper, SwiperSlide } from "swiper/react";
import { Navigation, Pagination, A11y } from "swiper/modules";
import Link from "next/link";
import styles from "./StateTourPackages.module.css";

import Image from "next/image";
const FALLBACK = "/images/about-banner-pages.jpg";

import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";

export interface TourPackage {
  slug: string;
  name: string;
  image: string;
  days: string;
  nights: string;
  description: string;
  href: string;
}

interface Props {
  packages: TourPackage[];
  heading?: string;
}

export default function StateTourPackages({ packages, heading }: Props) {
  if (!packages.length) return null;
  return (
    <section className={styles.section}>
      <div className={styles.inner}>
        {heading && <h2 className={styles.heading}>{heading}</h2>}
        <div className={styles.sliderWrap}>
          <Swiper
            modules={[Navigation, Pagination, A11y]}
            spaceBetween={20}
            slidesPerView={3}
            navigation
            pagination={{ clickable: true, dynamicBullets: true }}
            breakpoints={{
              0: { slidesPerView: 1, spaceBetween: 12 },
              640: { slidesPerView: 2, spaceBetween: 16 },
              1024: { slidesPerView: 3, spaceBetween: 20 },
            }}
            className={styles.swiper}
          >
            {packages.map((pkg) => (
              <SwiperSlide key={pkg.slug} className={styles.slide}>
                <Link href={pkg.href} className={styles.card}>
                  <div className={styles.imgWrap}>
                    <Image
                      src={pkg.image || FALLBACK}
                      alt={pkg.name}
                      fill
                      sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
                      className={styles.img}
                    />
                    <span className={styles.tag}>Festival</span>
                  </div>
                  <div className={styles.body}>
                    <div className={styles.titleRow}>
                      <h3 className={styles.name}>{pkg.name}</h3>
                      <span className={styles.duration}>{pkg.days} / {pkg.nights}</span>
                    </div>
                    <div className={styles.footer}>
                      <span className={styles.cta}>View Details</span>
                    </div>
                  </div>
                </Link>
              </SwiperSlide>
            ))}
          </Swiper>
        </div>
      </div>
    </section>
  );
}
