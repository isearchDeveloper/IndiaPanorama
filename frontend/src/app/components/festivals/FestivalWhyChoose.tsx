import Image from "next/image";
import styles from "./FestivalWhyChoose.module.css";

interface WhyItem {
  title: string;
  description?: string;
}

// universal why-choose section — festivals, experiences, etc. sab jagah reuse hota hai
interface Props {
  items?: WhyItem[];
  heading?: string;
  subtext?: string;
}

const defaultItems: WhyItem[] = [
  {
    title: "Authentic Cultural Experiences",
    description: "Participate in local traditions with genuine cultural interactions.",
  },
  {
    title: "Expert Festival Guides",
    description: "Travel with knowledgeable guides who bring every celebration to life.",
  },
  {
    title: "Handpicked Accommodations",
    description: "Stay in carefully selected hotels close to festival venues.",
  },
  {
    title: "Custom Festival Itineraries",
    description: "Flexible travel plans designed around your interests.",
  },
  {
    title: "Safe & Hassle-Free Travel",
    description: "End-to-end travel arrangements and local support.",
  },
  {
    title: "24/7 Assistance",
    description: "Dedicated travel experts available throughout your journey.",
  },
];

export default function FestivalWhyChoose({ items = defaultItems, heading, subtext }: Props) {
  if (!items || items.length === 0) return null;

  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{heading ?? "Why Experience Festivals With Indian Panorama"}</h2>
      {subtext && (
        <div
          className={`${styles.subtext} cms-content`}
          dangerouslySetInnerHTML={{ __html: subtext }}
        />
      )}
      <div className={styles.grid}>
        {items.map((item, i) => (
          <div key={i} className={styles.card}>
            <Image
              src="/images/tick-double-04.svg"
              alt="tick"
              width={36}
              height={36}
              className={styles.icon}
            />
            <h3 className={styles.title}>{item.title}</h3>
            {/* description optional hai; CMS se HTML bhi ho sakti hai */}
            {item.description && (
              <div
                className={`${styles.desc} cms-content`}
                dangerouslySetInnerHTML={{ __html: item.description }}
              />
            )}
          </div>
        ))}
      </div>
    </section>
  );
}
