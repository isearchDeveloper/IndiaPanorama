import Image from "next/image";
import Link from "next/link";
import { RiMapPinLine, RiTeamLine, RiQuestionAnswerLine, RiSuitcaseLine } from "react-icons/ri";
import type { HomeData } from "@/services/homeService";
import styles from "./AboutUs.module.css";

const iconMap: Record<string, React.ReactNode> = {
  "fas fa-map-marker-alt": <RiMapPinLine />,
  "fas fa-users":          <RiTeamLine />,
  "fas fa-comment-alt":    <RiQuestionAnswerLine />,
  "fas fa-briefcase":      <RiSuitcaseLine />,
};

interface Props {
  data: HomeData["trusted_operator"] | null;
}

export default function AboutUs({ data }: Props) {
  if (!data) return null;

  const paragraphs = data.description
    .split(/\r?\n\r?\n/)
    .map((p) => p.trim())
    .filter(Boolean);

  const sortedFeatures = [...data.features].sort((a, b) => a.sort_order - b.sort_order);

  return (
    <section className={styles.section}>
      <div className={styles.container}>

        {/* Left Column */}
        <div className={styles.leftColumn}>
          <h2 className={styles.heading}>{data.title}</h2>
          {paragraphs.map((para, i) => (
            <p key={i} className={styles.paragraph}>{para}</p>
          ))}
          <Link href={data.button_url || "/contact-us"} className={styles.bookBtn}>
            {data.button_text || "Book Now"}
          </Link>
        </div>

        {/* Right Column */}
        <div className={styles.rightColumn}>
          {data.master_text && (
            <p className={styles.topText}>{data.master_text}</p>
          )}

          <div className={styles.featureList}>
            {sortedFeatures.map((feature, i) => (
              <div key={i} className={styles.featureItem}>
                <span className={styles.featureIcon}>
                  {iconMap[feature.icon_class] ?? <RiMapPinLine />}
                </span>
                <div className={styles.featureContent}>
                  <h3 className={styles.featureTitle}>{feature.title}</h3>
                  <p className={styles.featureDesc}>{feature.description}</p>
                </div>
              </div>
            ))}
          </div>
        </div>

      </div>

      <div className={styles.bgImage}>
        <Image
          src="/images/bg-home-about.png"
          alt="Indian Monuments and Culture"
          width={1920}
          height={600}
          priority
          style={{ width: "100%", height: "auto", objectFit: "contain" }}
        />
      </div>
    </section>
  );
}
