import Link from "next/link";
import SafeImage from "@/app/components/common/SafeImage";
import styles from "./CategoryCards.module.css";

interface CategoryCard {
  id: number;
  title: string;
  slug: string;
  image: string;
  description: string;
  count?: number;
  href: string;
}

interface Props {
  heading: string;
  items: CategoryCard[];
  columns?: 2 | 3 | 4;
}

export default function CategoryCards({ heading, items, columns = 2 }: Props) {
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{heading}</h2>
      <div
        className={styles.grid}
        style={{ gridTemplateColumns: `repeat(${columns}, 1fr)` }}
      >
        {items.map((item) => {
          const inner = (
            <>
              <div className={styles.imgWrap}>
                <SafeImage
                  src={item.image}
                  alt={item.title}
                  fill
                  sizes="(max-width: 640px) 100vw, 50vw"
                  className={styles.img}
                />
                <div className={styles.imgOverlay} />
              </div>
              <div className={styles.body}>
                <h3 className={styles.title}>{item.title}</h3>
                <p className={styles.desc}>{item.description}</p>
              </div>
            </>
          );

          return item.href && item.href !== "#" ? (
            <Link key={item.id} href={item.href} className={styles.card}>
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
