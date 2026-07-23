import Image from "next/image";
import Link from "next/link";
import type { ExpStateItem } from "@/data/experiences/rootPageData";
import styles from "./PopularStatesSection.module.css";

interface Props {
  heading: string;
  featured: ExpStateItem[];
  states: ExpStateItem[];
}

export default function PopularStatesSection({ heading, featured, states }: Props) {
  if (!featured.length && !states.length) return null;

  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{heading}</h2>

      {/* Featured — bade cards with tours badge */}
      {featured.length > 0 && (
        <div className={styles.featuredGrid}>
          {featured.map((s) => (
            <Link key={s.slug} href={s.href} className={styles.featuredCard}>
              <div className={styles.featuredImgWrap}>
                <Image
                  src={s.image}
                  alt={s.name}
                  fill
                  sizes="(max-width: 640px) 100vw, 33vw"
                  className={styles.img}
                />
                {s.toursCount && <span className={styles.badge}>{s.toursCount}</span>}
              </div>
              <span className={styles.featuredName}>{s.name}</span>
            </Link>
          ))}
        </div>
      )}

      {/* All states — chhote cards grid */}
      {states.length > 0 && (
        <div className={styles.statesGrid}>
          {states.map((s) => (
            <Link key={s.slug} href={s.href} className={styles.stateCard}>
              <div className={styles.stateImgWrap}>
                <Image
                  src={s.image}
                  alt={s.name}
                  fill
                  sizes="(max-width: 640px) 50vw, (max-width: 900px) 33vw, 20vw"
                  className={styles.img}
                />
              </div>
              <span className={styles.stateName}>{s.name}</span>
            </Link>
          ))}
        </div>
      )}
    </section>
  );
}
