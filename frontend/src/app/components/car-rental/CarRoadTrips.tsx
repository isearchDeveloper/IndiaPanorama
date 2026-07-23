"use client";

import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import Image from "next/image";
import Link from "next/link";
import { Star } from "lucide-react";
import type { RoadTripItem } from "@/types/carRental";
import styles from "./CarRoadTrips.module.css";

const FALLBACK = "/images/about-banner-pages.jpg";

const FALLBACK_ITEMS: RoadTripItem[] = [
  {
    id: 1,
    title: "Rajasthan Road Trip",
    image: "/images/about-banner-pages.jpg",
    rating: 5,
    destinations: "Jaipur · Jodhpur · Udaipur · Jaisalmer",
    duration_days: 8,
    duration_nights: 7,
    slug: "rajasthan-road-trip",
  },
  {
    id: 2,
    title: "Kerala Road Trip",
    image: "/images/about-banner-pages.jpg",
    rating: 5,
    destinations: "Kochi · Munnar · Alleppey · Kovalam",
    duration_days: 7,
    duration_nights: 6,
    slug: "kerala-road-trip",
  },
  {
    id: 3,
    title: "Himachal Pradesh Road Trip",
    image: "/images/about-banner-pages.jpg",
    rating: 4,
    destinations: "Manali · Shimla · Dharamshala · Dalhousie",
    duration_days: 9,
    duration_nights: 8,
    slug: "himachal-pradesh-road-trip",
  },
  {
    id: 4,
    title: "Goa Road Trip",
    image: "/images/about-banner-pages.jpg",
    rating: 4,
    destinations: "North Goa · South Goa",
    duration_days: 5,
    duration_nights: 4,
    slug: "goa-road-trip",
  },
];

interface Props {
  title: string;
  subtitle: string;
  items: RoadTripItem[];
}

export default function CarRoadTrips({ title, subtitle, items }: Props) {
  const trips = items.length > 0 ? items : FALLBACK_ITEMS;

  return (
    <section className={styles.section}>
      <div className={styles.inner}>
        <div className={styles.header}>
          <h2 className={styles.heading}>{title}</h2>
          <p className={styles.subtext}>{subtitle}</p>
        </div>

        <noscript>
          <div className={styles.noscriptGrid}>
            {trips.map((trip, i) => {
              const href = trip.url ?? (trip.slug ? `/car-rental/${trip.slug}` : "#");
              return (
                <div key={trip.id ?? i} className={styles.card}>
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <div className={styles.imgWrap}>
                    <img src={trip.image || FALLBACK} alt={trip.image_alt ?? trip.title} className={styles.img} />
                  </div>
                  <div className={styles.body}>
                    <h3 className={styles.cardTitle}>{trip.title}</h3>
                    <div className={styles.footer}>
                      <Link href={href} className={styles.bookBtn}>Book Now</Link>
                    </div>
                  </div>
                </div>
              );
            })}
          </div>
        </noscript>

        <Swiper
          modules={[Autoplay, Pagination]}
          spaceBetween={20}
          loop={trips.length > 4}
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
          {trips.map((trip, i) => {
            const href = trip.url ?? (trip.slug ? `/car-rental/${trip.slug}` : "#");
            const rating = trip.rating ?? 4;
            const displayTitle = trip.title ?? (trip as unknown as { state?: string }).state ?? "";
            const displayDest = trip.destinations ?? (trip as unknown as { route_text?: string }).route_text ?? "";
            return (
              <SwiperSlide key={trip.id ?? i} className={styles.slide}>
                <div className={styles.card}>
                  <div className={styles.imgWrap}>
                    <Image
                      src={trip.image || FALLBACK}
                      alt={trip.image_alt ?? displayTitle}
                      fill
                      sizes="(max-width: 640px) 100vw, (max-width: 900px) 50vw, 25vw"
                      className={styles.img}
                    />
                  </div>
                  <div className={styles.body}>
                    <div className={styles.titleRow}>
                      <h3 className={styles.cardTitle}>{displayTitle}</h3>
                      <div className={styles.stars} aria-label={`${rating} out of 5`}>
                        {Array.from({ length: 5 }).map((_, idx) => (
                          <Star
                            key={idx}
                            size={13}
                            className={idx < rating ? styles.starOn : styles.starOff}
                            aria-hidden="true"
                          />
                        ))}
                      </div>
                    </div>
                    {trip.destinations && (
                      <p className={styles.destinations}>{trip.destinations}</p>
                    )}
                    <div className={styles.footer}>
                      {(trip.duration_days || trip.duration_nights) && (
                        <span className={styles.duration}>
                          {trip.duration_days ? `${trip.duration_days} Days` : ""}
                          {trip.duration_days && trip.duration_nights ? " / " : ""}
                          {trip.duration_nights ? `${trip.duration_nights} Nights` : ""}
                        </span>
                      )}
                      <Link href={href} className={styles.bookBtn}>
                        Book Now
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
