import styles from "./FestivalQuickFacts.module.css";

type Festival = {
  slug: string; name: string; image: string; bannerImage?: string;
  description: string; tagline?: string; location: string; duration: string;
  bestTime: string; stateName?: string; categoryLabel?: string;
  month?: string; location_text?: string; month_text?: string;
};

interface Props {
  festival: Festival;
}

export default function FestivalQuickFacts({ festival }: Props) {
  const facts = [
    { label: "Duration", value: festival.duration },
    { label: "Best Time", value: festival.bestTime },
    { label: "Location", value: festival.location },
    { label: "Category", value: festival.categoryLabel },
    { label: "State", value: festival.stateName },
  ];

  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>Quick Facts</h2>
      <div className={styles.grid}>
        {facts.map((fact) => (
          <div key={fact.label} className={styles.card}>
            <span className={styles.label}>{fact.label}</span>
            <span className={styles.value}>{fact.value}</span>
          </div>
        ))}
      </div>
    </section>
  );
}

