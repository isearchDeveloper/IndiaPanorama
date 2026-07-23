import styles from "./FestivalGallery.module.css";

const FALLBACK = "/images/about-banner-pages.jpg";

interface Props {
  images: string[];
  name: string;
}

export default function FestivalGallery({ images, name }: Props) {
  if (images.length <= 1) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>Photo Gallery</h2>
      <div className={styles.gallery}>
        {images.map((src, i) => (
          <div key={i} className={styles.imgWrap}>
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img
              src={src || FALLBACK}
              alt={`${name} — photo ${i + 1}`}
              className={styles.img}
            />
          </div>
        ))}
      </div>
    </section>
  );
}
