"use client";

import { Swiper, SwiperSlide } from "swiper/react";
import { Pagination, A11y } from "swiper/modules";
import Link from "next/link";
import styles from "./ExploreMoreStates.module.css";

import Image from "next/image";
const FALLBACK = "/images/about-banner-pages.jpg";

import "swiper/css";
import "swiper/css/pagination";

export interface ExploreMoreStateItem {
  name: string;
  slug: string;
  image: string;
  description: string;
  rating?: number;
}

interface Props {
  states: ExploreMoreStateItem[];
  heading?: string;
  subtext?: string;
}

export default function ExploreMoreStates({ states, heading, subtext }: Props) {
  if (!states || !states.length) return null;

  return (
    <section className={styles.section}>
      <div className={styles.inner}>
        {heading && <h2 className={styles.heading}>{heading}</h2>}
        {subtext && <p className={styles.subtext}>{subtext}</p>}
        <div className={styles.sliderWrap}>
          <Swiper
            modules={[Pagination, A11y]}
            spaceBetween={20}
            slidesPerView={4}
            pagination={{ clickable: true, dynamicBullets: true }}
            breakpoints={{
              0: { slidesPerView: 1, spaceBetween: 12 },
              580: { slidesPerView: 2, spaceBetween: 16 },
              900: { slidesPerView: 3, spaceBetween: 18 },
              1200: { slidesPerView: 4, spaceBetween: 20 },
            }}
            className={styles.swiper}
          >
            {states.map((state) => {
              const currentRating = Math.round(state.rating ?? 5);
              return (
                <SwiperSlide key={state.slug} className={styles.slide}>
                  <Link href={`/${state.slug}/tour-packages`} className={styles.card}>
                    <div className={styles.imgWrap}>
                      <Image
                        src={state.image || FALLBACK}
                        alt={state.name}
                        fill
                        sizes="(max-width: 580px) 100vw, (max-width: 900px) 50vw, 25vw"
                        className={styles.img}
                      />
                    </div>
                    <div className={styles.body}>
                      <div className={styles.nameRow}>
                        <h3 className={styles.name}>{state.name} Festivals</h3>
                        <div className={styles.stars}>
                          {Array.from({ length: 5 }).map((_, i) => (
                            <span
                              key={i}
                              className={i < currentRating ? styles.starFilled : styles.starEmpty}
                            >
                              ★
                            </span>
                          ))}
                        </div>
                      </div>
                      <p className={styles.desc}>{state.description}</p>
                      <div className={styles.footer}>
                        <span className={styles.btn}>View Details</span>
                      </div>
                    </div>
                  </Link>
                </SwiperSlide>
              );
            })}
          </Swiper>
        </div>
      </div>
    </section>
  );
}
