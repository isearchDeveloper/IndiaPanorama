import Image from "next/image";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import styles from "./CarWhyChoose.module.css";

const ICON_LIST = [FleetIcon, PersonIcon, AwardIcon, ClientIcon, StarIcon];

function FleetIcon() {
  return (
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <path d="M5 11l1.5-4.5A2 2 0 0 1 8.4 5h7.2a2 2 0 0 1 1.9 1.5L19 11" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" />
      <rect x="2" y="11" width="20" height="7" rx="2" stroke="currentColor" strokeWidth="1.8" />
      <circle cx="7" cy="18" r="2" fill="currentColor" />
      <circle cx="17" cy="18" r="2" fill="currentColor" />
    </svg>
  );
}

function PersonIcon() {
  return (
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <circle cx="12" cy="6" r="4" fill="currentColor" />
      <path d="M4 20c0-4.418 3.582-8 8-8s8 3.582 8 8" fill="currentColor" />
    </svg>
  );
}

function AwardIcon() {
  return (
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <circle cx="12" cy="9" r="5" stroke="currentColor" strokeWidth="1.8" />
      <path d="M8.5 14.5L7 22l5-3 5 3-1.5-7.5" stroke="currentColor" strokeWidth="1.8" strokeLinejoin="round" />
    </svg>
  );
}

function ClientIcon() {
  return (
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <circle cx="9"  cy="7"  r="3.5" stroke="currentColor" strokeWidth="1.8" />
      <circle cx="17" cy="7"  r="3.5" stroke="currentColor" strokeWidth="1.8" />
      <path d="M2 20c0-3.314 3.134-6 7-6" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" />
      <path d="M10 20c0-3.314 3.134-6 7-6s7 2.686 7 6" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" />
    </svg>
  );
}

function StarIcon() {
  return (
    <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
      <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
    </svg>
  );
}

interface ApiStat {
  icon?: string | null;
  value?: string | null;
  label: string;
}

interface Props {
  title?: string;
  description?: string;
  stats?: ApiStat[];
}

export default function CarWhyChoose({ title, description, stats }: Props) {
  const hasStats = Array.isArray(stats) && stats.length > 0;

  return (
    <section className={styles.section}>
      {title && <h2 className={styles.heading}>{title}</h2>}
      {description && (
        <ReadMoreHtml html={description} className={styles.description} />
      )}

      {hasStats && (
        <div className={styles.statsGrid}>
          {stats!.map((stat, i) => {
            const FallbackIcon = ICON_LIST[i % ICON_LIST.length];
            return (
              <div key={`${stat.label}-${i}`} className={styles.statCard}>
                <div className={styles.statIcon}>
                  {stat.icon
                    ? <Image src={stat.icon} alt="" width={32} height={32} className={styles.statImg} />
                    : <FallbackIcon />
                  }
                </div>
                <div className={styles.statLabel}>
                  {stat.value && <span className={styles.statValue}>{stat.value}</span>}
                  <span>{stat.label}</span>
                </div>
              </div>
            );
          })}
        </div>
      )}
    </section>
  );
}
