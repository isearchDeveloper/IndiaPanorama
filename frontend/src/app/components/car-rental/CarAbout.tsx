import Image from "next/image";
import type { GalleryImage } from "@/types/carRental";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import styles from "./CarAbout.module.css";

const FALLBACK = "/images/about-banner-pages.jpg";

interface Props {
  title: string;
  description: string;
  gallery: GalleryImage[];
}

export default function CarAbout({ title, description, gallery }: Props) {
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>
      <ReadMoreHtml html={description} className={styles.description} />

      {gallery.length > 0 && (
        <div className={styles.gallery}>
          {gallery.map((img, i) => (
            <div key={i} className={styles.galleryImgWrap}>
              <Image
                src={img.url || FALLBACK}
                alt={img.alt || ""}
                fill
                sizes="(max-width: 400px) 100vw, (max-width: 600px) 50vw, 33vw"
                className={styles.galleryImg}
              />
            </div>
          ))}
        </div>
      )}
    </section>
  );
}
