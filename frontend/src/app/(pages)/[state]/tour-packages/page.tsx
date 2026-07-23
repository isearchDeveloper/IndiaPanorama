import type { Metadata } from "next";
import { notFound } from "next/navigation";
import Banner from "@/app/components/common/Banner";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import TourPackagesSection from "@/app/components/common/TourPackagesSection";
import { fetchStateTourPackages } from "@/services/tourspackages";
import { fetchRegionDetails } from "@/services/cityguide";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import styles from "./StateTourPackagesPage.module.css";

type Props = { params: Promise<{ state: string }> };

function slugToLabel(slug: string) {
  return slug.replace(/-/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
}

// region API (packages/region/{slug}) → same shape as state packages API
// eslint-disable-next-line @typescript-eslint/no-explicit-any
function mapRegionToPackages(region: any) {
  return {
    state: { name: region.region?.name ?? "" },
    banner: region.banner ?? {},
    short_description: region.short_description ?? "",
    packages: region.packages ?? [],
    top_destinations: region.top_destinations ?? [],
    faqs: { title: region.faqs?.title ?? undefined, items: region.faqs?.items ?? [] },
    meta: region.meta ?? {},
  };
}

// state packages API → fallback region packages API (e.g. /west-india/tour-packages)
// eslint-disable-next-line @typescript-eslint/no-explicit-any
async function getPageData(state: string): Promise<{ data: any; isRegion: boolean } | null> {
  const stateData = await fetchStateTourPackages(state);
  if (stateData) return { data: stateData, isRegion: false };

  const regionData = await fetchRegionDetails(state);
  if (regionData) return { data: mapRegionToPackages(regionData), isRegion: true };

  return null;
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { state } = await params;
  const data = (await getPageData(state))?.data ?? null;

  const stateName = data?.state?.name ?? slugToLabel(state);
  const banner = data?.banner;
  const meta = data?.meta;

  return {
    title: meta?.meta_title ?? `${stateName} Tour Packages | Indian Panorama`,
    description:
      meta?.meta_description ??
      `Explore the best ${stateName} tour packages with Indian Panorama. Customised holidays, hand-picked itineraries, and expert guidance.`,
    alternates: { canonical: `https://www.indianpanorama.in/${state}/tour-packages` },
    robots: { index: true, follow: true },
    openGraph: banner?.image
      ? {
          images: [{ url: banner.image, alt: banner.image_alt ?? stateName }],
        }
      : undefined,
  };
}

export default async function StateTourPackagesPage({ params }: Props) {
  const { state } = await params;
  const result = await getPageData(state);

  if (!result) notFound();
  const { data, isRegion } = result;

  const stateName: string = data.state?.name ?? slugToLabel(state);
  const banner = data.banner ?? {};
  const shortDesc: string = data.short_description ?? "";
  const packages: any[] = data.packages ?? [];
  const topDestinations: any[] = data.top_destinations ?? [];
  const faqItems: any[] = data.faqs?.items ?? [];
  const h1: string = data.meta?.h1_heading ?? banner.title ?? `${stateName} Tour Packages`;

  const faqList = faqItems.map((f: any, i: number) => ({
    id: i + 1,
    question: f.question ?? f.title ?? "",
    answer: f.answer ?? f.description ?? "",
  }));

  return (
    <>
      <Banner
        // title={banner.title ?? `${stateName} Tour Packages`}
        // subtitle={banner.sub_title ?? `Discover the best of ${stateName} with our handcrafted tour packages`}
        bgImage={banner.image ?? "/images/about-banner-pages.jpg"} title={""}      />

      <div className={styles.contentLayout}>
        <div className={styles.leftCol}>

          <Breadcrumb items={[
            { label: "Home",          href: "/" },
            { label: stateName,       href: `/${state}` },
            { label: `${stateName} Tour Packages`, href: `/${state}/tour-packages` },
          ]} />

          <h1 className={styles.h1}>{h1}</h1>
          {/* Short description — HTML from API */}
          {shortDesc && (
            <ReadMoreHtml html={shortDesc} className={`cms-intro ${styles.intro}`} />
          )}

          <TourPackagesSection
            locationName={stateName}
            packages={packages}
            bestTime={data.best_time_to_visit?.items ?? []}
            bestTimeTitle={data.best_time_to_visit?.title}
          />

          {/* ── Top destinations grid ── */}
          {topDestinations.length > 0 && (
            <section>
              <h2 className={styles.sectionHeading}>Popular Destinations in {stateName}</h2>
              <div className={styles.cityGrid}>
                {topDestinations.map((dest: any) => (
                  <a
                    key={dest.slug}
                    // region destinations are states (/gujarat/tour-packages),
                    // state destinations are cities (/rajasthan/jaipur/tour-packages)
                    href={isRegion ? `/${dest.slug}/tour-packages` : `/${state}/${dest.slug}/tour-packages`}
                    className={styles.cityCard}
                    aria-label={dest.name}
                  >
                    <div className={styles.cityImgWrap}>
                      {/* eslint-disable-next-line @next/next/no-img-element */}
                      <img
                        src={dest.image ?? "/images/about-banner-pages.jpg"}
                        alt={dest.image_alt ?? dest.name}
                        className={styles.cityImg}
                      />
                    </div>
                    <div className={styles.cityBody}>
                      <span className={styles.cityName}>{dest.name}</span>
                      <span className={styles.cityExplore}>Explore Now</span>
                    </div>
                  </a>
                ))}
              </div>
            </section>
          )}

        </div>

        <aside className={styles.rightCol}>
          <SidebarForm />
        </aside>
      </div>

      {/* ── FAQs ── */}
      {faqList.length > 0 && (
        <FaqSection
          heading={data.faqs?.title ?? `${stateName} Tour Packages — FAQs`}
          items={faqList}
          sideImage={{ src: "/images/faq-side-image.webp", alt: stateName }}
        />
      )}
    </>
  );
}
