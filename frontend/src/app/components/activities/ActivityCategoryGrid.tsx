import Image from "next/image";
import Link from "next/link";
import type { ActivityTypeItem } from "@/services/activitiesService";
import styles from "./ActivityCategoryGrid.module.css";

interface Props {
  title: string;
  items: ActivityTypeItem[];
}

export default function ActivityCategoryGrid({ title, items }: Props) {
  if (!items.length) return null;

  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>
      <div className={styles.grid}>
        {items.map((cat) => (
          <Link key={cat.slug} href={cat.state_slug && cat.city_slug ? `/${cat.state_slug}/${cat.city_slug}/${cat.slug}` : "#"} className={styles.card}>
            <div className={styles.imgWrap}>
              {cat.image ? (
                <Image
                  src={cat.image}
                  alt={cat.image_alt ?? cat.name}
                  fill
                  sizes="(max-width:640px) 50vw, (max-width:1024px) 33vw, 25vw"
                  className={styles.img}
                />
              ) : (
                <div className={styles.imgPlaceholder} aria-hidden="true" />
              )}
              <span className={styles.arrow} aria-hidden="true">
                <svg className={styles.arrowIcon} viewBox="0 0 14 14" fill="none" stroke="#fff" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round">
                  <path d="M2 7h10M7 2l5 5-5 5" />
                </svg>
              </span>
            </div>
            <p className={styles.label}>{cat.name}</p>
          </Link>
        ))}
      </div>
    </section>
  );
}
