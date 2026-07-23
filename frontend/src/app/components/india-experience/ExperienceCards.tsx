import Link from "next/link";
import { ArrowUpRight } from "lucide-react";
import SafeImage from "@/app/components/common/SafeImage";
type ThemeItem = { name: string; slug: string; image: string; image_alt?: string; description?: string; popular_spots_text?: string };
import styles from "./ExperienceCards.module.scss";

interface Props {
  heading: string;
  description: string;
  themes: ThemeItem[];
}

export default function ExperienceCards({ heading, description, themes }: Props) {
  if (!themes || themes.length === 0) return null;

  return (
    <div className={styles.wrap}>
      <div className={styles.sectionHeader}>
        <h2 className={styles.heading}>{heading}</h2>
        <p className={styles.description}>{description}</p>
      </div>
      <div className={styles.grid}>
        {themes.map((card) => (
          <Link key={card.slug} href={`/experiences/${card.slug}`} className={styles.card}>
            <div className={styles.imgWrap}>
              <SafeImage
                src={card.image}
                alt={card.image_alt || card.name}
                fill
                sizes="(max-width: 576px) 90vw, (max-width: 1200px) 44vw, 30vw"
                className={styles.img}
              />
              <span className={styles.arrowBtn} aria-hidden="true">
                <ArrowUpRight size={18} className={styles.arrowIcon} />
              </span>
            </div>
            <div className={styles.cardBody}>
              <h3 className={styles.cardTitle}>{card.name}</h3>
              <p className={styles.cardDesc}>{card.description}</p>
            </div>
          </Link>
        ))}
      </div>
    </div>
  );
}
