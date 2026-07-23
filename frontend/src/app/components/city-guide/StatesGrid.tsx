import Image from "next/image";
import Link from "next/link";
import styles from "./StatesGrid.module.css";

type StateCard = { slug: string; name: string; image: string; tagline: string; cityCount: number };

interface Props {
  states: StateCard[];
}

export default function StatesGrid({ states }: Props) {
  if (!states || states.length === 0) return null;

  return (
    <div className={styles.grid}>
      {states.map((s) => (
        <Link key={s.slug} href={`/${s.slug}/city-guide`} className={styles.card}>
          <div className={styles.imgWrap}>
            <Image
              src={s.image}
              alt={s.name}
              fill
              sizes="(max-width: 640px) 100vw, (max-width: 900px) 50vw, 25vw"
              className={styles.img}
            />
            <div className={styles.overlay} />
            <div className={styles.info}>
              <span className={styles.name}>{s.name}</span>
              <span className={styles.tagline}>{s.tagline}</span>
              <span className={styles.count}>{s.cityCount} Cities</span>
            </div>
          </div>
        </Link>
      ))}
    </div>
  );
}

