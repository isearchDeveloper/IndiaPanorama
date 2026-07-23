import Image from "next/image";
import Link from "next/link";
import styles from "./ExploreGrid.module.css";

export interface ExploreGridItem {
  name: string;
  tagline?: string;
  description?: string; // CMS HTML allowed
  image: string;
  image_alt?: string;
  badge?: string;
  chip?: string;
  href: string;
}

// generic 3-col "Explore Now" cards grid — theme tours, activities,
// top experiences, top attractions sab sections isi se bante hain
interface Props {
  heading: string;
  items: ExploreGridItem[];
  viewAllHref?: string;
  linkLabel?: string;
}

export default function ExploreGrid({ heading, items, viewAllHref, linkLabel = "Explore Now →" }: Props) {
  if (!items.length) return null;

  return (
    <section className={styles.section}>
      <div className={styles.header}>
        <h2 className={styles.heading}>{heading}</h2>
        {viewAllHref && (
          <Link href={viewAllHref} className={styles.viewAll}>View All</Link>
        )}
      </div>

      <div className={styles.grid}>
        {items.map((item, i) => (
          <Link key={`${item.name}-${i}`} href={item.href} className={styles.card}>
            <div className={styles.imgWrap}>
              <Image
                src={item.image}
                alt={item.image_alt ?? item.name}
                fill
                sizes="(max-width: 640px) 100vw, (max-width: 900px) 50vw, 33vw"
                className={styles.img}
              />
              {item.badge && <span className={styles.badge}>{item.badge}</span>}
            </div>
            <div className={styles.body}>
              <div className={styles.nameRow}>
                <span className={styles.name}>
                  {item.name}
                  {item.tagline && <span className={styles.tagline}> ({item.tagline})</span>}
                </span>
                {item.chip && <span className={styles.chip}>{item.chip}</span>}
              </div>
              {item.description && (
                <div
                  className={`${styles.desc} cms-content`}
                  dangerouslySetInnerHTML={{ __html: item.description }}
                />
              )}
              <span className={styles.explore}>{linkLabel}</span>
            </div>
          </Link>
        ))}
      </div>
    </section>
  );
}
