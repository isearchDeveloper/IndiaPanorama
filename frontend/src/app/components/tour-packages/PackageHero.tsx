"use client";

import { useRef, useState } from "react";
import Image from "next/image";
import Script from "next/script";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, EffectFade } from "swiper/modules";
import "swiper/css";
import "swiper/css/effect-fade";
import type { Swiper as SwiperType } from "swiper";
import Lightbox from "yet-another-react-lightbox";
import Captions from "yet-another-react-lightbox/plugins/captions";
import "yet-another-react-lightbox/styles.css";
import "yet-another-react-lightbox/plugins/captions.css";
import "./LightboxOverride.css";
import styles from "./PackageHero.module.css";

const FALLBACK = "/images/about-banner-pages.jpg";

// Static review slider abhi HIDDEN hai — uski jagah Elfsight Google Reviews widget
// lagta hai. Wapas static chahiye to bas isko true kar do.
const SHOW_STATIC_REVIEWS = false;

// static reviews for now — backend reviews aane pe yahi shape me map kar denge
const STATIC_REVIEWS = [
  {
    text: "Ottimo Tour Operator indiano. Sono anni che utilizziamo questa agenzia per viaggiare in diverse aree dell'India. Ci siamo sempre trovati benissimo.",
    name: "Pierfrancesco Borsetta",
  },
  {
    text: "Exceptional service from start to finish. Every hotel, every driver, every guide was hand-picked with care. Our Rajasthan trip was flawless.",
    name: "Sarah Mitchell",
  },
  {
    text: "We travelled with our two kids and Indian Panorama planned everything perfectly — comfortable pace, wonderful homestays and unforgettable experiences.",
    name: "Thomas Weber",
  },
  {
    text: "The itinerary was tailored exactly to our wishes. The backwater houseboat in Kerala was the highlight of our honeymoon. Highly recommended!",
    name: "Emma Laurent",
  },
  {
    text: "Professional, responsive and genuinely caring team. They handled a last-minute change without any fuss. We will definitely book again.",
    name: "James O'Connor",
  },
  {
    text: "From the Taj Mahal at sunrise to the quiet villages of the south, every detail was thought through. The best travel company we have used in India.",
    name: "Anna Kowalska",
  },
];

interface ImageItem {
  src: string;
  alt: string;
}

interface Props {
  title: string;
  duration: string;
  images: ImageItem[];
  rating?: number;
  totalReview?: number;
  price?: string | null;
}

