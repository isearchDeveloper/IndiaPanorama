import Link from "next/link";
import Image from "next/image";
import styles from "./NearbyAttractions.module.css";

interface NearbyAttraction {
  id: number;
  name: string;
  image?: string | null;
  description: string;
  href: string;
}

interface Props {
  heading?: string;
  viewAllHref?: string;
  items: NearbyAttraction[];
}

export default function NearbyAttractions({
  heading = "Nearby Attractions",
  viewAllHref,
  items,
}: Props) {
  return (
    <section className={styles.section}>
      <div className={styles.header}>
        <h2 className={styles.heading}>{heading}</h2>
        {viewAllHref && (
          <Link href={viewAllHref} className={styles.viewAll}>
            View All Attractions →
          </Link>
        )}
      </div>
      <div className={styles.grid}>
        {items.map((item) => {
          const inner = (
            <>
              {item.image && (
                <div className={styles.imgWrap}>
                  <Image
                    src={item.image}
                    alt={item.name}
                    fill
                    sizes="(max-width: 640px) 100vw, 33vw"
                    className={styles.img}
                  />
                </div>
              )}
              <div className={styles.body}>
                <h3 className={styles.name}>{item.name}</h3>
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
