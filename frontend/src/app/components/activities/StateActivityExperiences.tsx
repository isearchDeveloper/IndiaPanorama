import type { ReactElement } from "react";
import styles from "./StateActivityExperiences.module.css";

type ActivityExperience = { title: string; description: string; icon: string; dark?: boolean };

interface Props { stateName: string; experiences: ActivityExperience[]; }

const icons: Record<string, ReactElement> = {
  boat: (
    <svg viewBox="0 0 32 32" fill="none" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round">
      <path d="M4 22l4-10h16l4 10H4z" /><path d="M16 12V6" /><path d="M10 6h12" /><path d="M2 26c4 2 8 2 14 0s10-2 14 0" />
    </svg>
  ),
  mountain: (
    <svg viewBox="0 0 32 32" fill="none" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round">
      <path d="M2 28L12 8l6 10 4-6 8 16H2z" /><path d="M20 10l2-4" />
    </svg>
  ),
  leaf: (
    <svg viewBox="0 0 32 32" fill="none" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round">
      <path d="M6 26C6 16 14 6 26 6c0 10-8 20-20 20z" /><path d="M6 26l10-10" />
    </svg>
  ),
  culture: (
    <svg viewBox="0 0 32 32" fill="none" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round">
      <circle cx="16" cy="10" r="5" /><path d="M8 28v-4a8 8 0 0116 0v4" /><path d="M4 28h24" />
    </svg>
  ),
  wildlife: (
    <svg viewBox="0 0 32 32" fill="none" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round">
      <ellipse cx="16" cy="18" rx="8" ry="6" /><circle cx="11" cy="11" r="3" /><circle cx="21" cy="11" r="3" />
      <path d="M13 17c0 1 1.3 2 3 2s3-1 3-2" /><circle cx="13.5" cy="17" r="0.8" fill="currentColor" stroke="none" /><circle cx="18.5" cy="17" r="0.8" fill="currentColor" stroke="none" />
    </svg>
  ),
  beach: (
    <svg viewBox="0 0 32 32" fill="none" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round">
      <path d="M4 24h24" /><path d="M8 24C8 16 16 8 26 10" /><circle cx="22" cy="10" r="4" /><path d="M2 28h28" />
    </svg>
  ),
  desert: (
    <svg viewBox="0 0 32 32" fill="none" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round">
      <path d="M2 24c4-8 8-4 12-12 4 8 8 4 12 12H2z" /><circle cx="16" cy="8" r="4" />
    </svg>
  ),
};

export default function StateActivityExperiences({ stateName, experiences }: Props) {
  if (!experiences || experiences.length === 0) return null;

  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>Popular Experience in {stateName}</h2>
      <div className={styles.grid}>
        {experiences.map((exp, i) => (
          <div key={i} className={`${styles.card} ${exp.dark ? styles.cardDark : ""}`}>
            <span className={styles.icon}>{icons[exp.icon] ?? icons.culture}</span>
            <div>
              <p className={styles.title}>{exp.title}</p>
              <p className={styles.desc}>{exp.description}</p>
            </div>
          </div>
        ))}
      </div>
    </section>
  );
}

