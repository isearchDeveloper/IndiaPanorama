import Image from "next/image";
import type { StatePopularExperienceItem } from "@/services/activitiesService";
import styles from "./StatePopularExperience.module.css";

interface Props {
  title: string;
  items: StatePopularExperienceItem[];
}

export default function StatePopularExperience({ title, items }: Props) {
  if (!items.length) return null;
  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{title}</h2>
      <div className={styles.grid}>
        {items.map((item, i) => {
          const isDark = i % 2 !== 0;
          return (
            <div key={i} className={`${styles.card} ${isDark ? styles.cardDark : ""}`}>
              <span className={styles.iconWrap}>
                {item.icon ? (
                  <Image
                    src={item.icon}
                    alt=""
                    aria-hidden="true"
                    width={36}
                    height={36}
                    className={styles.iconImg}
                  />
                ) : (
                  <svg className={styles.iconSvg} viewBox="0 0 32 32" fill="none" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round">
                    <circle cx="16" cy="10" r="5" /><path d="M8 28v-4a8 8 0 0116 0v4" />
                  </svg>
                )}
              </span>
              <div>
                <p className={styles.title}>{item.title}</p>
                <p className={styles.desc}>{item.description}</p>
              </div>
            </div>
          );
        })}
      </div>
    </section>
  );
}
