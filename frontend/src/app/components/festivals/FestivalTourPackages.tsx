"use client";

import { Swiper, SwiperSlide } from "swiper/react";
import { Pagination, A11y, Autoplay } from "swiper/modules";
import Link from "next/link";
import styles from "./FestivalTourPackages.module.css";

import Image from "next/image";
const FALLBACK = "/images/about-banner-pages.jpg";

import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";

export interface Package {
  slug: string;
  name: string;
  image: string;
  tag: string;
  days: number;
  nights: number;
  href: string;
}

interface Props {
  heading?: string;
  subtext?: string;
  packages?: Package[];
}

export default function FestivalTourPackages({ heading, subtext, packages}: Props) {
  if (!packages || !packages.length) return null;

  return (
    <section className={styles.section}>
      <div className={styles.inner}>
        <h2 className={styles.heading}>{heading ?? "Festival Tour Packages"}</h2>
        {subtext && <p className={styles.subtext}>{subtext}</p>}
        <div className={styles.sliderWrap}>
          <noscript>
            <div className={styles.noscriptGrid}>
              {packages.map((pkg) => (
                <Link key={pkg.slug} href={`/tour-packages/${pkg.slug}`} className={styles.card}>
                  <div className={styles.imgWrap}>
                    <Image
                      src={pkg.image || FALLBACK}
                      alt={pkg.name}
                      fill
                      sizes="(max-width: 580px) 100vw, (max-width: 900px) 50vw, 33vw"
                      className={styles.img}
                    />
                    <span className={styles.tag}>{pkg.tag}</span>
                  </div>
                  <div className={styles.body}>
                    <div className={styles.titleRow}>
                      <h3 className={styles.name}>{pkg.name}</h3>
                      <span className={styles.duration}>{pkg.days}D / {pkg.nights}N</span>
                    </div>
                    <div className={styles.footer}>
                      <span className={styles.cta}>View Details</span>
                    </div>
                  </div>
                </Link>
              ))}
            </div>
          </noscript>
          <Swiper
            modules={[Pagination, A11y, Autoplay]}
            spaceBetween={20}
            slidesPerView={4}
            pagination={{ clickable: true, dynamicBullets: true }}
            autoplay={{ delay: 3000, disableOnInteraction: false }}
            loop={packages.length > 3}
            breakpoints={{
              0: { slidesPerView: 1, spaceBetween: 12 },
              580: { slidesPerView: 2, spaceBetween: 16 },
              900: { slidesPerView: 3, spaceBetween: 18 },
              1200: { slidesPerView: 3, spaceBetween: 15 },
            }}
            className={styles.swiper}
          >
            {packages.map((pkg) => (
              <SwiperSlide key={pkg.slug} className={styles.slide}>
                <Link href={`/tour-packages/${pkg.slug}`} className={styles.card}>
                  <div className={styles.imgWrap}>
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img src={pkg.image || FALLBACK} alt={pkg.name} className={styles.img} />
                    <span className={styles.tag}>{pkg.tag}</span>
                  </div>
                  <div className={styles.body}>
                    <div className={styles.titleRow}>
                      <h3 className={styles.name}>{pkg.name}</h3>
                      <span className={styles.duration}>
                        {pkg.days}D / {pkg.nights}N
                      </span>
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
