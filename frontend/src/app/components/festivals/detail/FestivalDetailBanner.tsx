import styles from "./FestivalDetailBanner.module.css";

interface Props {
  title: string;
  image: string;
  imageAlt: string | null;
  stateName?: string | null;
}

export default function FestivalDetailBanner({ title, image, imageAlt, stateName }: Props) {
  return (
    <section className={styles.banner}>
      {/* eslint-disable-next-line @next/next/no-img-element */}
      <img
        src={image}
        alt={imageAlt ?? title}
        className={styles.bannerImg}
        fetchPriority="high"
      />
      <div className={styles.bannerOverlay} />
      <div className={styles.bannerContent}>
        <p className={styles.bannerTitle}>{title}</p>
        {stateName && <p className={styles.bannerSub}>{stateName}</p>}
      </div>
    </section>
  );
}
