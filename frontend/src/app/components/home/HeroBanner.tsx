"use client";

import { useRef, useState } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay } from "swiper/modules";
import { FaWhatsapp, FaLinkedinIn, FaInstagram, FaFacebookF } from "react-icons/fa";
import Link from "next/link";
import "swiper/css";
import styles from "./HeroBanner.module.css";
import type { HeroSlide } from "@/services/homeService";
import type { Swiper as SwiperType } from "swiper";

interface HeroBannerProps {
  slides: HeroSlide[];
}

export default function HeroBanner({ slides }: HeroBannerProps) {
  const [currentSlide, setCurrentSlide] = useState(1);
  const swiperRef = useRef<SwiperType | null>(null);
  const total = slides.length;

  if (!slides.length) return null;

  return (
    <section className={styles.bannerSection}>
      <noscript>
        {slides.map((slide, index) => (
          <div
            key={index}
            className={styles.slide}
            style={{ backgroundImage: `url(${slide.image})` }}
          >
            <div className={styles.overlay} />
            <p className={styles.subtitle}>{slide.subtitle}</p>
            <h1 className={styles.title}>{slide.title}</h1>
            <Link href={slide.button_url || "/"} className={styles.bookBtn}>
              {slide.button_text || "Book Now"}
            </Link>
          </div>
        ))}
      </noscript>
      <Swiper
        modules={[Autoplay]}
        autoplay={{ delay: 4000, disableOnInteraction: false }}
        loop={slides.length > 1}
        speed={800}
        onSwiper={(swiper) => { swiperRef.current = swiper; }}
        onSlideChange={(swiper) => setCurrentSlide(swiper.realIndex + 1)}
        className={styles.swiper}
      >
        {slides.map((slide, index) => (
          <SwiperSlide key={index}>
            <div
              className={styles.slide}
              style={{ backgroundImage: `url(${slide.image})` }}
            >
              <div className={styles.overlay} />

              <div className={styles.social}>
                <span className={styles.followText}>Follow us:</span>
                <div className={styles.socialIcons}>
                  <a href="#" aria-label="WhatsApp"><FaWhatsapp /></a>
                  <a href="#" aria-label="LinkedIn"><FaLinkedinIn /></a>
                  <a href="#" aria-label="Instagram"><FaInstagram /></a>
                  <a href="#" aria-label="Facebook"><FaFacebookF /></a>
                </div>
              </div>

              <p className={styles.subtitle}>{slide.subtitle}</p>

              <div className={styles.titleBar}>
                <div className={styles.progressLine} />
                <div className={styles.counterSection}>
                  <span>{String(currentSlide).padStart(2, "0")} | {String(total).padStart(2, "0")}</span>
                </div>
                <div className={styles.progressLine} />
                <h1 className={styles.title}>{slide.title}</h1>
                <div className={styles.progressLine} />
                <div className={styles.arrowSection}>
                  <button className={styles.arrowBtn} onClick={() => swiperRef.current?.slidePrev()} aria-label="Previous">&#8249;</button>
                  <button className={styles.arrowBtn} onClick={() => swiperRef.current?.slideNext()} aria-label="Next">&#8250;</button>
                </div>
                <div className={styles.progressLine} />
              </div>

              <Link href={slide.button_url || "/"} className={styles.bookBtn}>
                {slide.button_text || "Book Now"}
              </Link>
            </div>
          </SwiperSlide>
        ))}
      </Swiper>
    </section>
  );
}
