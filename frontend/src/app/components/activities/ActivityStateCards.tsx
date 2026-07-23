import Link from "next/link";
import SafeImage from "@/app/components/common/SafeImage";
import styles from "./ActivityStateCards.module.css";

type ActivityState = {
  slug: string; name: string; image: string; tagline?: string;
  activityCount?: number; topCategories: string[];
};

interface Props {
  states: ActivityState[];
  heading?: string;
}

export default function ActivityStateCards({ states, heading }: Props) {
  if (!states.length) return null;
  return (
    <section className={styles.section}>
      {heading && <h2 className={styles.heading}>{heading}</h2>}
      <div className={styles.grid}>
        {states.map((s) => (
          <Link key={s.slug} href={`/${s.slug}/activities`} className={styles.card}>
            <div className={styles.imgWrap}>
              <SafeImage
                src={s.image}
                alt={s.name}
                fill
                sizes="(max-width: 640px) 100vw, 50vw"
                className={styles.img}
              />
              <div className={styles.overlay} />
              <div className={styles.cardContent}>
                <h3 className={styles.stateName}>{s.name}</h3>
                <p className={styles.tagline}>{s.tagline}</p>
                <div className={styles.meta}>
                  <span className={styles.count}>{s.activityCount}+ Activities</span>
                </div>
                <div className={styles.categories}>
                  {s.topCategories.slice(0, 3).map((cat) => (
                    <span key={cat} className={styles.catPill}>{cat}</span>
                  ))}
                </div>
              </div>
            </div>
          </Link>
        ))}
      </div>
    </section>
  );
}

