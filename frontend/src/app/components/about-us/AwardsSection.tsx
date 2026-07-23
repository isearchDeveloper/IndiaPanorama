import Image from "next/image";
import Link from "next/link";
import styles from "./AwardsSection.module.css";

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export default function AwardsSection({ data }: { data: any }) {
  const awards: any[] = data?.awards ?? [];
  const heading = data?.content?.heading ?? "Awards and Recognitions";
  const showYear = data?.content?.show_year === "1";

  if (!awards.length) return null;

  const isBento = awards.length >= 5;

  return (
    <section className={styles.section}>
      <div className={styles.inner}>
        <div className={styles.headerRow}>
          <h2 className={styles.heading}>{heading}</h2>
          <Link href="/awards-achievements" className={styles.viewAllBtn}>View All</Link>
        </div>

        <div className={styles.grid}>
          {isBento ? (
            <>
              <div className={styles.colSide}>
                {awards.slice(0, 2).map((a: any) => <AwardCard key={a.id} award={a} showYear={showYear} />)}
              </div>
              <div className={styles.featuredCard}>
                {awards[2].banner_image && (
                  <div className={styles.featuredImgWrap}>
                    <Image src={awards[2].banner_image} alt={awards[2].title ?? ""} fill sizes="400px" className={styles.featuredImg} />
                  </div>
                )}
                <div className={styles.featuredText}>
                  <p className={styles.featuredTitle}>{awards[2].title}</p>
                  <p className={styles.featuredDesc}>{awards[2].description}</p>
                </div>
              </div>
              <div className={styles.colSide}>
                {awards.slice(3, 5).map((a: any) => <AwardCard key={a.id} award={a} showYear={showYear} />)}
              </div>
            </>
          ) : (
            <div className={styles.colSide}>
              {awards.map((a: any) => <AwardCard key={a.id} award={a} showYear={showYear} />)}
            </div>
          )}
        </div>
      </div>
    </section>
  );
}

function AwardCard({ award, showYear }: { award: any; showYear: boolean }) {
  return (
    <div className={styles.smallCard}>
      {award.banner_image && (
        <div className={styles.smallImgWrap}>
          <Image src={award.banner_image} alt={award.title ?? ""} fill sizes="100px" className={styles.smallImg} />
        </div>
      )}
      <div className={styles.smallText}>
        <p className={styles.smallTitle}>{award.title}</p>
        {award.description && <p className={styles.smallDesc}>{award.description}</p>}
        {showYear && award.award_year && <p className={styles.smallYear}>{award.award_year}</p>}
      </div>
    </div>
  );
}
