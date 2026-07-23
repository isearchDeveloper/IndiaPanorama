import SidebarForm from "@/app/components/common/SidebarForm";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import FaqSection from "@/app/components/common/FaqSection";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import PackageHero from "./PackageHero";
import DestinationCovered from "./DestinationCovered";
import ItineraryAccordion from "./ItineraryAccordion";
import RelatedPackages from "./RelatedPackages";
import styles from "./PackageDetailLayout.module.css";

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export default function PackageDetailLayout({ data }: { data: any }) {
  const banner      = data.banner      ?? {};
  const destData    = data.destination_covered ?? {};
  const similar     = data.similar_packages    ?? [];
  const faqsData    = data.faqs        ?? {};
  const itineraries = data.itineraries ?? [];

  const days   = banner.duration_days   ?? 0;
  const nights = banner.duration_nights ?? 0;
  const duration = `${days} Day${days !== 1 ? "s" : ""} / ${nights} Night${nights !== 1 ? "s" : ""}`;

  // Images: primary + gallery
  const allImages: { src: string; alt: string }[] = [];
  if (banner.primary_image) {
    allImages.push({ src: banner.primary_image, alt: banner.primary_image_alt ?? banner.title });
  }
  (banner.images ?? []).forEach((img: { image_path: string; image_alt: string }) => {
    allImages.push({ src: img.image_path, alt: img.image_alt ?? banner.title });
  });

  // Destinations covered
  const destinations = (destData.items ?? []).map((d: any) => ({
    title: d.name,
    highlights: d.highlights ?? "",
  }));

  // Itinerary
  const itinerary = itineraries.map((item: any, i: number) => ({
    day: i + 1,
    title: item.title ?? `Day ${i + 1}`,
    html: item.details ?? "",
  }));

  // FAQs
  const faqItems = (faqsData.items ?? []).map((f: any, i: number) => ({
    id: i + 1,
    question: f.question ?? f.title ?? "",
    answer: f.answer ?? f.description ?? "",
  }));

  // Similar packages
  const relatedPackages = similar.map((p: any) => ({
    slug: p.slug,
    title: p.title,
    image: p.primary_image ?? null,
    image_alt: p.primary_image_alt ?? p.title,
    duration_days: p.duration_days ?? null,
    duration_nights: p.duration_nights ?? null,
    location: p.location?.name ?? "",
  }));

  // Breadcrumb
  const breadcrumb = [
    { label: "Home",          href: "/" },
    { label: "Tour Packages", href: "/tour-packages" },
    { label: banner.title,    href: `/tour-packages/${banner.slug}` },
  ];

  return (
    <>
      <PackageHero
        title={banner.title ?? ""}
        duration={duration}
        images={allImages}
      />

      <div className={styles.contentLayout}>
        <div className={styles.leftCol}>
          <Breadcrumb items={breadcrumb} />

          {/* About — tour highlights from backend, ~100 words then Read More */}
          {banner.tour_highlights && (
            <section className={styles.aboutBlock}>
              <h2 className={styles.aboutHeading}>About {banner.title}</h2>
              <ReadMoreHtml
                html={banner.tour_highlights}
                className={styles.aboutText}
                lines={5}
              />
            </section>
          )}

          {/* Destination Covered */}
          <DestinationCovered destinations={destinations} description={destData.description} />

          {/* Itinerary */}
          {itinerary.length > 0 && (
            <>
              <ItineraryAccordion itinerary={itinerary} />
            </>
          )}
        </div>

        <aside className={styles.rightCol}>
          <SidebarForm />
        </aside>
      </div>

      {/* FAQs */}
      {faqItems.length > 0 && (
        <FaqSection
          heading={faqsData.title ?? "Frequently Asked Questions"}
          items={faqItems}
          sideImage={{ src: "/images/faq-side-image.webp", alt: banner.title }}
        />
      )}

      {/* Similar Packages */}
      {relatedPackages.length > 0 && (
        <RelatedPackages
          packages={relatedPackages}
          heading="Similar Packages"
        />
      )}
    </>
  );
}
