import type { TripInfo } from "@/types/carRental";
import styles from "./CarTripInfoBox.module.css";

interface Props {
  info: TripInfo;
}

const rows = [
  { key: "distance" as const, label: "Distance" },
  { key: "duration" as const, label: "Duration" },
  { key: "route" as const, label: "Route" },
  { key: "best_season" as const, label: "Best Season" },
];

export default function CarTripInfoBox({ info }: Props) {
  return (
    <div className={styles.box}>
      {rows.map(({ key, label }) =>
        info[key] ? (
          <div key={key} className={styles.row}>
            <span className={styles.label}>{label}</span>
            <span className={styles.value}>{info[key]}</span>
          </div>
        ) : null
      )}
    </div>
  );
}
