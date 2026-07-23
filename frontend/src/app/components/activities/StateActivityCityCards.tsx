import Link from "next/link";
import SafeImage from "@/app/components/common/SafeImage";
import styles from "./StateActivityCityCards.module.css";

type ActivityCityCard = { name: string; image: string; tag?: string; category?: string; href: string };

interface Props {
  stateName: string;
  cards: ActivityCityCard[];
}

export default function StateActivityCityCards({ stateName, cards }: Props) {
  if (!cards || cards.length === 0) return null;

  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>Discover the Best Activities in {stateName}</h2>
      <div className={styles.grid}>
        {cards.map((card, i) => (
          <Link key={i} href={card.href} className={styles.card}>
            <div className={styles.imgWrap}>
              <SafeImage
                src={card.image}
                alt={card.name}
                fill
                sizes="(max-width:640px) 50vw, 33vw"
                className={styles.img}
              />
              <div className={styles.overlay} />
              <div className={styles.cardFooter}>
                <div className={styles.cardInfo}>
                  <p className={styles.cityName}>
                    {card.name}
                    <span className={styles.cityTag}> ({card.tag})</span>
                  </p>
                  <p className={styles.category}>{card.category}</p>
                </div>
                <span className={styles.exploreBtn}>Explore Now →</span>
              </div>
            </div>
          </Link>
        ))}
      </div>
    </section>
  );
}

