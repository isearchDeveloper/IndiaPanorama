import Link from "next/link";
import SafeImage from "@/app/components/common/SafeImage";
import styles from "./TATopTours.module.css";

type Item = { id: number; name: string; image: string; label: string; href: string; };

type Props = { title: string; items: Item[]; };

export default function TATopTours({ title, items }: Props) {
  if (!items.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>
      <div className={styles.grid}>
        {items.map((tour) => (
          <Link key={tour.id} href={tour.href} className={styles.card}>
            <div className={styles.imgWrap}>
              <SafeImage src={tour.image} alt={tour.name} fill sizes="(max-width: 640px) 100vw, 33vw" className={styles.img} />
            </div>
            <div className={styles.body}>
              <h3 className={styles.name}>{tour.name} <span>(Tourist Attraction)</span></h3>
              <span className={styles.label}>{tour.label}</span>
            </div>
          </Link>
        ))}
      </div>
    </section>
  );
}
