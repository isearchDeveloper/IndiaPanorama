"use client";

import Image from "next/image";
import Link from "next/link";
import styles from "./PackageCards.module.css";

const FALLBACK_IMAGE = "/images/about-banner-pages.jpg";

export interface PackageCardItem {
  title: string;
  slug: string;
  image?: string;
  image_alt?: string;
  price?: number | null;
  short_description?: string;
}

interface Props {
  heading: string;
  viewAllHref?: string;
  packages?: PackageCardItem[];
}

export default function PackageCards({ heading, viewAllHref = "/tour-packages", packages = [] }: Props) {
  if (packages.length === 0) return null;

  const shown = packages.slice(0, 3);

  return (
    <div className={styles.wrapper}>
      <div className={styles.header}>
        <h2 className={styles.heading}>{heading}</h2>
        <Link href={viewAllHref} className={styles.viewAll}>View All</Link>
      </div>

      <div className={styles.grid}>
        {shown.map((pkg) => (
          <Link key={pkg.slug} href={`/tour-packages/${pkg.slug}`} className={styles.card}>
            <div className={styles.imgWrap}>
              <Image
                src={pkg.image ?? FALLBACK_IMAGE}
                alt={pkg.image_alt ?? pkg.title}
                fill
                sizes="(max-width: 640px) 100vw, 33vw"
                className={styles.img}
              />
            </div>
            <div className={styles.body}>
              <p className={styles.title}>{pkg.title}</p>
              {pkg.price && (
                <p className={styles.meta}>From ₹{pkg.price.toLocaleString("en-IN")}</p>
              )}
            </div>
          </Link>
        ))}
      </div>
    </div>
  );
}
