import Image from "next/image";
import styles from "./Banner.module.css";

const FALLBACK = "/images/about-banner-pages.jpg";

interface BannerProps {
  title: string;
  subtitle?: string;
  bgImage?: string | null;
  textPosition?: "center" | "bottom";
}

// NOTE: overlay + title/subtitle abhi HIDDEN hain (sirf banner image dikhti hai).
// Props/API flow waise ka waisa hai — wapas dikhane ke liye niche ke commented
// blocks uncomment kar do bas.
export default function Banner({ title ="",subtitle ="", bgImage, textPosition = "center" }: BannerProps) {
  const isBottom = textPosition === "bottom";
  // hidden text me bhi props "used" rahen taaki lint/TS error na aaye
  void title; void subtitle; void isBottom;
  return (
    <section className={styles.bannerSection}>
      <div className={styles.bannerBg}>
        <Image
          src={bgImage || FALLBACK}
          alt=""
          role="presentation"
          fill
          priority
          sizes="100vw"
          className={styles.bgImage}
        />
        {/* overlay hidden — uncomment to restore
        <div className={isBottom ? styles.overlayBottom : styles.overlay} />
        */}
      </div>

      {/* banner text hidden — uncomment to restore
      <div className={isBottom ? styles.contentContainerBottom : styles.contentContainer}>
        {isBottom ? (
          <div className={styles.textBlurPill}>
            <div className={styles.title}>{title}</div>
            {subtitle && <p className={styles.subtitle}>{subtitle}</p>}
          </div>
        ) : (
          <>
            <div className={styles.title}>{title}</div>
            {subtitle && <p className={styles.subtitle}>{subtitle}</p>}
          </>
        )}
      </div>
      */}
    </section>
  );
}
