import { TbMountain, TbBeach, TbTrees, TbSunset, TbCompass, TbLeaf } from "react-icons/tb";
import styles from "./ZoneWise.module.css";

const zones = [
  { id: 1, name: "North Zone", description: "Explore the Himalayas, heritage forts, and spiritual rivers of North India.", href: "/city-guide?zone=north", Icon: TbMountain },
  { id: 2, name: "South Zone", description: "Discover temple towns, backwaters, and pristine beaches of South India.", href: "/city-guide?zone=south", Icon: TbCompass },
  { id: 3, name: "East Zone", description: "Uncover tribal culture, wildlife sanctuaries, and the Bay of Bengal coast.", href: "/city-guide?zone=east", Icon: TbBeach },
  { id: 4, name: "West Zone", description: "Experience royal deserts, vibrant festivals, and cosmopolitan coastal cities.", href: "/city-guide?zone=west", Icon: TbSunset },
  { id: 5, name: "Center Zone", description: "Journey through ancient dynasties, dense forests, and iconic river ghats.", href: "/city-guide?zone=center", Icon: TbTrees },
  { id: 6, name: "North East Zone", description: "Venture into India's hidden paradise of misty hills, monasteries, and tea gardens.", href: "/city-guide?zone=northeast", Icon: TbLeaf },
];

interface Props {
  heading?: string;
  subtext?: string;
}

export default function ZoneWise({
  heading = "Zone Wise",
  subtext = "Browse cities by zone for easier navigation.",
}: Props) {
  return (
    <div className={styles.wrapper}>
      <h2 className={styles.heading}>{heading}</h2>
      <p className={styles.subtext}>{subtext}</p>
      <div className={styles.grid}>
        {zones.map(({ id, name, description, href, Icon }) => (
          <a key={id} href="#" className={styles.card}>
            <div className={styles.iconWrap}>
              <Icon className={styles.icon} />
            </div>
            <h3 className={styles.zoneName}>{name}</h3>
            <p className={styles.zoneDesc}>{description}</p>
            <span className={styles.link}>Explore Now →</span>
          </a>
        ))}
      </div>
    </div>
  );
}
