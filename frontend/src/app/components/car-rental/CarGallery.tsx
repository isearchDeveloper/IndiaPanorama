"use client";

import { useState, useEffect, useCallback } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Navigation, Pagination, Thumbs, FreeMode, Autoplay } from "swiper/modules";
import type { Swiper as SwiperType } from "swiper";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import Image from "next/image";
import "swiper/css/thumbs";
import "swiper/css/free-mode";
import styles from "./CarGallery.module.css";

const FALLBACK = "/images/about-banner-pages.jpg";

interface Props {
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  images: any[];
}

export default function CarGallery({ images }: Props) {
  const [lightboxOpen, setLightboxOpen] = useState(false);
  const [activeIndex, setActiveIndex] = useState(0);
  const [thumbsSwiper, setThumbsSwiper] = useState<SwiperType | null>(null);

  const openLightbox = (i: number) => {
    setActiveIndex(i);
    setLightboxOpen(true);
  };

  const closeLightbox = useCallback(() => setLightboxOpen(false), []);

  const prev = useCallback(() => setActiveIndex((i) => (i - 1 + images.length) % images.length), [images.length]);
  const next = useCallback(() => setActiveIndex((i) => (i + 1) % images.length), [images.length]);

  useEffect(() => {
    if (!lightboxOpen) return;
    const onKey = (e: KeyboardEvent) => {
      if (e.key === "Escape") closeLightbox();
      if (e.key === "ArrowLeft") prev();
      if (e.key === "ArrowRight") next();
    };
    window.addEventListener("keydown", onKey);
    document.body.style.overflow = "hidden";
    return () => {
      window.removeEventListener("keydown", onKey);
      document.body.style.overflow = "";
    };
  }, [lightboxOpen, prev, next, closeLightbox]);

  if (!images?.length) return null;

  const getUrl = (img: any) => img.url || img.image || FALLBACK;
  const getAlt = (img: any) => img.alt ?? img.image_alt ?? "";

  return (
    <>
      {/* Main swiper gallery */}
      <div className={styles.galleryWrap}>
        <Swiper
          modules={[Pagination, Autoplay]}
          spaceBetween={12}
          slidesPerView={1}
          autoplay={{ delay: 3000, disableOnInteraction: false }}
          pagination={{ clickable: true, dynamicBullets: true }}
          breakpoints={{
            480: { slidesPerView: 2, spaceBetween: 12 },
            768: { slidesPerView: 3, spaceBetween: 14 },
          }}
          className={styles.mainSwiper}
        >
          {images.map((img, i) => (
            <SwiperSlide key={i}>
              <button
                type="button"
                className={styles.slideBtn}
                onClick={() => openLightbox(i)}
                aria-label={`Open image ${i + 1}`}
              >
                <Image
                  src={getUrl(img)}
                  alt={getAlt(img)}
                  fill
                  sizes="(max-width: 768px) 50vw, 33vw"
                  className={styles.slideImg}
                />
                <span className={styles.zoomIcon} aria-hidden="true">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" strokeWidth="2.2" strokeLinecap="round">
                    <circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35M11 8v6M8 11h6"/>
                  </svg>
                </span>
              </button>
            </SwiperSlide>
          ))}
        </Swiper>
      </div>

      {/* Lightbox */}
      {lightboxOpen && (
        <div className={styles.lightboxOverlay} onClick={closeLightbox} role="dialog" aria-modal="true">
          <div className={styles.lightboxInner} onClick={(e) => e.stopPropagation()}>

            {/* Close */}
            <button type="button" className={styles.lbClose} onClick={closeLightbox} aria-label="Close">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>

            {/* Counter */}
            <span className={styles.lbCounter}>{activeIndex + 1} / {images.length}</span>

            {/* Main image swiper */}
            <Swiper
              modules={[Navigation, Thumbs]}
              thumbs={{ swiper: thumbsSwiper && !thumbsSwiper.destroyed ? thumbsSwiper : null }}
              initialSlide={activeIndex}
              onSlideChange={(s) => setActiveIndex(s.activeIndex)}
              navigation
              className={styles.lbMainSwiper}
            >
              {images.map((img, i) => (
                <SwiperSlide key={i} className={styles.lbSlide}>
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img src={getUrl(img)} alt={getAlt(img)} className={styles.lbImg} />
                </SwiperSlide>
              ))}
            </Swiper>

            {/* Thumbnails */}
            {images.length > 1 && (
              <Swiper
                modules={[FreeMode, Thumbs]}
                onSwiper={setThumbsSwiper}
                spaceBetween={8}
                slidesPerView={Math.min(images.length, 6)}
                freeMode
                watchSlidesProgress
                className={styles.lbThumbSwiper}
              >
                {images.map((img, i) => (
                  <SwiperSlide key={i} className={styles.lbThumbSlide}>
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img src={getUrl(img)} alt={getAlt(img)} className={styles.lbThumbImg} />
                  </SwiperSlide>
                ))}
              </Swiper>
            )}
          </div>
        </div>
      )}
    </>
  );
}
