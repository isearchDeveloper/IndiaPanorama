"use client";

import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import Image from "next/image";
import Link from "next/link";
import styles from "./RelatedPackages.module.css";

const FALLBACK = "/images/about-banner-pages.jpg";

interface RelatedPkg {
  slug: string;
  title: string;
  image?: string | null;
  image_alt?: string | null;
  duration_days?: number | null;
  duration_nights?: number | null;
  location?: string;
}

interface Props {
  packages: RelatedPkg[];
  heading?: string;
}

export default function RelatedPackages({ packages, heading = "Similar Packages" }: Props) {
  if (!packages.length) return null;

  return (
    <section className={styles.section}>
      <div className={styles.inner}>
        <div className={styles.header}>
          <h2 className={styles.heading}>{heading}</h2>
        </div>

        <noscript>
          <div className={styles.noscriptGrid}>
            {packages.map((pkg) => (
              <Link key={pkg.slug} href={`/tour-packages/${pkg.slug}`} className={styles.card}>
                <div className={styles.imgWrap}>
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img src={pkg.image ?? FALLBACK} alt={pkg.image_alt ?? pkg.title} className={styles.img} />
                </div>
                <div className={styles.body}>
                  <h3 className={styles.title}>{pkg.title}</h3>
                  <div className={styles.footer}>
                    <span className={styles.duration}>
                      {pkg.duration_days ? `${pkg.duration_days} Days` : ""}
                      {pkg.duration_days && pkg.duration_nights ? " / " : ""}
                      {pkg.duration_nights ? `${pkg.duration_nights} Nights` : ""}
                    </span>
                    <span className={styles.bookBtn}>View Details</span>
                  </div>
                </div>
              </Link>
            ))}
          </div>
        </noscript>

        <Swiper
          modules={[Autoplay, Pagination]}
          spaceBetween={20}
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
            const days   = pkg.duration_days;
            const nights = pkg.duration_nights;
            return (
              <SwiperSlide key={pkg.slug} className={styles.slide}>
                <div className={styles.card}>
                  <div className={styles.imgWrap}>
                    <Image
                      src={pkg.image ?? FALLBACK}
                      alt={pkg.image_alt ?? pkg.title}
                      fill
                      sizes="(max-width: 640px) 100vw, (max-width: 900px) 50vw, 25vw"
                      className={styles.img}
                    />
                  </div>
                  <div className={styles.body}>
                    <h3 className={styles.title}>{pkg.title}</h3>
                    {pkg.location && <p className={styles.location}>{pkg.location}</p>}
                    <div className={styles.footer}>
                      {(days || nights) && (
                        <span className={styles.duration}>
                          {days ? `${days} Days` : ""}
                          {days && nights ? " / " : ""}
                          {nights ? `${nights} Nights` : ""}
                        </span>
                      )}
                      <Link href={`/tour-packages/${pkg.slug}`} className={styles.bookBtn}>
                        View Details
                      </Link>
                    </div>
                  </div>
                </div>
              </SwiperSlide>
            );
          })}
        </Swiper>

        <div className={styles.pagination} />
      </div>
    </section>
  );
}
