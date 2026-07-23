import type { Metadata } from "next";
import { notFound } from "next/navigation";
import Banner from "@/app/components/common/Banner";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import TourPackagesSection from "@/app/components/common/TourPackagesSection";
import { fetchCityTourPackages } from "@/services/tourspackages";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import styles from "./CityTourPackagesPage.module.css";

type Props = { params: Promise<{ state: string; city: string }> };

function slugToLabel(slug: string) {
  return slug.replace(/-/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { state, city } = await params;
  const data = await fetchCityTourPackages(state, city);

  const cityName  = data?.city?.name  ?? slugToLabel(city);
  const stateName = data?.state?.name ?? slugToLabel(state);
  const meta      = data?.meta ?? {};
  const banner    = data?.banner ?? {};

  return {
    title:
      meta.meta_title ??
      `${cityName} Tour Packages | ${stateName} | Indian Panorama`,
    description:
      meta.meta_description ??
      `Explore the best ${cityName} tour packages with Indian Panorama. Customised holidays, expert-guided tours, and the best prices guaranteed.`,
    alternates: {
      canonical: `https://www.indianpanorama.in/${state}/${city}/tour-packages`,
    },
    robots: { index: true, follow: true },
    openGraph: banner.image
      ? { images: [{ url: banner.image, alt: banner.image_alt ?? cityName }] }
      : undefined,
  };
}

export default async function CityTourPackagesPage({ params }: Props) {
  const { state, city } = await params;
  const data = await fetchCityTourPackages(state, city);

  if (!data) notFound();

  const cityName:  string   = data.city?.name  ?? slugToLabel(city);
  const stateName: string   = data.state?.name ?? slugToLabel(state);
  const banner               = data.banner ?? {};
  const shortDesc: string   = data.short_description ?? "";
  const packages:  any[]    = data.packages ?? [];
  const faqItems: any[]     = data.faqs?.items ?? [];
  const bestTime: any[]     = data.best_time_to_visit?.items ?? [];

  // h1: use meta.h1_heading if present, else fall back to banner.title
  const h1: string =
    (data.meta?.h1_heading && data.meta.h1_heading.trim())
      ? data.meta.h1_heading
      : banner.title ?? `${cityName} Tour Packages`;

  const faqList = faqItems.map((f: any, i: number) => ({
    id: i + 1,
    question: f.question ?? f.title ?? "",
    answer:   f.answer   ?? f.description ?? "",
  }));


  return (
    <>
      <Banner
        // title={banner.title ?? `${cityName} Tour Packages`}
        // subtitle={
        //   banner.sub_title ??
        //   `Explore the best of ${cityName}, ${stateName}`
        // }
        bgImage={banner.image ?? "/images/about-banner-pages.jpg"} title={""}      />

      <div className={styles.contentLayout}>
        <div className={styles.leftCol}>

          <Breadcrumb items={[
            { label: "Home",          href: "/" },
            { label: stateName,       href: `/${state}` },
            { label: cityName,        href: `/${state}/${city}` },
            { label: `${cityName} Tour Packages`, href: `/${state}/${city}/tour-packages` },
          ]} />

          {/* Only one h1 — meta.h1_heading if available, else banner title */}
          <h1 className={styles.h1}>{h1}</h1>

          {/* Short description HTML */}
          {shortDesc && (
            <ReadMoreHtml html={shortDesc} className={`cms-intro ${styles.intro}`} />
          )}

          <TourPackagesSection
            locationName={cityName}
            packages={packages}
            bestTime={bestTime}
            bestTimeTitle={data.best_time_to_visit?.title}
          />

        </div>

        <aside className={styles.rightCol}>
          <SidebarForm />
        </aside>
      </div>

      {/* ── FAQs ── */}
      {faqList.length > 0 && (
        <FaqSection
          heading={data.faqs?.title ?? `${cityName} Tour Packages — FAQs`}
          items={faqList}
          sideImage={{ src: "/images/faq-side-image.webp", alt: cityName }}
        />
      )}
    </>
  );
}
