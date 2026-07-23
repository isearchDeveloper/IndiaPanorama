import Image from "next/image";
import Link from "next/link";
import type { ActivityCategoryItem } from "@/services/activitiesService";
import styles from "./ActivityByCategoryList.module.css";

interface Props {
  title: string;
  items: ActivityCategoryItem[];
}

export default function ActivityByCategoryList({ title, items }: Props) {
  if (!items.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>
      <div className={styles.list}>
        {items.map((cat) => (
          <Link key={cat.slug} href="#" className={styles.row}>
            <div className={styles.imgWrap}>
              {cat.image ? (
                <Image
                  src={cat.image}
                  alt={cat.image_alt ?? cat.name}
                  fill
                  sizes="180px"
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
            <div className={styles.body}>
              <p className={styles.label}>{cat.name}</p>
              <p className={styles.desc}>{cat.description}</p>
            </div>
          </Link>
        ))}
      </div>
    </section>
  );
}
