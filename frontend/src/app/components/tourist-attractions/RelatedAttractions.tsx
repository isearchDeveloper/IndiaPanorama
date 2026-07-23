"use client";

import { useState } from "react";
import Link from "next/link";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import SafeImage from "@/app/components/common/SafeImage";
import styles from "./RelatedAttractions.module.css";

interface RelatedItem {
  id: number;
  name: string;
  image: string;
  description: string;
  href: string;
}

interface Props {
  heading?: string;
  items: RelatedItem[];
}

export default function RelatedAttractions({
  heading = "Explore More Attractions",
  items,
}: Props) {
  const [activeIndex, setActiveIndex] = useState(0);

  if (!items.length) return null;

  return (
    <section className={styles.section}>
      <div className={styles.inner}>
        <div className={styles.header}>
          <h2 className={styles.heading}>{heading}</h2>
          <Link href="/tourist-attractions" className={styles.viewAll}>
            View All
          </Link>
        </div>
        <Swiper
          modules={[Autoplay, Pagination]}
          autoplay={{ delay: 3500, disableOnInteraction: false }}
          spaceBetween={20}
          slidesPerView={1}
          loop={items.length > 3}
          pagination={{ el: `.${styles.pagination}`, clickable: true, dynamicBullets: true }}
          onSlideChange={(s) => setActiveIndex(s.realIndex)}
          breakpoints={{
            480: { slidesPerView: 2 },
            768: { slidesPerView: 3 },
            1024: { slidesPerView: 4 },
          }}
          className={styles.swiper}
        >
          {items.map((item) => (
            <SwiperSlide key={item.id} className={styles.slide}>
              {item.href && item.href !== "#" ? (
                <Link href={item.href} className={styles.card}>
                  <div className={styles.imgWrap}>
                    <SafeImage
                      src={item.image}
                      alt={item.name}
                      fill
                      sizes="(max-width: 640px) 100vw, 25vw"
                      className={styles.img}
                    />
                  </div>
                  <div className={styles.body}>
                    <h3 className={styles.name}>{item.name}</h3>
                    <p className={styles.desc}>{item.description}</p>
                  </div>
                </Link>
              ) : (
                <div className={styles.card}>
                  <div className={styles.imgWrap}>
                    <SafeImage
                      src={item.image}
                      alt={item.name}
                      fill
                      sizes="(max-width: 640px) 100vw, 25vw"
                      className={styles.img}
                    />
                  </div>
                  <div className={styles.body}>
                    <h3 className={styles.name}>{item.name}</h3>
                    <p className={styles.desc}>{item.description}</p>
                  </div>
                </div>
              )}
            </SwiperSlide>
          ))}
        </Swiper>
        <div className={styles.pagination} />
      </div>
    </section>
  );
}
