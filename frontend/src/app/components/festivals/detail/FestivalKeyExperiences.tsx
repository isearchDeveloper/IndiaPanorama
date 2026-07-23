import styles from "./FestivalKeyExperiences.module.css";

interface Item {
  icon: string | null;
  label: string;
}

interface Props {
  title: string;
  items: Item[];
}

export default function FestivalKeyExperiences({ title, items }: Props) {
  if (!items.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>
      <div className={styles.grid}>
        {items.map((exp, i) => (
          <div key={i} className={styles.card}>
            {exp.icon && (
              // eslint-disable-next-line @next/next/no-img-element
              <img
                src={exp.icon}
                alt=""
                aria-hidden="true"
                className={styles.icon}
                loading="lazy"
              />
            )}
            <span className={styles.label}>{exp.label}</span>
          </div>
        ))}
      </div>
    </section>
  );
}
