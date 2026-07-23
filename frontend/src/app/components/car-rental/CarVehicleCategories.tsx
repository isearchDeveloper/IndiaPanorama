import type { VehicleCategory } from "@/types/carRental";
import styles from "./CarVehicleCategories.module.css";

function SedanIcon() {
  return (
    <svg width="36" height="36" viewBox="0 0 48 48" fill="none" aria-hidden="true">
      <path d="M8 28l4-10a3 3 0 0 1 2.8-2h18.4a3 3 0 0 1 2.8 2l4 10" stroke="currentColor" strokeWidth="2" strokeLinecap="round" />
      <rect x="4" y="28" width="40" height="10" rx="3" stroke="currentColor" strokeWidth="2" />
      <circle cx="13" cy="38" r="3" fill="currentColor" />
      <circle cx="35" cy="38" r="3" fill="currentColor" />
      <path d="M14 22h20" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" />
    </svg>
  );
}

function SuvIcon() {
  return (
    <svg width="36" height="36" viewBox="0 0 48 48" fill="none" aria-hidden="true">
      <path d="M6 26l3-9a3 3 0 0 1 2.8-2h20.4a3 3 0 0 1 2.8 2l3 9" stroke="currentColor" strokeWidth="2" strokeLinecap="round" />
      <rect x="3" y="26" width="42" height="11" rx="3" stroke="currentColor" strokeWidth="2" />
      <circle cx="13" cy="37" r="3.5" fill="currentColor" />
      <circle cx="35" cy="37" r="3.5" fill="currentColor" />
      <path d="M14 20h20M6 30h36" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" />
    </svg>
  );
}

function BusIcon() {
  return (
    <svg width="36" height="36" viewBox="0 0 48 48" fill="none" aria-hidden="true">
      <rect x="4" y="8" width="40" height="26" rx="3" stroke="currentColor" strokeWidth="2" />
      <path d="M4 18h40" stroke="currentColor" strokeWidth="1.5" />
      <path d="M24 8v26" stroke="currentColor" strokeWidth="1.5" />
      <circle cx="12" cy="38" r="3" fill="currentColor" />
      <circle cx="36" cy="38" r="3" fill="currentColor" />
      <path d="M4 30h40" stroke="currentColor" strokeWidth="1.5" />
    </svg>
  );
}

function TravellerIcon() {
  return (
    <svg width="36" height="36" viewBox="0 0 48 48" fill="none" aria-hidden="true">
      <rect x="4" y="12" width="36" height="22" rx="3" stroke="currentColor" strokeWidth="2" />
      <path d="M40 18h5v10h-5" stroke="currentColor" strokeWidth="2" strokeLinecap="round" />
      <path d="M4 20h36" stroke="currentColor" strokeWidth="1.5" />
      <circle cx="12" cy="37" r="3" fill="currentColor" />
      <circle cx="32" cy="37" r="3" fill="currentColor" />
      <path d="M4 28h36" stroke="currentColor" strokeWidth="1.5" />
      <rect x="8" y="14" width="6" height="5" rx="1" stroke="currentColor" strokeWidth="1.2" />
      <rect x="18" y="14" width="6" height="5" rx="1" stroke="currentColor" strokeWidth="1.2" />
      <rect x="28" y="14" width="6" height="5" rx="1" stroke="currentColor" strokeWidth="1.2" />
    </svg>
  );
}

function CoachIcon() {
  return (
    <svg width="36" height="36" viewBox="0 0 48 48" fill="none" aria-hidden="true">
      <rect x="2" y="10" width="44" height="24" rx="3" stroke="currentColor" strokeWidth="2" />
      <path d="M2 20h44" stroke="currentColor" strokeWidth="1.5" />
      <path d="M2 28h44" stroke="currentColor" strokeWidth="1.5" />
      <circle cx="10" cy="37" r="3" fill="currentColor" />
      <circle cx="38" cy="37" r="3" fill="currentColor" />
      <rect x="6" y="12" width="7" height="7" rx="1" stroke="currentColor" strokeWidth="1.2" />
      <rect x="17" y="12" width="7" height="7" rx="1" stroke="currentColor" strokeWidth="1.2" />
      <rect x="28" y="12" width="7" height="7" rx="1" stroke="currentColor" strokeWidth="1.2" />
      <rect x="39" y="12" width="5" height="7" rx="1" stroke="currentColor" strokeWidth="1.2" />
    </svg>
  );
}

const ICONS = [SedanIcon, SuvIcon, BusIcon, TravellerIcon, CoachIcon, SuvIcon, BusIcon];

interface Props {
  title?: string;
  categories: VehicleCategory[];
}

export default function CarVehicleCategories({ title, categories }: Props) {
  if (!categories.length) return null;

  return (
    <section className={styles.section}>
      {title && <h2 className={styles.heading}>{title}</h2>}
      <div className={styles.grid}>
        {categories.map((cat, i) => {
          const Icon = ICONS[i % ICONS.length];
          return (
            <div key={cat.slug} className={styles.chip}>
              <span className={styles.icon}>
                {cat.icon ? (
                  // eslint-disable-next-line @next/next/no-img-element
                  <img src={cat.icon} alt={cat.icon_alt ?? cat.name} width={36} height={36} />
                ) : (
                  <Icon />
                )}
              </span>
              <span className={styles.label}>{cat.name}</span>
            </div>
          );
        })}
      </div>
    </section>
  );
}
