"use client";

import { useState } from "react";
import type { LocationItem } from "@/types/carRental";
import styles from "./CarLocationGrid.module.css";

const INITIAL_COUNT = 9;
const LOAD_MORE_COUNT = 9;

const FALLBACK_LOCATIONS: LocationItem[] = [
  { title: "Car Rental In Chennai",               slug: "car-rental-in-chennai" },
  { title: "Golden Triangle Car Rental",          slug: "golden-triangle-car-rental" },
  { title: "Darjeeling Car Rental",               slug: "darjeeling-car-rental" },
  { title: "Tiruvannamalai Car Rental",           slug: "tiruvannamalai-car-rental" },
  { title: "Orcha Fort Car Rental",               slug: "orcha-fort-car-rental" },
  { title: "Car Rental to Jaipur Palace",         slug: "car-rental-to-jaipur-palace" },
  { title: "Car Rental to Nasik",                 slug: "car-rental-to-nasik" },
  { title: "Car Rental to Coorg",                 slug: "car-rental-to-coorg" },
  { title: "Car Rental to Buddhist Circuit",      slug: "car-rental-to-buddhist-circuit" },
  { title: "Car Rental to Ahmedabad",             slug: "car-rental-to-ahmedabad" },
  { title: "Car Rental to Vagamon",               slug: "car-rental-to-vagamon" },
  { title: "Car Rental to Konark Sun Temple",     slug: "car-rental-to-konark-sun-temple" },
  { title: "Car Rental to Meghalaya",             slug: "car-rental-to-meghalaya" },
  { title: "Car Rental to Nainital & Jim Corbett", slug: "car-rental-to-nainital-jimcorbett" },
  { title: "Car Rental Madurai to Rameshwaram",   slug: "car-rental-madurai-to-rameshwaram" },
  { title: "Car Rental Gokarna Beach",            slug: "car-rental-gokarna-beach" },
  { title: "Car Rental Kolkata Sundarbans",       slug: "car-rental-kolkata-sundarbans" },
];

interface Props {
  title: string;
  items: LocationItem[];
}

export default function CarLocationGrid({ title, items }: Props) {
  const locations = items.length > 0 ? items : FALLBACK_LOCATIONS;
  const [visibleCount, setVisibleCount] = useState(INITIAL_COUNT);

  const visible = locations.slice(0, visibleCount);
  const hasMore = visibleCount < locations.length;

  return (
    <section className={styles.section}>
      <div className={styles.inner}>
        <h2 className={styles.heading}>{title}</h2>
        <p className={styles.subtext}>
          Explore India&apos;s top car rental destinations with Indian Panorama and enjoy comfortable, reliable, and hassle-free travel wherever you go.
        </p>

        <div className={styles.grid}>
          {visible.map((loc) => {
            const href = loc.url ?? `/car-rental/${loc.slug}`;
            return (
              <a key={loc.slug} href={href} className={styles.link}>
                {loc.title ?? loc.label ?? loc.slug}
              </a>
            );
          })}
        </div>

        {hasMore && (
          <div className={styles.loadMore}>
            <button
              className={styles.loadMoreBtn}
              type="button"
              onClick={() => setVisibleCount((c) => c + LOAD_MORE_COUNT)}
            >
              Load More ↓
            </button>
          </div>
        )}
      </div>
    </section>
  );
}
