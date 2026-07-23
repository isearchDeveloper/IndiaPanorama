import { TbMap2, TbPlaneDeparture, TbTrophy, TbCar, TbBuildingHospital, TbHeadset } from "react-icons/tb";
import type { IconType } from "react-icons";
import styles from "./WhyChooseUs.module.css";

const reasons: { Icon: IconType; title: string; desc: string }[] = [
  { Icon: TbMap2,             title: "Expert Local Knowledge", desc: "Our guides have decades of on-the-ground experience across every Indian state." },
  { Icon: TbPlaneDeparture,   title: "End-to-End Planning",    desc: "From flights to hotels to local sightseeing — we handle every detail for you." },
  { Icon: TbTrophy,           title: "Award-Winning Service",  desc: "National tourism award winners with 25+ years of trust from travellers worldwide." },
  { Icon: TbCar,              title: "Private Transport",       desc: "150+ well-maintained vehicles and professional drivers across India." },
  { Icon: TbBuildingHospital, title: "Handpicked Hotels",       desc: "1,500+ verified hotels from boutique heritage stays to luxury resorts." },
  { Icon: TbHeadset,          title: "24/7 Support",            desc: "Our travel desk is always available — before, during, and after your journey." },
];

export default function WhyChooseUs() {
  return (
    <section className={styles.section}>
      <div className={styles.inner}>
        <div className={styles.header}>
          <h2 className={styles.heading}>Why Plan With Indian Panorama?</h2>
          <p className={styles.subtext}>
            Over 25 years of expertise, national awards, and thousands of happy travellers make us
            India's most trusted tour operator.
          </p>
        </div>
        <div className={styles.grid}>
          {reasons.map((r) => (
            <div key={r.title} className={styles.card}>
              <r.Icon className={styles.icon} />
              <h3 className={styles.title}>{r.title}</h3>
              <p className={styles.desc}>{r.desc}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
