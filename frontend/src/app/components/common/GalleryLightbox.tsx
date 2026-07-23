"use client";

import { useState, useEffect, useCallback } from "react";
import styles from "./GalleryLightbox.module.css";

const FALLBACK = "/images/about-banner-pages.jpg";

type GalleryImage = { image: string; image_alt: string };

type Props = {
  heading?: string;
  images: GalleryImage[];
};

export default function GalleryLightbox({ heading = "Gallery", images }: Props) {
  const [active, setActive] = useState<number | null>(null);

  const close = useCallback(() => setActive(null), []);
  const prev = useCallback(() =>
    setActive((i) => (i !== null ? (i - 1 + images.length) % images.length : null)),
    [images.length]
  );
  const next = useCallback(() =>
    setActive((i) => (i !== null ? (i + 1) % images.length : null)),
    [images.length]
  );

  useEffect(() => {
    if (active === null) return;
    const handler = (e: KeyboardEvent) => {
      if (e.key === "Escape") close();
      if (e.key === "ArrowLeft") prev();
      if (e.key === "ArrowRight") next();
    };
    document.addEventListener("keydown", handler);
    document.body.style.overflow = "hidden";
    return () => {
      document.removeEventListener("keydown", handler);
      document.body.style.overflow = "";
    };
  }, [active, close, prev, next]);

  if (!images.length) return null;

  // 1 big + up to 4 small shown; extras counted from index 5 onward
  const VISIBLE = 5;
  const shown = images.slice(0, VISIBLE);
  const extras = images.length - VISIBLE; // negative means all fit
  const [first, ...smalls] = shown;

  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{heading}</h2>

      <div className={styles.grid}>
        {/* Big image — left */}
        <button
          className={`${styles.item} ${styles.itemFirst}`}
          onClick={() => setActive(0)}
          aria-label={first.image_alt}
        >
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img
            src={first.image || FALLBACK}
            alt={first.image_alt}
            className={styles.img}
            loading="eager"
            decoding="async"
            onError={(e) => { e.currentTarget.src = FALLBACK; }}
          />
        </button>

        {/* 2×2 sub-grid — right */}
        <div className={styles.subGrid}>
          {smalls.map((img, i) => {
            const globalIndex = i + 1;
            const isLast = i === smalls.length - 1 && extras > 0;
            return (
              <button
                key={i}
                className={styles.item}
                onClick={() => setActive(globalIndex)}
                aria-label={img.image_alt}
              >
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img
                  src={img.image || FALLBACK}
                  alt={img.image_alt}
                  className={styles.img}
                  loading="lazy"
                  decoding="async"
                  onError={(e) => { e.currentTarget.src = FALLBACK; }}
                />
                {isLast && (
                  <div className={styles.moreOverlay}>
                    <span className={styles.morePlus}>+{extras}</span>
                    <span className={styles.moreText}>more</span>
                  </div>
                )}
              </button>
            );
          })}
        </div>
      </div>

      {/* Lightbox — ALL images */}
      {active !== null && (
        <div className={styles.overlay} onClick={close} role="dialog" aria-modal="true">
          <button className={styles.close} onClick={close} aria-label="Close">✕</button>

          <button
            className={`${styles.nav} ${styles.navPrev}`}
            onClick={(e) => { e.stopPropagation(); prev(); }}
            aria-label="Previous"
          >‹</button>

          <div className={styles.lightboxImgWrap} onClick={(e) => e.stopPropagation()}>
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img
              src={images[active].image || FALLBACK}
              alt={images[active].image_alt}
              className={styles.lightboxImg}
              onError={(e) => { e.currentTarget.src = FALLBACK; }}
            />
          </div>

          <button
            className={`${styles.nav} ${styles.navNext}`}
            onClick={(e) => { e.stopPropagation(); next(); }}
            aria-label="Next"
          >›</button>

          <p className={styles.counter}>{active + 1} / {images.length}</p>
        </div>
      )}

      <noscript>
        <div className={styles.noscriptGrid}>
          {images.map((img, i) => (
            // eslint-disable-next-line @next/next/no-img-element
            <img key={i} src={img.image || FALLBACK} alt={img.image_alt} className={styles.noscriptImg} loading="lazy" />
          ))}
        </div>
      </noscript>
    </section>
  );
}
