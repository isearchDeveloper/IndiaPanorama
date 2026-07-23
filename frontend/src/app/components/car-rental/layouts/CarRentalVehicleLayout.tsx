import Link from "next/link";
import Banner from "@/app/components/common/Banner";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import CarGallery from "../CarGallery";
import { CarWhyChoose, CarRoadTrips } from "../shared";
import type { FaqItem } from "@/app/components/common/FaqSection";
import Image from "next/image";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import styles from "./layouts.module.css";
import vehicleStyles from "./CarRentalVehicleLayout.module.css";

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export default function CarRentalVehicleLayout({ data }: { data: any }) {
  const h1 = data.meta?.h1_heading?.trim() || data.banner?.title || "Car Rental";
  const about = data.about_car;
  const spec = data.specification;
  const features = data.features?.items ?? [];
  const gallery = about?.gallery?.images ?? [];
  const highlights: { icon?: string; label?: string; text?: string }[] = (about?.highlights ?? []).map((h: any) =>
    typeof h === "string"
      ? { text: h }
      : { icon: h.icon, label: h.label ?? h.title, text: h.text ?? h.value ?? "" }
  );
  const popularLocations = data.popular_locations?.items ?? [];

  const specItems = spec?.items
    ? Object.entries(spec.items)
        // null/empty values wale specs skip — "null" text na dikhe
        .filter(([, val]) => val !== null && val !== undefined && String(val).trim() !== "" && String(val).toLowerCase() !== "null")
        .map(([key, val]) => ({
          label: key.replace(/_/g, " ").replace(/\b\w/g, (c) => c.toUpperCase()),
          value: String(val),
        }))
    : [];

  const roadTripItems = (data.road_trips?.items ?? []).map((i: any) => ({
    title: i.state ?? i.title ?? "",
    image: i.image ?? "",
    image_alt: i.image_alt ?? "",
    rating: i.rating,
    destinations: i.route_text ?? i.destinations ?? "",
    duration_days: i.duration_days,
    duration_nights: i.duration_nights,
  }));

  const faqItems: FaqItem[] = (data.faqs?.items ?? []).map((f: any, i: number) => ({
    id: i + 1,
    question: f.question,
    answer: f.answer,
  }));

  return (
    <>
      <Banner bgImage={data.banner?.image ?? "/images/about-banner-pages.jpg"} title="" />

      <div className={styles.contentLayout}>
        <div className={styles.leftCol}>
          <Breadcrumb items={[
            { label: "Home", href: "/" },
            { label: "Car Rental", href: "/car-rental" },
            { label: h1, href: "#" },
          ]} />

          <h1 className={styles.titleH1}>{h1}</h1>

          {data.short_description && (
            <ReadMoreHtml html={data.short_description} className={styles.shortDesc} />
          )}

          {/* About car: image + description + highlights */}
          {about && (
            <>
              <div className={vehicleStyles.aboutRow}>
                {about.image && (
                  <div className={vehicleStyles.aboutImgWrap}>
                    <Image
                      src={about.image}
                      alt={about.image_alt ?? h1}
                      fill
                      sizes="340px"
                      className={vehicleStyles.aboutImg}
                    />
                  </div>
                )}
                <div className={vehicleStyles.aboutContent}>
                  {about.title && <h2 className={vehicleStyles.aboutHeading}>{about.title}</h2>}
                  {about.description && (
                    <ReadMoreHtml html={about.description} className={vehicleStyles.aboutDesc} />
                  )}
                  {/* 2-column label/value grid with divider — Ertiga design */}
                  {highlights.length > 0 && (
                    <div className={vehicleStyles.highlightsGrid}>
                      {highlights.map((h, i) => (
                        <div key={i} className={vehicleStyles.highlightBox}>
                          {h.label && <span className={vehicleStyles.highlightLabel}>{h.label}</span>}
                          <span className={vehicleStyles.highlightText}>{h.text}</span>
                        </div>
                      ))}
                    </div>
                  )}
                </div>
              </div>
            </>
          )}

          {/* Gallery */}
          {gallery.length > 0 && (
            <>
              <CarGallery images={gallery} />
            </>
          )}

          {/* Features */}
          {features.length > 0 && (
            <>
              <div className={vehicleStyles.featuresSection}>
                <h2 className={vehicleStyles.sectionHeading}>{data.features?.title ?? "Features & Amenities"}</h2>
                <div className={vehicleStyles.featuresGrid}>
                  {features.map((f: any, i: number) => (
                    <div key={i} className={vehicleStyles.featureCard}>
                      {f.icon && (
                        // eslint-disable-next-line @next/next/no-img-element
                        <img src={f.icon} alt="" width={24} height={24} aria-hidden="true" className={vehicleStyles.featureImg} />
                      )}
                      <div>
                        <p className={vehicleStyles.featureLabel}>{f.label}</p>
                        {f.description && <p className={vehicleStyles.featureDesc}>{f.description}</p>}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </>
          )}

          {/* Specifications */}
          {specItems.length > 0 && (
            <>
              <div className={vehicleStyles.specsSection}>
                <h2 className={vehicleStyles.sectionHeading}>{spec?.title ?? "Vehicle Specifications"}</h2>
                {spec?.description && <p className={vehicleStyles.specsDesc}>{spec.description}</p>}
                <div className={vehicleStyles.specsGrid}>
                  {specItems.map((s, i) => (
                    <div key={i} className={vehicleStyles.specItem}>
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" className={vehicleStyles.specCheck}>
                        <rect x="1" y="1" width="22" height="22" rx="5" fill="#22c03c" />
                        <path d="M7 12.5l3.5 3.5 6.5-7" stroke="#fff" strokeWidth="2.4" strokeLinecap="round" strokeLinejoin="round" />
                      </svg>
                      <span className={vehicleStyles.specText}>
                        <span className={vehicleStyles.specLabel}>{s.label}:</span> {s.value}
                      </span>
                    </div>
                  ))}
                </div>
              </div>
            </>
          )}

          {/* Why Choose */}
          <>
            <CarWhyChoose
              title={data?.why_choose?.title}
              description={data?.why_choose?.description}
              stats={data?.why_choose?.stats}
            />
          </>

          {/* Popular Locations */}
          {popularLocations.length > 0 && (
            <>
              <div className={vehicleStyles.locationsSection}>
                <h2 className={vehicleStyles.sectionHeading}>{data.popular_locations?.title ?? "Popular Locations"}</h2>
                {data.popular_locations?.description && (
                  <p className={vehicleStyles.locationsDesc}>{data.popular_locations.description}</p>
                )}
                <div className={vehicleStyles.locationsGrid}>
                  {popularLocations.map((item: any) => (
                    <Link key={item.slug} href={`/car-rental/${item.slug}`} className={vehicleStyles.locationCard}>
                      <span className={vehicleStyles.locationDot} aria-hidden="true" />
                      {item.label ?? item.title}
                      <span className={vehicleStyles.locationArrow} aria-hidden="true">›</span>
                    </Link>
                  ))}
                </div>
              </div>
            </>
          )}
        </div>

        <aside className={styles.rightCol} aria-label="Enquiry form">
          <SidebarForm />
        </aside>
      </div>

      {faqItems.length > 0 && (
        <FaqSection
          heading={data.faqs?.title ?? "Frequently Asked Questions"}
          items={faqItems}
          sideImage={{ src: "/images/faq-side-image.webp", alt: h1 }}
        />
      )}

      {roadTripItems.length > 0 && (
        <CarRoadTrips
          title={data.road_trips?.title ?? "Popular Road Trip Destinations"}
          subtitle={data.road_trips?.subtitle ?? ""}
          items={roadTripItems}
        />
      )}
    </>
  );
}
