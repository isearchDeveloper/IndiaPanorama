import Banner from "@/app/components/common/Banner";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import type { BreadcrumbItem } from "@/app/components/common/Breadcrumb";
type ExperienceSpot = {
  title: string;
  tagline?: string | null;
  description: string;
  images: string[];
  highlights: string[];
  bestTime?: string | null;
  duration?: string | null;
  entryFee?: string | null;
  location?: string | null;
  faqs: { id: number; question: string; answer: string }[];
  relatedSpots: RelatedSpotItem[];
};
import type { RelatedSpot as RelatedSpotItem } from "./RelatedSpots";
import RelatedSpots from "./RelatedSpots";
import Image from "next/image";
import styles from "./ExperienceDetailLayout.module.css";

interface Props {
  spot: ExperienceSpot;
  breadcrumbs: BreadcrumbItem[];
}

export default function ExperienceDetailLayout({ spot, breadcrumbs }: Props) {
  const faqItems = spot.faqs.map((f) => ({
    id: f.id,
    question: f.question,
    answer: f.answer,
  }));

  return (
    <>
      <Banner
        title={spot.title}
        subtitle={spot.tagline ?? undefined}
        bgImage={spot.images[0] ?? "/images/about-banner-pages.jpg"}
      />

      <div className={styles.contentLayout}>
        <div className={styles.leftCol}>
          <Breadcrumb items={breadcrumbs} />

          {/* ── About — description API se HTML aati hai ── */}
          <section className={styles.aboutSection}>
            <h1 className={styles.h1}>{spot.title}</h1>
            <div
              className={`cms-content cms-intro ${styles.description}`}
              dangerouslySetInnerHTML={{ __html: spot.description ?? "" }}
            />
          </section>


          {/* ── Quick Info — sirf jo values aayein wahi cards ── */}
          {(spot.bestTime || spot.duration || spot.entryFee || spot.location) && (
            <section className={styles.infoSection}>
              <h2 className={styles.sectionHeading}>Quick Information</h2>
              <div className={styles.infoGrid}>
                {spot.bestTime && (
                  <div className={styles.infoCard}>
                    <span className={styles.infoLabel}>Best Time to Visit</span>
                    <span className={styles.infoValue}>{spot.bestTime}</span>
                  </div>
                )}
                {spot.duration && (
                  <div className={styles.infoCard}>
                    <span className={styles.infoLabel}>Duration</span>
                    <span className={styles.infoValue}>{spot.duration}</span>
                  </div>
                )}
                {spot.entryFee && (
                  <div className={styles.infoCard}>
                    <span className={styles.infoLabel}>Entry Fee</span>
                    <span className={styles.infoValue}>{spot.entryFee}</span>
                  </div>
                )}
                {spot.location && (
                  <div className={styles.infoCard}>
                    <span className={styles.infoLabel}>Location</span>
                    <span className={styles.infoValue}>{spot.location}</span>
                  </div>
                )}
              </div>
            </section>
          )}


          {/* ── Image Gallery ── */}
          {spot.images.length > 1 && (
            <>
              <section className={styles.gallerySection}>
                <h2 className={styles.sectionHeading}>Photo Gallery</h2>
                <div className={styles.gallery}>
                  {spot.images.map((src, i) => (
                    <div key={i} className={styles.galleryImg}>
                      <Image
                        src={src}
                        alt={`${spot.title} — photo ${i + 1}`}
                        fill
                        sizes="(max-width: 640px) 100vw, 50vw"
                        className={styles.galleryImgEl}
                      />
                    </div>
                  ))}
                </div>
              </section>
            </>
          )}

          {/* ── Highlights ── */}
          {spot.highlights.length > 0 && (
            <>
              <section className={styles.highlightsSection}>
                <h2 className={styles.sectionHeading}>Highlights</h2>
                <ul className={styles.highlightList}>
                  {spot.highlights.map((h) => (
                    <li key={h} className={styles.highlightItem}>
                      <span className={styles.checkIcon}>✓</span>
                      {h}
                    </li>
                  ))}
                </ul>
              </section>
            </>
          )}

          {/* ── FAQ ── */}
          {faqItems.length > 0 && (
            <FaqSection
              heading={`FAQs — ${spot.title}`}
              items={faqItems}
            />
          )}
        </div>

        <aside className={styles.rightCol}>
          <SidebarForm />
        </aside>
      </div>

      {/* ── Related Experiences ── */}
      {spot.relatedSpots.length > 0 && (
        <RelatedSpots spots={spot.relatedSpots} />
      )}
    </>
  );
}
