"use client";

import { Swiper, SwiperSlide } from "swiper/react";
import { Pagination, A11y } from "swiper/modules";
import Link from "next/link";
import styles from "./FestivalStateCards.module.css";

import "swiper/css";
import "swiper/css/pagination";

const FALLBACK = "/images/about-banner-pages.jpg";

type FestivalStateCard = {
  slug: string;
  name: string;
  image: string;
  description: string;
  featuredFestivals: string[];
};

interface Props {
  states: FestivalStateCard[];
  heading?: string;
}

export default function FestivalStateCards({ states, heading }: Props) {
  if (!states.length) return null;

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
            0:    { slidesPerView: 1, spaceBetween: 12 },
            640:  { slidesPerView: 2, spaceBetween: 16 },
            1024: { slidesPerView: 3, spaceBetween: 20 },
          }}
          className={styles.swiper}
        >
          {states.map((state) => {
            const listItems = state.featuredFestivals.slice(0, 3);
            const padded = [...listItems, ...Array(Math.max(0, 3 - listItems.length)).fill(null)];

            return (
              <SwiperSlide key={state.slug} className={styles.slide}>
                <Link href={`/${state.slug}/festivals`} className={styles.card}>
                  <div className={styles.imgWrap}>
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img
                      src={state.image || FALLBACK}
                      alt={state.name}
                      className={styles.img}
                      loading="lazy"
                      decoding="async"
                      onError={(e) => { e.currentTarget.src = FALLBACK; }}
                    />
                    <div className={styles.overlay}>
                      <h3 className={styles.stateName}>{state.name}</h3>
                    </div>
                  </div>
                  <div className={styles.body}>
                    <p className={styles.desc}>
                      {state.description.slice(0, 130)}…
                    </p>
                    <div className={styles.popular}>
                      <span className={styles.popularLabel}>Popular Festivals</span>
                      <ul className={styles.festList}>
                        {padded.map((name, i) =>
                          name ? (
                            <li key={i}>
                              <span className={styles.festLink}>{name}</span>
                            </li>
                          ) : (
                            <li key={`empty-${i}`} className={styles.festEmpty}>&nbsp;</li>
                          )
                        )}
                      </ul>
                    </div>
                    <span className={styles.cta}>Explore Festivals &rsaquo;</span>
                  </div>
                </Link>
              </SwiperSlide>
            );
          })}
        </Swiper>
      </div>
    </section>
  );
}
