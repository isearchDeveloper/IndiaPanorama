"use client";

import Link from "next/link";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import SafeImage from "@/app/components/common/SafeImage";
import styles from "./CityExperiencesSlider.module.css";

interface ExperienceItem {
  id: number;
  name: string;
  image: string;
  description: string;
  popular?: string[];
  isNew?: boolean;
  label?: string;
  href: string;
}

interface Props {
  heading: string;
  viewAllHref?: string;
  items: ExperienceItem[];
}

export default function CityExperiencesSlider({ heading, viewAllHref, items }: Props) {
  if (!items.length) return null;

  return (
    <section className={styles.section}>
      <div className={styles.header}>
        <h2 className={styles.heading}>{heading}</h2>
        {viewAllHref && (
          <Link href={viewAllHref} className={styles.viewAll}>
            View All
          </Link>
        )}
      </div>
      <Swiper
        modules={[Autoplay, Pagination]}
        autoplay={{ delay: 3500, disableOnInteraction: false }}
        spaceBetween={20}
        slidesPerView={1}
        loop={items.length > 3}
        pagination={{ el: `.${styles.pagination}`, clickable: true, dynamicBullets: true }}
        breakpoints={{
          480: { slidesPerView: 2 },
          768: { slidesPerView: 3 },
        }}
        className={styles.swiper}
      >
        {items.map((item) => (
          <SwiperSlide key={item.id} className={styles.slide}>
            {item.href && item.href !== "#" ? (
              <Link href={item.href} className={styles.card}>
                <CardInner item={item} />
              </Link>
            ) : (
              <div className={styles.card}>
                <CardInner item={item} />
              </div>
            )}
          </SwiperSlide>
        ))}
      </Swiper>
      <div className={styles.pagination} />
    </section>
  );
}

function CardInner({ item }: { item: ExperienceItem }) {
  return (
    <>
      <div className={styles.imgWrap}>
        <SafeImage
          src={item.image}
          alt={item.name}
          fill
          sizes="(max-width: 640px) 100vw, 33vw"
          className={styles.img}
        />
        {item.isNew && <span className={styles.badge}>New</span>}
      </div>
      <div className={styles.body}>
        <h3 className={styles.name}>{item.name}</h3>
        <p className={styles.desc}>{item.description}</p>
        {item.popular && item.popular.length > 0 && (
          <p className={styles.popular}>
            <span className={styles.popularLabel}>Popular</span>{" "}
            {item.popular.join(" | ")}
          </p>
        )}
        {item.label && <span className={styles.label}>{item.label}</span>}
      </div>
    </>
  );
}
