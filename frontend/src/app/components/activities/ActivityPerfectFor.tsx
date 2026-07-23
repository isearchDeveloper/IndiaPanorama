import type { ActivityPerfectForItem } from "@/services/activitiesService";
import styles from "./ActivityPerfectFor.module.css";


const svgIcons: Record<string, React.ReactNode> = {
  wildlife: (
    <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round">
      <ellipse cx="20" cy="22" rx="10" ry="8" />
      <circle cx="14" cy="14" r="4" /><circle cx="26" cy="14" r="4" />
      <circle cx="13" cy="28" r="2" /><circle cx="27" cy="28" r="2" />
      <path d="M17 20c0 1.1.9 2 3 2s3-.9 3-2" />
      <circle cx="16" cy="21" r="1" fill="currentColor" stroke="none" />
      <circle cx="24" cy="21" r="1" fill="currentColor" stroke="none" />
    </svg>
  ),
  adventure: (
    <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round">
      <path d="M20 6l3 8h8l-6.5 5 2.5 8L20 22l-7 5 2.5-8L9 14h8z" />
      <path d="M20 22v10" />
    </svg>
  ),
  family: (
    <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round">
      <circle cx="14" cy="12" r="4" /><circle cx="26" cy="12" r="4" /><circle cx="20" cy="26" r="3" />
      <path d="M6 32c0-5 4-8 8-8" /><path d="M34 32c0-5-4-8-8-8" />
      <path d="M14 24c1.5-1 3.5-1.5 6-1.5" />
    </svg>
  ),
  camera: (
    <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round">
      <rect x="6" y="14" width="28" height="20" rx="3" />
      <circle cx="20" cy="24" r="5" />
      <path d="M14 14l2-4h8l2 4" />
      <circle cx="30" cy="18" r="1.5" fill="currentColor" stroke="none" />
    </svg>
  ),
  globe: (
    <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round">
      <path d="M20 6C14 6 8 12 8 20c0 4 2 7 4 9" />
      <path d="M20 6c6 0 12 6 12 14 0 4-2 7-4 9" />
      <path d="M20 6v28" /><path d="M8 20h24" />
      <path d="M10 13c3 2 6 3 10 3s7-1 10-3" />
      <path d="M10 27c3-2 6-3 10-3s7 1 10 3" />
    </svg>
  ),
};

const iconKeys = Object.keys(svgIcons);

interface Props {
  title: string;
  // description optional — experiences theme pages pe label ke niche text aata hai
  items: (ActivityPerfectForItem & { description?: string })[];
}

export default function ActivityPerfectFor({ title, items }: Props) {
  if (!items.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>
      <div className={styles.row}>
        {items.map((item, i) => (
          <div key={item.title} className={styles.card}>
            <span className={styles.icon}>
              {item.icon ? (
                /* eslint-disable-next-line @next/next/no-img-element */
                <img src={item.icon} alt="" aria-hidden="true" className={styles.iconImg} loading="lazy" />
              ) : (
                svgIcons[iconKeys[i % iconKeys.length]]
              )}
            </span>
            <span className={styles.label}>{item.title}</span>
            {item.description && (
              <span className={styles.itemDesc}>{item.description}</span>
            )}
          </div>
        ))}
      </div>
    </section>
  );
}
