import Link from "next/link";
import Banner from "@/app/components/common/Banner";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import type { FaqItem } from "@/app/components/common/FaqSection";
import { CarFleetSection, CarWhyChoose, CarRoadTrips, RouteAbout, RouteHighlights } from "../shared";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import styles from "./layouts.module.css";
import routeStyles from "./CarRentalRouteLayout.module.css";

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export default function CarRentalRouteLayout({ data }: { data: any }) {
  const h1 = data.meta?.h1_heading?.trim() || data.banner?.title || "Car Rental";
  const fleetCategories = data.fleet?.categories ?? [];
  const stats = data.about?.stats;
  const highlights = data.highlights?.items ?? [];
  const popularLocations = data.popular_locations?.items ?? [];
  const routes = data.popular_routes?.items ?? data.routes?.items ?? [];
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
            { label: h1, href: `/car-rental/${data.slug ?? ""}` },
          ]} />

          <h1 className={styles.titleH1}>{h1}</h1>

          {data.short_description && (
            <ReadMoreHtml html={data.short_description} className={styles.shortDesc} />
          )}

          {/* About: image + description + trip stats */}
          {data.about && (
            <>
             
              <RouteAbout
                image={data.about.image}
                imageAlt={data.about.image_alt ?? h1}
                title={data.about.title}
                description={data.about.description}
                stats={stats}
              />
            </>
          )}

          {/* Route Highlights */}
          {highlights.length > 0 && (
            <>
              <RouteHighlights
                title={data.highlights?.title ?? "Route Highlights"}
                items={highlights}
              />
            </>
          )}

          {/* Fleet */}
          {fleetCategories.length > 0 && (
            <>
              <CarFleetSection categories={fleetCategories} />
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

          {/* Popular Routes */}
          {routes.length > 0 && (
            <>
              <div className={routeStyles.routesSection}>
                <h2 className={routeStyles.sectionHeading}>{data.popular_routes?.title ?? data.routes?.title ?? "Popular Routes"}</h2>
                <div className={routeStyles.routesTags}>
                  {routes.map((r: any) => (
                    <Link key={r.slug} href={`/car-rental/${r.slug}`} className={routeStyles.routeTag}>
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true"><path d="M3 12h18M13 5l7 7-7 7"/></svg>
                      {r.label}
                    </Link>
                  ))}
                </div>
              </div>
            </>
          )}

          {/* Popular Locations */}
          {popularLocations.length > 0 && (
            <>
              <div className={routeStyles.locationsSection}>
                <h2 className={routeStyles.sectionHeading}>{data.popular_locations?.title ?? "Popular Locations"}</h2>
                {data.popular_locations?.description && <p className={routeStyles.locationsDesc}>{data.popular_locations.description}</p>}
                <div className={routeStyles.locationsGrid}>
                  {popularLocations.map((item: any) => (
                    <Link key={item.slug} href={`/car-rental/${item.slug}`} className={routeStyles.locationCard}>
                      <span className={routeStyles.locationDot} aria-hidden="true" />
                      {item.label ?? item.title}
                      <span className={routeStyles.locationArrow} aria-hidden="true">›</span>
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
