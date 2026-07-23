import Link from "next/link";
import styles from "./FestivalHighlightsGrid.module.css";

const FALLBACK = "/images/about-banner-pages.jpg";

interface Item {
  image: string;
  image_alt: string | null;
  title: string;
  slug: string;
}

interface Props {
  items: Item[];
}

export default function FestivalHighlightsGrid({ items }: Props) {
  if (!items.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>Festival Highlights</h2>
      <div className={styles.grid}>
        {items.map((item) => (
          <Link key={item.slug} href={`/festivals/${item.slug}`} className={styles.card}>
            <div className={styles.imgWrap}>
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img
                src={item.image || FALLBACK}
                alt={item.image_alt ?? item.title}
                className={styles.img}
                loading="lazy"
              />
            </div>
            <p className={styles.title}>{item.title}</p>
          </Link>
        ))}
      </div>
    </section>
  );
}
