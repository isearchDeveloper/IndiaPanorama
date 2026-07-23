import { GiMountains, GiPalmTree, GiSunrise, GiDesert, GiIndiaGate, GiForest } from "react-icons/gi";
import styles from "./RegionCards.module.css";

type RegionCard = { id: number; title: string; slug: string; icon: string; description: string; href: string; };

type Props = { heading: string; items: RegionCard[]; };

const ZONE_ICONS: Record<string, React.ReactNode> = {
  north:      <GiMountains size={44} color="#3d6b35" />,
  south:      <GiPalmTree  size={44} color="#3d6b35" />,
  east:       <GiSunrise   size={44} color="#3d6b35" />,
  west:       <GiDesert    size={44} color="#3d6b35" />,
  center:     <GiIndiaGate size={44} color="#3d6b35" />,
  "north-east": <GiForest  size={44} color="#3d6b35" />,
};

const STATIC_REGIONS: RegionCard[] = [
  { id: 1, title: "North Zone",     slug: "north",      icon: "north",      description: "Browse cities by zone for easier navigation.", href: "#" },
  { id: 2, title: "South Zone",     slug: "south",      icon: "south",      description: "Browse cities by zone for easier navigation.", href: "#" },
  { id: 3, title: "East Zone",      slug: "east",       icon: "east",       description: "Browse cities by zone for easier navigation.", href: "#" },
  { id: 4, title: "West Zone",      slug: "west",       icon: "west",       description: "Browse cities by zone for easier navigation.", href: "#" },
  { id: 5, title: "Center Zone",    slug: "center",     icon: "center",     description: "Browse cities by zone for easier navigation.", href: "#" },
  { id: 6, title: "North East Zone",slug: "north-east", icon: "north-east", description: "Browse cities by zone for easier navigation.", href: "#" },
];

export default function RegionCards({ heading, items }: Props) {
  const data = items.length > 0 ? items : STATIC_REGIONS;

  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{heading}</h2>
      <div className={styles.grid}>
        {data.map((item) => (
          <div key={item.id} className={styles.card}>
            <span className={styles.icon}>
              {ZONE_ICONS[item.slug] ?? ZONE_ICONS["center"]}
            </span>
            <h3 className={styles.title}>{item.title}</h3>
            <p className={styles.desc}>{item.description}</p>
            <span className={styles.link}>Explore Now →</span>
          </div>
        ))}
      </div>
    </section>
  );
}
