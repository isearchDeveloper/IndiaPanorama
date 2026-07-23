import SafeImage from "@/app/components/common/SafeImage";
import styles from "./GallerySection.module.css";

interface GalleryImage {
  id: number;
  src: string;
  alt: string;
  span?: "wide" | "tall" | "normal";
}

interface Props {
  heading?: string;
  images: GalleryImage[];
}

export default function GallerySection({ heading = "Gallery", images }: Props) {
  if (!images || images.length === 0) return null;

  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{heading}</h2>
      <div className={styles.grid}>
        {images.map((img) => (
          <div
            key={img.id}
            className={`${styles.imgWrap} ${img.span === "wide" ? styles.wide : img.span === "tall" ? styles.tall : ""}`}
          >
            <SafeImage
              src={img.src}
              alt={img.alt}
              fill
              sizes="(max-width: 640px) 100vw, 33vw"
              className={styles.img}
            />
          </div>
        ))}
      </div>
    </section>
  );
}
