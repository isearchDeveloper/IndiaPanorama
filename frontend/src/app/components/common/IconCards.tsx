import Image from "next/image";
import styles from "./IconCards.module.css";

export type IconCardItem = {
  id: number;
  title: string;
  description: string;
};

type Props = {
  heading: string;
  items: IconCardItem[];
};

export default function IconCards({ heading, items }: Props) {
  if (!items.length) return null;

  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>{heading}</h2>
      <div className={styles.grid}>
        {items.map((item) => (
          <div key={item.id} className={styles.card}>
            <div className={styles.iconWrap}>
              <Image
                src="/images/tick-double-04.svg"
                alt=""
                width={40}
                height={40}
                className={styles.icon}
              />
            </div>
            <h3 className={styles.title}>{item.title}</h3>
            <div className={`${styles.desc} cms-content`} dangerouslySetInnerHTML={{ __html: item.description ?? "" }} />
          </div>
        ))}
      </div>
    </section>
  );
}
