"use client";

import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import Link from "next/link";
import SafeImage from "@/app/components/common/SafeImage";
import styles from "./IndiaPopularPackages.module.css";

export interface PackageCard {
  id: number;
  title: string;
  image: string;
  image_alt?: string | null;
  duration_days?: number | null;
  duration_nights?: number | null;
  url?: string;
  slug?: string;
}

interface Props {
  heading?: string;
  subtext?: string;
  viewAllHref?: string;
  packages?: PackageCard[];
}

export default function IndiaPopularPackages({
  heading = "Popular Tour Packages",
  subtext = "India, being the seventh largest country globally, stands out as an extraordinary destination due to its diverse landscape and rich cultural heritage.",
  viewAllHref,
  packages = [],
}: Props) {
  if (packages.length === 0) return null;

  return (
    <section className={styles.section}>
      <div className={styles.inner}>

        <div className={styles.header}>
          <div className={styles.headerTop}>
            <h2 className={styles.heading}>{heading}</h2>
            {viewAllHref && (
              <Link href={viewAllHref} className={styles.viewAll}>View All</Link>
            )}
          </div>
          <p className={styles.subtext}>{subtext}</p>
        </div>

        <noscript>
          <div className={styles.noscriptGrid}>
            {packages.map((pkg) => {
              const href = { pathname: pkg.slug ?? `/tour-packages/${pkg.slug}`, query: {} };
              return (
                <Link key={pkg.id} href={href} className={styles.card}>
                  <div className={styles.imgWrap}>
                    <SafeImage src={pkg.image} alt={pkg.image_alt ?? pkg.title} fill sizes="25vw" className={styles.img} />
                  </div>
                  <div className={styles.body}>
                    <h3 className={styles.title}>{pkg.title}</h3>
                    <div className={styles.footer}>
                      <span className={styles.duration}>
                        {pkg.duration_days ? `${pkg.duration_days} Days` : ""}{pkg.duration_days && pkg.duration_nights ? " / " : ""}{pkg.duration_nights ? `${pkg.duration_nights} Nights` : ""}
                      </span>
                      <span className={styles.bookBtn}>View Details</span>
                    </div>
                  </div>
                </Link>
              );
            })}
          </div>
        </noscript>

        <Swiper
          modules={[Autoplay, Pagination]}
          spaceBetween={20}
          slidesPerView={4}
          loop={packages.length > 4}
          autoplay={{ delay: 3000, disableOnInteraction: false, pauseOnMouseEnter: true }}
          pagination={{ clickable: true, el: `.${styles.pagination}`, dynamicBullets: true }}
          breakpoints={{
            0:    { slidesPerView: 1.1, spaceBetween: 12 },
            480:  { slidesPerView: 1.3, spaceBetween: 14 },
            640:  { slidesPerView: 2,   spaceBetween: 16 },
            900:  { slidesPerView: 3,   spaceBetween: 18 },
            1200: { slidesPerView: 4,   spaceBetween: 20 },
          }}
          className={styles.swiper}
        >
          {packages.map((pkg) => {
            const href =(pkg.slug ? `/tour-packages/${pkg.slug}` : "#");
            const nights = pkg.duration_nights;
            const days = pkg.duration_days;
            return (
              <SwiperSlide key={pkg.id} className={styles.slide}>
                <Link href={href} className={styles.card}>
                  <div className={styles.imgWrap}>
                    <SafeImage
                      src={pkg.image}
                      alt={pkg.image_alt ?? pkg.title}
                      fill
                      sizes="(max-width: 640px) 88vw, (max-width: 900px) 46vw, 25vw"
                      className={styles.img}
                    />
                  </div>
                  <div className={styles.body}>
                    <div className={styles.titleRow}>
                      <h3 className={styles.title}>{pkg.title}</h3>
                    </div>
                    <div className={styles.footer}>
                      {(days || nights) && (
                        <span className={styles.duration}>
                          {days ? `${days} Days` : ""}{days && nights ? " / " : ""}{nights ? `${nights} Nights` : ""}
                        </span>
                      )}
                      <span className={styles.bookBtn}>
                      View Details
                      </span>
                    </div>
                  </div>
                </Link>
              </SwiperSlide>
            );
          })}
        </Swiper>

        <div className={styles.pagination} />

      </div>
    </section>
  );
}
