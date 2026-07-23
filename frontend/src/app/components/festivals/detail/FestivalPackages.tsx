"use client";

import { Swiper, SwiperSlide } from "swiper/react";
import { Pagination, Autoplay } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import Link from "next/link";
import styles from "./FestivalPackages.module.css";

const FALLBACK = "/images/about-banner-pages.jpg";

interface Item {
  title: string;
  slug: string;
  image: string;
  image_alt: string | null;
  duration_days: number;
  duration_nights: number;
  location: string;
}

interface Props {
  title: string;
  items: Item[];
}

export default function FestivalPackages({ title, items }: Props) {
  if (!items.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>

      <Swiper
        modules={[Pagination, Autoplay]}
        slidesPerView={3}
        spaceBetween={20}
        pagination={{ clickable: true, dynamicBullets: true }}
        autoplay={{ delay: 3000, disableOnInteraction: false }}
        loop={items.length > 3}
        className={styles.swiper}
        breakpoints={{
          0:   { slidesPerView: 1, spaceBetween: 12 },
          600: { slidesPerView: 2, spaceBetween: 16 },
          900: { slidesPerView: 3, spaceBetween: 20 },
        }}
      >
        {items.map((pkg, i) => (
          <SwiperSlide key={pkg.slug} className={styles.slide}>
            <PackageCard pkg={pkg} eager={i < 3} />
          </SwiperSlide>
        ))}
      </Swiper>

      <noscript>
        <div className={styles.noscriptGrid}>
          {items.map((pkg) => (
            <PackageCard key={pkg.slug} pkg={pkg} eager={false} />
          ))}
        </div>
      </noscript>
    </section>
  );
}

function PackageCard({ pkg, eager }: { pkg: Item; eager: boolean }) {
  return (
    <Link href={`/tour-packages/${pkg.slug}`} className={styles.card}>
      <div className={styles.imgWrap}>
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          src={pkg.image || FALLBACK}
          alt={pkg.image_alt ?? pkg.title}
          className={styles.img}
          loading={eager ? "eager" : "lazy"}
          decoding="async"
        />
      </div>
      <div className={styles.body}>
        <p className={styles.title}>{pkg.title}</p>
        <p className={styles.location}>{pkg.location}</p>
        <div className={styles.footer}>
          <span className={styles.duration}>
            {pkg.duration_days} Days / {pkg.duration_nights} Nights
          </span>
          <span className={styles.cta}>View Package</span>
        </div>
      </div>
    </Link>
  );
}
