import Image from "next/image";
import Link from "next/link";
import type { StateFeaturedCategoryItem } from "@/services/activitiesService";
import styles from "./StateFeaturedCategory.module.css";

interface Props {
  title: string;
  stateSlug: string;
  items: StateFeaturedCategoryItem[];
}

export default function   StateFeaturedCategory({ title, stateSlug, items }: Props) {
  if (!items.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>
      <div className={styles.grid}>
        {items.map((item) => (
          <Link
            key={item.slug}
            href={`/${stateSlug}/activities/${item.slug}`}
            className={styles.card}
          >
            <div className={styles.imgWrap}>
              {item.image ? (
                <Image
                  src={item.image}
                  alt={item.image_alt ?? item.name}
                  fill
                  sizes="(max-width:640px) 90vw, 50vw"
                  className={styles.img}
                />
              ) : (
                <div className={styles.imgPlaceholder} />
              )}
              <div className={styles.overlay} />
              <span className={styles.badge}>{item.name}</span>
            </div>
            <div className={styles.body}>
              <div
                className={styles.desc}
                dangerouslySetInnerHTML={{ __html: item.description }}
              />
            </div>
          </Link>
        ))}
      </div>
    </section>
  );
}
