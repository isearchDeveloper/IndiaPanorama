"use client";

import { useState } from "react";
import Image from "next/image";
import Link from "next/link";
import type { FleetCategory, CarItem } from "@/types/carRental";
import styles from "./CarFleetSection.module.css";

const FALLBACK = "/images/about-banner-pages.jpg";

interface Props {
  categories: FleetCategory[];
  activeTab?: string;
}

function SeatsIcon() {
  return (
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <circle cx="12" cy="6" r="4" fill="currentColor" />
      <path d="M4 20c0-4.418 3.582-8 8-8s8 3.582 8 8" fill="currentColor" />
    </svg>
  );
}

function FuelIcon() {
  return (
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <path d="M4 22V6a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v3l2-2v9a2 2 0 0 1-2 2h-1v2H5v-2H4z" stroke="currentColor" strokeWidth="1.8" strokeLinejoin="round" />
      <rect x="7" y="8" width="6" height="4" rx="0.5" fill="currentColor" />
    </svg>
  );
}

function CarIcon() {
  return (
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <path d="M5 11l1.5-4.5A2 2 0 0 1 8.4 5h7.2a2 2 0 0 1 1.9 1.5L19 11" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" />
      <rect x="2" y="11" width="20" height="7" rx="2" stroke="currentColor" strokeWidth="1.8" />
      <circle cx="7" cy="18" r="2" fill="currentColor" />
      <circle cx="17" cy="18" r="2" fill="currentColor" />
    </svg>
  );
}

export default function CarFleetSection({ categories }: Props) {
  const [activeTab, setActiveTab] = useState<string>(categories[0]?.slug ?? "all");

  if (!categories || categories.length === 0) return null;

  const activeCategory = categories.find((c) => c.slug === activeTab) ?? categories[0];
  const cars: CarItem[] = activeCategory?.cars ?? [];

  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>Our Best Batch Of Fleets</h2>

      <div className={styles.tabsWrapper}>
        <nav className={styles.tabs} aria-label="Fleet categories">
          {categories.map((cat) => {
            const isActive = activeTab === cat.slug;
            return (
              <button
                key={cat.slug}
                type="button"
                className={`${styles.tab} ${isActive ? styles.tabActive : ""}`}
                aria-current={isActive ? "true" : undefined}
                onClick={() => setActiveTab(cat.slug)}
              >
                {cat.name}
              </button>
            );
          })}
        </nav>
      </div>

      <div className={styles.grid}>
        {cars.map((car) => (
          <Link key={car.slug} href={`/car-rental/${car.slug}`} className={styles.card}>
            <div className={styles.imgWrap}>
              <Image
                src={car.primary_image || FALLBACK}
                alt={car.primary_image_alt || car.title}
                fill
                sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
                className={styles.img}
              />
              <span className={styles.arrowBtn} aria-hidden="true">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                  <path d="M7 17L17 7M17 7H7M17 7v10" stroke="#fff" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round" />
                </svg>
              </span>
            </div>
            <div className={styles.body}>
              <h3 className={styles.carTitle}>{car.title}</h3>
              <div className={styles.specs}>
                <span className={styles.spec}>
                  <SeatsIcon />
                  {car.seats} Seats
                </span>
                <span className={styles.spec}>
                  <FuelIcon />
                  {car.fuel_type}
                </span>
                {car.category?.name && (
                  <span className={styles.spec}>
                    <CarIcon />
                    {car.category.name}
                  </span>
                )}
              </div>
            </div>
          </Link>
        ))}

        {cars.length === 0 && (
          <p className={styles.empty}>No vehicles in this category.</p>
        )}
      </div>
    </section>
  );
}
