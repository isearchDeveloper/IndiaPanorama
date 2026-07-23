import Banner from "@/app/components/common/Banner";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import {
  CarFleetSection,
  CarGallery,
  CarWhyChoose,
  CarRoadTrips,
  CarChecklist,
  CarRouteLinks,
  CarPopularLocations,
  // CarDestinationTags,   // section hidden — uncomment to bring back
  // CarPackageLinks,      // section hidden — uncomment to bring back
} from "../shared";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import styles from "./layouts.module.css";
import homeStyles from "./CarRentalHomeLayout.module.css";

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export default function CarRentalHomeLayout({ data }: { data: any }) {
  const h1 = data?.meta?.h1_heading?.trim() || data?.banner?.title || "Car Rental";

  const fleetCategories = data?.fleet?.categories ?? [];

  const galleryImages = (data?.gallery?.images ?? []).map((g: any) => ({
    url: g.image ?? "",
    alt: g.image_alt ?? "",
  }));

  const roadTripItems = (data?.road_trips?.items ?? []).map((i: any) => ({
    title: i.state ?? i.title ?? "",
    image: i.image ?? "",
    image_alt: i.image_alt ?? "",
    rating: i.rating,
    destinations: i.route_text ?? i.destinations ?? "",
    duration_days: i.duration_days,
    duration_nights: i.duration_nights,
  }));

  const routeItems = (data?.routes?.items ?? []).map((i: any) => ({
    label: i.label,
    slug: i.slug,
    url: `/car-rental/${i.slug}`,
  }));

  const popularItems = (data?.popular_locations?.items ?? []).map((i: any) => ({
    title: i.label ?? i.title ?? "",
    slug: i.slug,
    url: `/car-rental/${i.slug}`,
  }));

  // hidden sections ka data mapping — sections wapas laane pe uncomment karo
  // const destItems = (data?.destination?.items ?? []).map((i: any) => ({
  //   label: i.label,
  //   slug: i.slug,
  //   url: `/car-rental/${i.slug}`,
  // }));

  // const pkgItems = (data?.car_rental_packages?.items ?? []).map((i: any) => ({
  //   label: i.label,
  //   slug: i.slug,
  //   url: `/car-rental/${i.slug}`,
  // }));

  const faqItems = (data?.faqs?.items ?? []).map((f: any, idx: number) => ({
    id: idx + 1,
    question: f.question,
    answer: f.answer,
  }));

  return (
    <>
      <Banner bgImage={data?.banner?.image ?? "/images/about-banner-pages.jpg"} title="" />

      <div className={styles.contentLayout}>
        <div className={styles.leftCol}>
          <Breadcrumb
            items={[{ label: "Home", href: "/" }, { label: "Car Rental", href: "/car-rental" }]}
          />

          <h1 className={styles.titleH1}>{h1}</h1>

          {data?.short_description && (
            <ReadMoreHtml html={data.short_description} className={homeStyles.introPara} />
          )}

          {fleetCategories.length > 0 && (
            <>
              <CarFleetSection categories={fleetCategories} />
            </>
          )}

          {data?.long_description && (
            <ReadMoreHtml html={data.long_description} className={styles.longDesc} />
          )}

          {(data?.checklist?.items?.length ?? 0) > 0 && (
            <>
              <CarChecklist
                title={data.checklist.title}
                items={data.checklist.items}
              />
            </>
          )}

          {galleryImages.length > 0 && (
            <>

              {data.gallery?.title && (
                <h2 className={homeStyles.sectionHeading}>{data.gallery.title}</h2>
              )}
              {data.gallery?.description && (
                <p className={homeStyles.introPara}>{data.gallery.description}</p>
              )}
              <CarGallery images={galleryImages} />
            </>
          )}

          <>
            <CarWhyChoose
              title={data?.why_choose?.title}
              description={data?.why_choose?.description}
              stats={data?.why_choose?.stats}
            />
          </>

          {routeItems.length > 0 && (
            <>
              <CarRouteLinks
                title={data.routes.title}
                items={routeItems}
              />
            </>
          )}

          {popularItems.length > 0 && (
            <>
              <CarPopularLocations
                title={data.popular_locations.title}
                description={data.popular_locations.description}
                items={popularItems}
              />
            </>
          )}

          {/* {destItems.length > 0 && (
            <>
              <CarDestinationTags
                title={data.destination.title}
                items={destItems}
              />
            </>
          )} */}

          {/* {pkgItems.length > 0 && (
            <>
              <CarPackageLinks
                title={data.car_rental_packages.title}
                items={pkgItems}
              />
            </>
          )} */}
        </div>

        <aside className={styles.rightCol} aria-label="Enquiry form">
          <SidebarForm />
        </aside>
      </div>

      {faqItems.length > 0 && (
        <FaqSection
          heading={data?.faqs?.title ?? "Frequently Asked Questions"}
          items={faqItems}
          sideImage={{ src: "/images/faq-side-image.webp", alt: "Car Rental in India" }}
        />
      )}

      {roadTripItems.length > 0 && (
        <CarRoadTrips
          title={data?.road_trips?.title ?? ""}
          subtitle={data?.road_trips?.subtitle ?? ""}
          items={roadTripItems}
        />
      )}
    </>
  );
}
