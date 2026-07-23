import type { Metadata } from "next";
import Banner from "@/app/components/common/Banner";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import FaqAccordion from "./FaqAccordion";
import { FAQ_CATEGORIES } from "./FaqData";
import styles from "./page.module.css";

export const metadata: Metadata = {
  title: "Frequently Asked Questions | Indian Panorama",
  description:
    "Find answers to all your questions about booking India tours with Indian Panorama — covering payments, cancellations, visas, hotels, flights, safety, and more.",
  openGraph: {
    title: "FAQ | Indian Panorama — India Tour Operator",
    description:
      "Answers to common questions about booking tours to India — visas, payments, hotels, safety, customisation, and more.",
    type: "website",
    url: "https://www.indianpanorama.in/faq",
  },
  twitter: {
    card: "summary",
    title: "FAQ | Indian Panorama",
    description:
      "All your questions about India tour packages answered — booking, payments, visa, hotels, safety, and more.",
  },
  alternates: { canonical: "https://www.indianpanorama.in/faq" },
  robots: { index: true, follow: true },
};

const totalQuestions = FAQ_CATEGORIES.reduce((acc, cat) => acc + cat.items.length, 0);

export default function FaqPage() {
  return (
    <div className={styles.page}>
      <Banner
        title="Frequently Asked Questions"
        subtitle="Everything you need to know before planning your dream India journey"
        bgImage="/images/india-taj-faq.png"
      />

      <div className={styles.container}>
        <div className={styles.breadcrumbRow}>
          <Breadcrumb
            items={[
              { label: "Home", href: "/" },
              { label: "FAQ", href: "/faq" },
            ]}
          />
        </div>

        {/* Page Header */}
        <div className={styles.pageHeader}>
          <div className={styles.headerText}>
            <span className={styles.badge}>Travel Answers</span>
            <h1 className={styles.pageTitle}>Your Questions, Answered</h1>
            <p className={styles.pageIntro}>
              Planning a trip to India can feel overwhelming — but it doesn&apos;t have to be. We&apos;ve
              gathered the questions our guests ask most often about booking with Indian Panorama,
              travelling through India, and making the most of every journey. Browse by category or
              scroll to find what you need.
            </p>
            <div className={styles.statsRow}>
              <div className={styles.stat}>
                <span className={styles.statNum}>{totalQuestions}+</span>
                <p className={styles.statLabel}>Questions Answered</p>
              </div>
              <div className={styles.stat}>
                <span className={styles.statNum}>{FAQ_CATEGORIES.length}</span>
                <p className={styles.statLabel}>Categories</p>
              </div>
              <div className={styles.stat}>
                <span className={styles.statNum}>24/7</span>
                <p className={styles.statLabel}>Expert Support</p>
              </div>
            </div>
          </div>

          <div className={styles.headerImg} aria-hidden="true">
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img
              src="/images/faq-side-image.webp"
              alt=""
              loading="lazy"
              decoding="async"
            />
          </div>
        </div>

        {/* ── Noscript fallback — all questions visible without JS ── */}
        <noscript>
          <div className={styles.noscriptFallback}>
            {FAQ_CATEGORIES.map((cat) => (
              <div key={cat.id} className={styles.noscriptCategory} id={cat.id}>
                <h2 className={styles.noscriptCatTitle}>
                  {cat.icon} {cat.title}
                </h2>
                {cat.items.map((item) => (
                  <div key={item.id} className={styles.noscriptItem}>
                    <h3 className={styles.noscriptQ}>{item.question}</h3>
                    <p className={styles.noscriptA}>{item.answer}</p>
                  </div>
                ))}
              </div>
            ))}
          </div>
        </noscript>

        {/* ── Interactive accordion (client component) ── */}
        <div className={styles.accordionArea}>
          <FaqAccordion categories={FAQ_CATEGORIES} />
        </div>

        {/* ── Still have questions CTA ── */}
        <div className={styles.ctaSection}>
          <div className={styles.ctaCard}>
            <span className={styles.ctaCardIcon}>💬</span>
            <h2 className={styles.ctaCardTitle}>Still Have Questions?</h2>
            <p className={styles.ctaCardText}>
              Our travel experts are available 7 days a week to answer any question about planning
              your India tour. Whether you need help with an itinerary, a specific destination, or
              booking logistics — we are here for you.
            </p>
            <a href="/contact-us" className={`${styles.ctaCardBtn} ${styles.ctaCardBtnPrimary}`}>
              Talk to an Expert
            </a>
          </div>

          <div className={styles.ctaCard}>
            <span className={styles.ctaCardIcon}>📋</span>
            <h2 className={styles.ctaCardTitle}>Ready to Start Planning?</h2>
            <p className={styles.ctaCardText}>
              Tell us your travel dates, dream destinations, and group size. Our specialists will
              craft a personalised India itinerary for you within 24 hours — completely free, with
              no obligation to book.
            </p>
            <a href="/tour-packages" className={styles.ctaCardBtn}>
              Explore Tour Packages
            </a>
          </div>
        </div>

      </div>
    </div>
  );
}
