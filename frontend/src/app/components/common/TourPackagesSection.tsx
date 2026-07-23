import styles from "./TourPackagesSection.module.css";

const FALLBACK_IMAGE = "/images/about-banner-pages.jpg";

type Package = {
  slug: string;
  title: string;
  primary_image?: string | null;
  primary_image_alt?: string;
  tag?: string;
  duration_days?: number | null;
  duration_nights?: number | null;
};

type BestTimeItem = {
  month_range?: string;
  title?: string;
  month?: string;
  season?: string;
  tagline?: string;
  description?: string;
  desc?: string;
};

type Props = {
  locationName: string;
  packages: Package[];
  bestTime?: BestTimeItem[];
  bestTimeTitle?: string;
};

export default function TourPackagesSection({
  locationName,
  packages,
  bestTime = [],
  bestTimeTitle,
}: Props) {
  return (
    <>
      {packages.length > 0 && (
        <section>
          <h2 className={styles.sectionHeading}>All {locationName} Tour Packages</h2>
          <div className={styles.packagesGrid}>
            {packages.map((pkg, i) => (
              <a
                key={pkg.slug ?? i}
                href={`/tour-packages/${pkg.slug}`}
                className={styles.pkgCard}
                aria-label={pkg.title}
              >
                <div className={styles.pkgImgWrap}>
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img
                    src={pkg.primary_image ?? FALLBACK_IMAGE}
                    alt={pkg.primary_image_alt ?? pkg.title}
                    className={styles.pkgImg}
                  />
                  {pkg.tag && (
                    <span className={styles.pkgTag}>{pkg.tag}</span>
                  )}
                </div>
                <div className={styles.pkgBody}>
                  <div className={styles.pkgTitleRow}>
                    <h3 className={styles.pkgTitle}>{pkg.title}</h3>
                    {(pkg.duration_days || pkg.duration_nights) && (
                      <span className={styles.pkgDuration}>
                        {pkg.duration_days ? `${pkg.duration_days}D` : ""}
                        {pkg.duration_days && pkg.duration_nights ? " / " : ""}
                        {pkg.duration_nights ? `${pkg.duration_nights}N` : ""}
                      </span>
                    )}
                  </div>
                  <div className={styles.pkgFooter}>
                    <span className={styles.pkgCta}>View Details</span>
                  </div>
                </div>
              </a>
            ))}
          </div>
        </section>
      )}

      {bestTime.length > 0 && (
        <section className={styles.bestTimeSection}>
          <h2 className={styles.sectionHeading}>
            {bestTimeTitle ?? `Best Time To Visit ${locationName}`}
          </h2>
          <div className={styles.bestTimeGrid}>
            {bestTime.map((item, i) => (
              <div key={i} className={styles.bestTimeCard}>
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img
                  src="/images/tick-double-04.svg"
                  alt=""
                  aria-hidden="true"
                  className={styles.bestTimeIcon}
                />
                <p className={styles.bestTimeTitle}>
                  {item.month_range ?? item.title ?? item.month ?? item.season ?? ""}
                </p>
                <p className={styles.bestTimeDesc}>
                  {item.tagline ?? item.description ?? item.desc ?? ""}
                </p>
              </div>
            ))}
          </div>
        </section>
      )}
    </>
  );
}
