import Link from "next/link";
import Image from "next/image";
import Banner from "@/app/components/common/Banner";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import type { FaqItem } from "@/app/components/common/FaqSection";
import { CarFleetSection, CarWhyChoose, CarRoadTrips, PackageTourOverview, PackageHighlights } from "../shared";
import RouteAbout from "../RouteAbout";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import styles from "./layouts.module.css";
import pkgStyles from "./CarRentalPackageLayout.module.css";

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export default function CarRentalPackageLayout({ data }: { data: any }) {
  const h1 = data.meta?.h1_heading?.trim() || data.banner?.title || "Car Rental Package";
  const fleetCategories = data.fleet?.categories ?? [];
  const about = data.about;
  const highlights = data.highlights;
  const features = data.features?.items ?? [];
  const routes = data.routes?.items ?? [];
  const popularLocations = data.popular_locations?.items ?? [];

  const faqItems: FaqItem[] = (data.faqs?.items ?? []).map((f: any, i: number) => ({
    id: i + 1,
    question: f.question,
    answer: f.answer,
  }));

  const roadTripItems = (data.road_trips?.items ?? []).map((i: any) => ({
    title: i.state ?? i.title ?? "",
    image: i.image ?? "",
    image_alt: i.image_alt ?? "",
    rating: i.rating,
    destinations: i.route_text ?? i.destinations ?? "",
    duration_days: i.duration_days,
    duration_nights: i.duration_nights,
  }));

  const overviewItems = about?.stats ? [
    { label: "Duration",          value: about.stats.duration },
    { label: "Best Time To Visit", value: about.stats.best_time_to_visit },
    { label: "Route",             value: about.stats.route },
    { label: "Ideal For",         value: about.stats.ideal_for },
  ].filter(v => v.value) : [];

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

          {/* About: image left + content right */}
          {about && (
            <RouteAbout
              image={about.image}
              imageAlt={about.image_alt ?? h1}
              title={about.title}
              description={about.description}
            />
          )}

          {/* Tour Overview */}
          {overviewItems.length > 0 && (
            <>

              <PackageTourOverview items={overviewItems} />
            </>
          )}

          {/* Route Highlights */}
          {highlights?.groups?.length > 0 && (
            <>
             
              <PackageHighlights
                heading={highlights.title}
                groups={highlights.groups}
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

          {/* Features & Amenities */}
          {features.length > 0 && (
            <>
              <div className={pkgStyles.amenitiesSection}>
                <h2 className={pkgStyles.sectionHeading}>{data.features?.title ?? "Features & Amenities"}</h2>
                <div className={pkgStyles.amenitiesGrid}>
                  {features.map((f: any, i: number) => (
                    <div key={i} className={pkgStyles.amenityCard}>
                      {f.icon && (
                        <span className={pkgStyles.amenityIcon} aria-hidden="true">
                          <Image src={f.icon} alt="" width={32} height={32} className={pkgStyles.amenityImg} />
                        </span>
                      )}
                      <div>
                        <p className={pkgStyles.amenityLabel}>{f.label}</p>
                        {f.description && <p className={pkgStyles.amenityDesc}>{f.description}</p>}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </>
          )}

          {/* Popular Routes */}
          {routes.length > 0 && (
            <>
              <div className={pkgStyles.routesSection}>
                <h2 className={pkgStyles.sectionHeading}>{data.routes?.title ?? "Popular Routes"}</h2>
                <div className={pkgStyles.routesTags}>
                  {routes.map((r: any) => (
                    <Link key={r.slug} href={`/car-rental/${r.slug}`} className={pkgStyles.routeTag}>
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
              <div className={pkgStyles.locationsSection}>
                <h2 className={pkgStyles.sectionHeading}>{data.popular_locations?.title ?? "Popular Locations"}</h2>
                {data.popular_locations?.description && (
                  <p className={pkgStyles.locationsDesc}>{data.popular_locations.description}</p>
                )}
                <div className={pkgStyles.locationsGrid}>
                  {popularLocations.map((item: any) => (
                    <Link key={item.slug} href={`/car-rental/${item.slug}`} className={pkgStyles.locationCard}>
                      <span className={pkgStyles.locationDot} aria-hidden="true" />
                      {item.label ?? item.title}
                      <span className={pkgStyles.locationArrow} aria-hidden="true">›</span>
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
