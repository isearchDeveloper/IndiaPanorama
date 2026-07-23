import type { Metadata } from "next";
import Banner from "@/app/components/common/Banner";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import styles from "./TourPackagesPage.module.css";
import categoryStyles from "./TourCategories.module.css";
import contentStyles from "./TourContent.module.css";
import { fetchTourPackageDetails } from "@/services/tourspackages";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";

export async function generateMetadata(): Promise<Metadata> {
  const data = await fetchTourPackageDetails();
  const meta = data?.holiday?.meta;

  return {
    title: meta?.meta_title ?? "India Tour Packages | Luxury, Adventure & Budget Tours",
    description: meta?.meta_description ?? "Explore India with Indian Panorama — premium, customised India tour packages since 1995.",
    keywords: meta?.meta_keywords ?? undefined,
    alternates: { canonical: "https://www.indianpanorama.in/tour-packages" },
    robots: { index: true, follow: true },
    openGraph: {
      title: meta?.meta_title ?? "India Tour Packages | Indian Panorama",
      description: meta?.meta_description ?? "Discover the best India tour packages.",
      url: "https://www.indianpanorama.in/tour-packages",
      siteName: "Indian Panorama",
      type: "website",
    },
  };
}

export default async function TourPackagesPage() {
  const data = await fetchTourPackageDetails();

  const h1title = data?.holiday?.meta?.h1_heading;
  const details = data?.holiday?.details;
  const faqs = data?.holiday?.faqs;
  const states: any[] = data?.states ?? [];

  const faqItems = faqs?.list?.map((f: any, i: number) => ({
    id: i + 1,
    question: f.question,
    answer: f.answer,
  })) ?? [];

  return (
    <>
      <Banner
        title={details?.banner_title ?? "All India Tour Packages"}
        subtitle={details?.banner_description ?? ""}
        bgImage={details?.banner_image ?? "/images/about-banner-pages.jpg"}
      />

      <div className={styles.contentLayout}>
        <div className={styles.leftCol}>

          <Breadcrumb items={[
            { label: "Home", href: "/" },
            { label: "Tour Packages", href: "/tour-packages" },
          ]} />
          <h1 className={styles.titleh1}>{h1title ? h1title : details?.banner_title}</h1>

          {/* ── Tour Categories Grid ── */}
          {states.length > 0 && (
            <section className={categoryStyles.section}>
              {details?.short_description && (
                <div className={categoryStyles.sectionHeader}>
                  <ReadMoreHtml html={details.short_description} className={`cms-intro ${categoryStyles.description}`} />
                </div>
              )}

              <div className={categoryStyles.grid}>
                {states.map((state) => (
                  <a
                    key={state.name}
                    href={`/${state.slug}/tour-packages`}
                    className={categoryStyles.card}
                    aria-label={state.name}
                  >
                    <div className={categoryStyles.imgWrap}>
                      {/* eslint-disable-next-line @next/next/no-img-element */}
                      <img
                        src={state.banner_image ?? "/images/about-banner-pages.jpg"}
                        alt={state.banner_image_alt ?? state.name}
                        className={categoryStyles.img}
                      />
                      {state.packages_count > 0 && (
                        <span className={categoryStyles.badge}>{state.packages_count} Tours</span>
                      )}
                    </div>
                    <div className={categoryStyles.cardBody}>
                      <span className={categoryStyles.cardTitle}>{state.name}</span>
                    </div>
                  </a>
                ))}
              </div>
            </section>
          )}


          {/* ── Long description ── */}
          {details?.long_description && (
            <div className={contentStyles.section}>
              <ReadMoreHtml html={details.long_description} className={`cms-intro ${contentStyles.body}`} lines={8} />
            </div>
          )}

        </div>

        <aside className={styles.rightCol} aria-label="Enquiry form">
          <SidebarForm />
        </aside>
      </div>

      {/* ── FAQ Section ── */}
      {faqItems.length > 0 && (
        <FaqSection
          heading={faqs?.faq_title ?? "FAQ's"}
          subtext="Find answers to the most common questions about our India tour packages, travel planning, bookings, accommodations, and customized holiday experiences."
          items={faqItems}
          sideImage={{ src: "/images/faq-side-image.webp", alt: "India Taj Mahal" }}
        />
      )}

    </>
  );
}
