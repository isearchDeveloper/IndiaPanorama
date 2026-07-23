import styles from "./CarFeaturesBenefits.module.css";

function CheckIcon() {
  return (
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true" className={styles.checkIcon}>
      <circle cx="12" cy="12" r="11" fill="#2e7d32" />
      <path d="M7 12.5l3.5 3.5 6.5-7" stroke="#fff" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

interface BulletListProps {
  title: string;
  items: string[];
}

function BulletList({ title, items }: BulletListProps) {
  return (
    <div className={styles.col}>
      <h3 className={styles.colHeading}>{title}</h3>
      <ul className={styles.list}>
        {items.map((item, i) => (
          <li key={i} className={styles.item}>
            <CheckIcon />
            <span>{item}</span>
          </li>
        ))}
      </ul>
    </div>
  );
}

interface Props {
  heading?: string;
  description?: string;
  features?: { title: string | null; items: string[] };
  benefits?: { title: string | null; items: string[] };
}

export default function CarFeaturesBenefits({ heading, description, features, benefits }: Props) {
  const hasFeatures = features?.items && features.items.length > 0;
  const hasBenefits = benefits?.items && benefits.items.length > 0;

  if (!hasFeatures && !hasBenefits) return null;

  return (
    <section className={styles.section}>
      {heading && <h2 className={styles.sectionHeading}>{heading}</h2>}
      {description && <p className={styles.sectionDesc}>{description}</p>}
      <div className={styles.wrapper}>
        {hasFeatures && (
          <BulletList
            title={features!.title ?? "Features"}
            items={features!.items}
          />
        )}
        {hasBenefits && (
          <BulletList
            title={benefits!.title ?? "Benefits"}
            items={benefits!.items}
          />
        )}
      </div>
    </section>
  );
}
