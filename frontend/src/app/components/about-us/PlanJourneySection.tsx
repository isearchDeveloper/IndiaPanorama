import Image from "next/image";
import Link from "next/link";
import styles from "./PlanJourneySection.module.css";

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export default function PlanJourneySection({ data }: { data: any }) {
  if (!data) return null;

  const { heading, subheading, cta_label, cta_url, bg_image, bg_image_alt, cards = [] } = data.content;

  return (
    <section className={styles.section}>
      <div className={styles.bgWrap}>
        <Image src={bg_image ?? "/images/about-banner-pages.jpg"} alt={bg_image_alt ?? ""} fill className={styles.bgImg} priority sizes="100vw" />
        <div className={styles.bgOverlay} />
      </div>

      <div className={styles.decoLeft}>
        <Image src="/images/person-waking.png" alt="" fill className={styles.decoImgFlip} sizes="260px" />
      </div>
      <div className={styles.decoRight}>
        <Image src="/images/elephant-above.png" alt="" fill className={styles.decoImg} sizes="420px" />
      </div>

      <div className={styles.inner}>
        <div className={styles.headingRow}>
          <div className={styles.headingBlock}>
            {heading && <h2 className={styles.heading}>{heading}</h2>}
            {subheading && <p className={styles.subtext}>{subheading}</p>}
          </div>
          {cta_label && (
            <div className={styles.ctaBlock}>
              <Link href={cta_url ?? "/"} className={styles.ctaBtn}>{cta_label}</Link>
            </div>
          )}
        </div>

        {cards.length > 0 && (
          <div className={styles.cardsGrid}>
            {cards.map((card: any, idx: number) => (
              <div key={idx} className={styles.card}>
                {card.image && (
                  <div className={styles.cardImgWrap}>
                    <Image src={card.image} alt={card.image_alt ?? ""} fill className={styles.cardImg} sizes="(max-width: 640px) 90vw, (max-width: 1024px) 45vw, 25vw" />
                  </div>
                )}
                <div className={styles.cardBody}>
                  <h3 className={styles.cardTitle}>{card.title}</h3>
                  <div className={styles.cardRule} />
                  {card.description && <p className={styles.cardDesc}>{card.description}</p>}
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </section>
  );
}
