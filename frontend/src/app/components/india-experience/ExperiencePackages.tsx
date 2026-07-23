"use client";

import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import Link from "next/link";
import { Star } from "lucide-react";
import SafeImage from "@/app/components/common/SafeImage";
type PackageItem = { title: string; slug: string; image: string; image_alt?: string; duration_days?: number; duration_nights?: number; rating?: number; destinations?: string; url?: string };
import styles from "./ExperiencePackages.module.scss";

interface Props {
  packages: PackageItem[];
}

export default function ExperiencePackages({ packages }: Props) {
  if (!packages.length) return null;

  return (
    <section className={styles.section}>
      <div className={styles.inner}>
        <div className={styles.header}>
          <h2 className={styles.heading}>Popular Tour Packages</h2>
          <p className={styles.subtext}>
            India, being the seventh largest country globally, stands out as an extraordinary
            destination due to its diverse landscape and rich cultural heritage.
          </p>
        </div>

        <noscript>
          <div className="noscript-grid">
            {packages.map((pkg) => (
              <div key={pkg.slug} className={styles.card}>
                <div className={styles.imgWrap}>
                  <SafeImage src={pkg.image} alt={pkg.image_alt || pkg.title} fill sizes="220px" className={styles.img} />
                </div>
                <div className={styles.body}>
                  <div className={styles.titleRow}>
                    <h3 className={styles.title}>{pkg.title}</h3>
                  </div>
                  {pkg.destinations && <p className={styles.destinations}>{pkg.destinations}</p>}
                  <div className={styles.footer}>
                    {pkg.duration_days && (
                      <span className={styles.duration}>{pkg.duration_days} Days / {pkg.duration_nights} Nights</span>
                    )}
                    <Link href={pkg.url ?? `/tour-packages/${pkg.slug}`} className={styles.bookBtn}>Book Now</Link>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </noscript>

        <Swiper
          modules={[Autoplay, Pagination]}
          spaceBetween={20}
          loop={packages.length > 4}
          autoplay={{ delay: 3000, disableOnInteraction: false, pauseOnMouseEnter: true }}
          pagination={{ clickable: true, dynamicBullets: true }}
          breakpoints={{
            0:    { slidesPerView: 1.1, spaceBetween: 12 },
            480:  { slidesPerView: 1.3, spaceBetween: 14 },
            640:  { slidesPerView: 2,   spaceBetween: 16 },
            900:  { slidesPerView: 3,   spaceBetween: 18 },
            1200: { slidesPerView: 4,   spaceBetween: 20 },
          }}
          className={styles.swiper}
        >
          {packages.map((pkg) => (
            <SwiperSlide key={pkg.slug} className={styles.slide}>
              <div className={styles.card}>
                <div className={styles.imgWrap}>
                  <SafeImage
                    src={pkg.image}
                    alt={pkg.image_alt || pkg.title}
                    fill
                    sizes="(max-width: 640px) 88vw, (max-width: 900px) 46vw, 25vw"
                    className={styles.img}
                  />
                </div>
                <div className={styles.body}>
                  <div className={styles.titleRow}>
                    <h3 className={styles.title}>{pkg.title}</h3>
                    {pkg.rating !== undefined && (
                      <div className={styles.stars} aria-label={`${pkg.rating} out of 5`}>
                        {Array.from({ length: 5 }).map((_, i) => (
                          <Star
                            key={i}
                            size={13}
                            className={i < (pkg.rating ?? 0) ? styles.starOn : styles.starOff}
                            aria-hidden="true"
                          />
                        ))}
                      </div>
                    )}
                  </div>
                  {pkg.destinations && <p className={styles.destinations}>{pkg.destinations}</p>}
                  <div className={styles.footer}>
                    {pkg.duration_days && (
                      <span className={styles.duration}>
                        {pkg.duration_days} Days / {pkg.duration_nights} Nights
                      </span>
                    )}
                    <Link href={pkg.url ?? `/tour-packages/${pkg.slug}`} className={styles.bookBtn}>
                      Book Now
                    </Link>
                  </div>
                </div>
              </div>
            </SwiperSlide>
          ))}
        </Swiper>

        <div className={styles.pagination} />
      </div>
    </section>
  );
}
