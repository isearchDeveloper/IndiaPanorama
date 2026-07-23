import Link from "next/link";
import Banner from "@/app/components/common/Banner";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import type { FaqItem } from "@/app/components/common/FaqSection";
import {
  CarFleetSection,
  CarGallery,
  CarWhyChoose,
  CarFeaturesBenefits,
  CarRoadTrips,
} from "../shared";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import styles from "./layouts.module.css";
import cityStyles from "./CarRentalCityLayout.module.css";

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export default function CarRentalCityLayout({ data }: { data: any }) {
  const h1 = data.meta?.h1_heading?.trim() || data.banner?.title || "Car Rental";
  const bannerTitle = data.meta?.h1_heading?.trim() ? "" : (data.banner?.title || "");
  const fleetCategories = data.fleet?.categories ?? [];

  const faqItems: FaqItem[] = (data.faqs?.items ?? []).map((f: any, i: number) => ({
    id: i + 1,
    question: f.question,
    answer: f.answer,
  }));

  return (
    <>
      <Banner bgImage={data.banner?.image ?? "/images/about-banner-pages.jpg"} title={bannerTitle} />

      <div className={styles.contentLayout}>
        <div className={styles.leftCol}>
          <Breadcrumb
            items={[
              { label: "Home", href: "/" },
              { label: "Car Rental", href: "/car-rental" },
              { label: h1, href: `/car-rental/${data.slug}` },
            ]}
          />

          <h1 className={styles.titleH1}>{h1}</h1>

          {data.short_description && (
            <ReadMoreHtml html={data.short_description} className={styles.longDesc} />
          )}
          {(data.gallery?.length ?? 0) > 0 && (
            <>
              <CarGallery images={data.gallery} />
            </>
          )}


          {((data.features?.items?.length ?? 0) > 0 || (data.benefits?.items?.length ?? 0) > 0) && (
            <>
              <CarFeaturesBenefits features={data.features} benefits={data.benefits} />
            </>
          )}

          {fleetCategories.length > 0 && (
            <>
              <CarFleetSection categories={fleetCategories} />
            </>
          )}

          <>
            <CarWhyChoose
              title={data?.why_choose?.title}
              description={data?.why_choose?.description}
              stats={data?.why_choose?.stats}
            />
          </>

          {(data.popular_locations?.items?.length ?? 0) > 0 && (
            <>
              <div className={cityStyles.locationsSection}>
                <h2 className={cityStyles.sectionHeading}>{data.popular_locations.title}</h2>
                {data.popular_locations.description && (
                  <p className={cityStyles.locationsDesc}>{data.popular_locations.description}</p>
                )}
                <div className={cityStyles.locationsGrid}>
                  {data.popular_locations.items.map((item: any) => (
                    <Link key={item.slug} href={`/car-rental/${item.slug}`} className={cityStyles.locationCard}>
                      <span className={cityStyles.locationDot} aria-hidden="true" />
                      {item.label}
                      <span className={cityStyles.locationArrow} aria-hidden="true">›</span>
                    </Link>
                  ))}
                </div>
              </div>
            </>
          )}

          {(data.routes?.items?.length ?? 0) > 0 && (
            <>
              <div className={cityStyles.routesSection}>
                <h2 className={cityStyles.routesHeading}>{data.routes!.title}</h2>
                <div className={cityStyles.routesTags}>
                  {data.routes!.items.map((item: any) => (
                    <Link key={item.slug} href={item.url ?? `/car-rental/${item.slug}`} className={cityStyles.routeTag}>
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
                        <path d="M3 12h18M13 5l7 7-7 7"/>
                      </svg>
                      {item.label}
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

      {(data.road_trips?.items?.length ?? 0) > 0 && (
        <CarRoadTrips
          title={data.road_trips.title}
          subtitle={data.road_trips.subtitle}
          items={data.road_trips.items}
        />
      )}
    </>
  );
}
