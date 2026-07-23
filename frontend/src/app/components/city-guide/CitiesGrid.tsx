import Image from "next/image";
import Link from "next/link";
import styles from "./CitiesGrid.module.css";

type CityCard = { slug: string; stateSlug: string; name: string; state: string; image: string; tagline: string };

interface Props {
  cities: CityCard[];
}

export default function CitiesGrid({ cities }: Props) {
  if (!cities || cities.length === 0) return null;

  return (
    <div className={styles.grid}>
      {cities.map((c) => (
        <Link key={`${c.stateSlug}-${c.slug}`} href={`/${c.stateSlug}/${c.slug}/city-guide`} className={styles.card}>
          <div className={styles.imgWrap}>
            <Image
              src={c.image}
              alt={c.name}
              fill
              sizes="(max-width: 640px) 100vw, (max-width: 900px) 50vw, 25vw"
              className={styles.img}
            />
            <span className={styles.stateBadge}>{c.state}</span>
          </div>
          <div className={styles.body}>
            <span className={styles.name}>{c.name}</span>
            <p className={styles.tagline}>{c.tagline}</p>
          </div>
        </Link>
      ))}
    </div>
  );
}

