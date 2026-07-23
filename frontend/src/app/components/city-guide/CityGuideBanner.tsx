import styles from "./CityGuideBanner.module.css";

type CityGuideBannerProps = {
  title: string;
  description?: string;
  image: string;
};

export default function CityGuideBanner({ title, description, image }: CityGuideBannerProps) {
  return (
    <section
      className={styles.hero}
      style={{ backgroundImage: `url(${image || "/images/about-banner-pages.jpg"})` }}
    >
      {/* <div className={styles.overlay} /> */}
      <div className={styles.content}>
        {/* <div>{title}</div> */}
        {description && <p>{description}</p>}
      </div>
    </section>
  );
}
