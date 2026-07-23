import Link from "next/link";
import styles from "./AttractionCards.module.css";

const FALLBACK = "/images/about-banner-pages.jpg";

type AttractionCard = {
  id: number;
  name: string;
  image: string;
  description: string;
  label?: string;
  href?: string;
};

type Props = {
  heading: string;
  items: AttractionCard[];
  columns?: 3 | 4;
};

export default function AttractionCards({ heading, items, columns = 3 }: Props) {
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{heading}</h2>
      <div className={`${styles.grid} ${columns === 4 ? styles.grid4 : styles.grid3}`}>
        {items.map((item, i) => {
          const href = item.href && item.href !== "#" ? item.href : null;
          const inner = (
            <>
              <div className={styles.imgWrap}>
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img
                  src={item.image || FALLBACK}
                  alt={item.name}
                  className={styles.img}
                  loading={i < 3 ? "eager" : "lazy"}
                  decoding="async"
                />
              </div>
              <div className={styles.cardBody}>
                <h3 className={styles.cardName}>{item.name}</h3>
                {item.description && (
                  <div className={`${styles.cardDesc} cms-content`} dangerouslySetInnerHTML={{ __html: item.description }} />
                )}
                <span className={styles.cta}>{item.label ?? "Explore Now →"}</span>
              </div>
            </>
          );

          return href ? (
            <Link key={item.id} href={href} className={styles.card}>
              {inner}
            </Link>
          ) : (
            <div key={item.id} className={styles.card}>
              {inner}
            </div>
          );
        })}
      </div>
    </section>
  );
}
