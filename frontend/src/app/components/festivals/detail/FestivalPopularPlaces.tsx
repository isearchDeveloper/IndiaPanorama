import styles from "./FestivalPopularPlaces.module.css";

const FALLBACK = "/images/about-banner-pages.jpg";

interface Item {
  image: string;
  image_alt: string | null;
  name: string;
}

interface Props {
  title: string;
  items: Item[];
}

export default function FestivalPopularPlaces({ title, items }: Props) {
  if (!items.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>
      <div className={styles.row}>
        {items.map((item, i) => (
          <div key={i} className={styles.wrap}>
            <div className={styles.oval}>
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img
                src={item.image || FALLBACK}
                alt={item.image_alt ?? item.name}
                className={styles.img}
                loading="lazy"
              />
            </div>
            <p className={styles.name}>{item.name}</p>
          </div>
        ))}
      </div>
    </section>
  );
}