export default function PackageHero({
  title,
  duration,
  images,
  price,
}: Props) {
  const slides = images.length > 0 ? images : [{ src: FALLBACK, alt: title }];
  const hasMultiple = slides.length > 1;

  const imageSwiperRef = useRef<SwiperType | null>(null);
  const reviewSwiperRef = useRef<SwiperType | null>(null);
  const [lightboxOpen, setLightboxOpen] = useState(false);
  const [lightboxIndex, setLightboxIndex] = useState(0);

  const lightboxSlides = slides.map((img) => ({ src: img.src, title: img.alt }));

  function openAt(index: number) {
    setLightboxIndex(index);
    setLightboxOpen(true);
  }

  return (
    <section className={styles.hero}>
      <div className={styles.inner}>

        {/* ── Title row: h1 + duration badge ── */}
        <div className={styles.headerRow}>
          <h1 className={styles.title}>{title}</h1>
          {duration && <span className={styles.durationBadge}>{duration}</span>}
        </div>

   

        {/* ── Main grid: image slider left, review card right ── */}
        <div className={styles.grid}>

          {/* Left — image slider */}
          <div className={styles.sliderWrap}>
            {/* arrows on the banner image, one on each side — only when multiple images */}
            {hasMultiple && (
              <>
                <button
                  className={`${styles.arrowBtn} ${styles.imageArrowLeft}`}
                  onClick={() => imageSwiperRef.current?.slidePrev()}
                  aria-label="Previous image"
                >
                  ‹
                </button>
                <button
                  className={`${styles.arrowBtn} ${styles.imageArrowRight}`}
                  onClick={() => imageSwiperRef.current?.slideNext()}
                  aria-label="Next image"
                >
                  ›
                </button>
              </>
            )}
            <Swiper
              modules={[Autoplay, EffectFade]}
              effect="fade"
              fadeEffect={{ crossFade: true }}
              autoplay={hasMultiple ? { delay: 4000, disableOnInteraction: false } : false}
              loop={hasMultiple}
              onSwiper={(s) => { imageSwiperRef.current = s; }}
              className={styles.imageSwiper}
            >
              {slides.map((img, i) => (
                <SwiperSlide key={i}>
                  <button
                    className={styles.imgBtn}
                    onClick={() => openAt(i)}
                    aria-label={`View image ${i + 1}`}
                  >
                    <div className={styles.slideImgWrap}>
                      <Image
                        src={img.src}
                        alt={img.alt}
                        fill
                        priority={i === 0}
                        sizes="(max-width: 1024px) 100vw, 62vw"
                        className={styles.slideImg}
                      />
                    </div>
                  </button>
                </SwiperSlide>
              ))}
            </Swiper>
          </div>

          {/* Right — reviews */}
          <div className={styles.reviewCol}>
            {/* Elfsight Google Reviews | Indian Panorama */}
            {!SHOW_STATIC_REVIEWS && (
              <div className={styles.elfsightWrap}>
                <Script src="https://elfsightcdn.com/platform.js" strategy="lazyOnload" />
                <div
                  className="elfsight-app-540e7153-a626-4898-b630-a4b2ee4e8650"
                  data-elfsight-app-lazy
                />
              </div>
            )}

            {/* purana static review slider — hidden, SHOW_STATIC_REVIEWS=true karne pe wapas */}
            {SHOW_STATIC_REVIEWS && (<>
            <div className={styles.reviewCard}>
              <span className={styles.quoteIcon} aria-hidden="true">
                <svg viewBox="0 0 24 24" width="26" height="26" fill="#1a3a1c">
                  <path d="M9.5 5C6.5 6.6 4.5 9.3 4.5 12.7c0 2.9 1.9 4.8 4.2 4.8 2.1 0 3.7-1.6 3.7-3.7 0-2-1.5-3.4-3.4-3.4-.3 0-.7 0-.8.1.3-1.8 1.9-3.8 3.6-4.8L9.5 5zm8.6 0c-3 1.6-5 4.3-5 7.7 0 2.9 1.9 4.8 4.2 4.8 2.1 0 3.7-1.6 3.7-3.7 0-2-1.5-3.4-3.4-3.4-.3 0-.6 0-.8.1.3-1.8 1.9-3.8 3.6-4.8L18.1 5z" />
                </svg>
              </span>

              <Swiper
                modules={[Autoplay]}
                autoplay={{ delay: 4500, disableOnInteraction: false }}
                loop
                onSwiper={(s) => { reviewSwiperRef.current = s; }}
                className={styles.reviewSwiper}
              >
                {STATIC_REVIEWS.map((review, i) => (
                  <SwiperSlide key={i}>
                    <div className={styles.reviewSlide}>
                      <div className={styles.stars} aria-label="5 star rating">
                        {"★★★★★".split("").map((s, j) => (
                          <span key={j} className={styles.star}>{s}</span>
                        ))}
                      </div>
                      <p className={styles.reviewText}>{review.text}</p>
                      <div className={styles.reviewAuthor}>
                        <span className={styles.avatar}>{review.name.charAt(0)}</span>
                        <span className={styles.authorName}>{review.name}</span>
                      </div>
                    </div>
                  </SwiperSlide>
                ))}
              </Swiper>
            </div>

            {/* arrows — control the review slider (like the reference design) */}
            <div className={styles.arrows}>
              <button
                className={styles.arrowBtn}
                onClick={() => reviewSwiperRef.current?.slidePrev()}
                aria-label="Previous review"
              >
                ‹
              </button>
              <button
                className={styles.arrowBtn}
                onClick={() => reviewSwiperRef.current?.slideNext()}
                aria-label="Next review"
              >
                ›
              </button>
            </div>
            </>)}
          </div>

        </div>

      </div>

      <Lightbox
        open={lightboxOpen}
        close={() => setLightboxOpen(false)}
        slides={lightboxSlides}
        index={lightboxIndex}
        plugins={[Captions]}
        captions={{ showToggle: false, descriptionTextAlign: "center" }}
        styles={{ container: { backgroundColor: "rgba(0,0,0,0.92)" } }}
      />
    </section>
  );
}
