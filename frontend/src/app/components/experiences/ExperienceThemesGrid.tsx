import Image from "next/image";
import Link from "next/link";
import type { ExpTheme } from "@/data/experiences/rootPageData";
import styles from "./ExperienceThemesGrid.module.css";

// root themes + theme-page sub-themes dono ke liye — extra fields optional
export interface ThemeGridItem extends ExpTheme {
  toursCount?: string;
  popularTag?: string;
}

interface Props {
  heading: string;
  themes: ThemeGridItem[];
  viewAllHref?: string;
  /** card link = `${basePath}/${slug}` — default root themes */
  basePath?: string;
  /** "View Tours →" line dikhani ho to (theme pages) */
  showViewTours?: boolean;
  /** slug ke baad append hota hai — state context ke liye e.g. "-in-kerala" */
  linkSuffix?: string;
}

export default function ExperienceThemesGrid({
  heading,
  themes,
  viewAllHref,
  basePath = "/experiences",
  showViewTours = false,
  linkSuffix = "",
}: Props) {
  if (!themes.length) return null;

  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{heading}</h2>

      <div className={styles.grid}>
        {themes.map((t) => {
          // If basePath does not contain /experiences (i.e. is state/city details context), append -experience suffix
          const isDetailPath = !basePath.includes("/experiences");
          const href = basePath.includes("?section=")
            ? basePath
            : `${basePath}/${t.slug}${isDetailPath ? "-experience" : linkSuffix}`;
          return (
            <Link key={t.slug} href={href} className={styles.card}>
              <div className={styles.imgWrap}>
                <Image
                  src={t.image}
                  alt={t.image_alt || ""}
                  fill
                  sizes="(max-width: 640px) 100vw, 50vw"
                  className={styles.img}
                />
                {t.toursCount ? (
                  <span className={styles.toursBadge}>{t.toursCount}</span>
                ) : (
                  <span className={styles.arrowBadge} aria-hidden="true">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.4" strokeLinecap="round" strokeLinejoin="round">
                      <path d="M7 17L17 7" /><path d="M8 7h9v9" />
                    </svg>
                  </span>
                )}
              </div>
              <div className={styles.body}>
                <h3 className={styles.name}>{t.name}</h3>
                <div
                  className={`${styles.desc} cms-content`}
                  dangerouslySetInnerHTML={{ __html: t.description }}
                />
                {t.popularTag && (
                  <p className={styles.popular}>
                    <strong>Popular:</strong> {t.popularTag}
                  </p>
                )}
                {showViewTours && <span className={styles.viewTours}>View Tours →</span>}
              </div>
            </Link>
          );
        })}
      </div>

      {viewAllHref && (
        <div className={styles.viewAllWrap}>
          <Link href={viewAllHref} className={styles.viewAll}>View All</Link>
        </div>
      )}
    </section>
  );
}
