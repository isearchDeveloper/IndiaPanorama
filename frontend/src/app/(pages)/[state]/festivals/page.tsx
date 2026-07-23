import type { Metadata } from "next";
import { notFound } from "next/navigation";
import FestivalLayout from "@/app/components/festivals/FestivalLayout";
import StateFestivalGrid from "@/app/components/festivals/StateFestivalGrid";
import FestivalMonths from "@/app/components/festivals/FestivalMonths";
import FeaturedFestival from "@/app/components/festivals/FeaturedFestival";
import FestivalWhyChoose from "@/app/components/festivals/FestivalWhyChoose";
import ExploreMoreStates from "@/app/components/festivals/ExploreMoreStates";
import FestivalTourPackages from "@/app/components/festivals/FestivalTourPackages";
import { fetchStateFestivals } from "@/services/festivalsService";

export const dynamic = "force-dynamic";

type Props = { params: Promise<{ state: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { state } = await params;
  const data = await fetchStateFestivals(state);
  if (!data) return {};
  return {
    title: data.meta?.meta_title ?? `${data.banner?.title ?? "Festivals"} | Indian Panorama`,
    description: data.meta?.meta_description ?? undefined,
    keywords: data.meta?.meta_keywords ?? undefined,
    alternates: { canonical: `https://www.indianpanorama.in/${state}/festivals` },
    robots: { index: true, follow: true },
  };
}

export default async function StateFestivalsPage({ params }: Props) {
  const { state } = await params;
  const data = await fetchStateFestivals(state);
  if (!data) notFound();

  // backend flux: koi bhi section kabhi bhi missing aa sakta hai — sab guarded
  const stateName = (data.banner?.title ?? "").replace("Festivals of ", "");

  // 1. Map popular festivals for the grid
  const stateFestivals = (data.popular_festivals?.items ?? []).map((f) => ({
    name: f.name,
    slug: f.slug,
    image: f.image,
    image_alt: f.image_alt,
    location: f.location_text ?? "",
    location_text: f.location_text ?? "",
    monthName: f.month_text ?? "",
    month_text: f.month_text ?? "",
    description: f.short_description ? f.short_description.replace(/<[^>]*>/g, "") : "",
    // Fallbacks for mock properties
    id: 0,
    tagline: "",
    state: state,
    stateName,
    month: "",
    date: "",
    duration: "",
    bannerImage: "",
  }));

  // 2. Map explore by month
  const exploreByMonth = data.explore_by_month ?? [];
  const monthsData = exploreByMonth.map((m) => ({
    slug: m.month_name.toLowerCase(),
    name: m.month_name,
    shortName: m.month_name,
    festivals: (m.festivals ?? []).map((f) => f.name),
  }));

  const monthFestivalsList = exploreByMonth.flatMap((m) =>
    (m.festivals ?? []).map((f) => ({
      name: f.name,
      slug: f.name.toLowerCase().replace(/\s+/g, "-"),
      image: f.image,
      image_alt: f.image_alt,
      month: m.month_name.toLowerCase(),
      description: "",
      date: "",
      duration: "",
      location: "",
      state: f.state_slug,
    }))
  );

  // 3. Map featured festival
  const featuredFestival = data.featured_festival ? {
    name: data.featured_festival.name,
    slug: data.featured_festival.slug,
    image: data.featured_festival.image,
    image_alt: data.featured_festival.image_alt,
    location: data.featured_festival.location_text,
    monthName: data.featured_festival.month_text,
    duration: data.featured_festival.duration_text,
    description: (data.featured_festival.short_description ?? "").replace(/<[^>]*>/g, ""),
    id: 0,
    tagline: "",
    state: state,
    stateName: "",
    month: "",
    date: "",
    bannerImage: "",
  } : null;

  // 4. Map why visit items
  const whyItems = (data.why_visit?.items ?? []).map((item) => ({
    title: item.title,
    description: item.tagline || item.description || "",
  }));

  // 5. Map FAQ items
  const faqItems = (data.faqs?.list ?? []).map((f, i) => ({
    id: i + 1,
    question: f.question,
    answer: f.answer,
  }));

  // 6. Map explore more destinations
  const otherStates = (data.explore_more_destinations?.items ?? []).map((ds) => ({
    name: ds.state_name,
    slug: ds.state_slug,
    image: ds.image,
    description: `${ds.route_text} · ${ds.duration_days} Days / ${ds.duration_nights} Nights`,
    rating: ds.rating,
  }));

  // 7. Map packages if available
  const packagesList = data.state_packages?.items?.map((pkg) => ({
    slug: pkg.slug,
    name: pkg.title,
    image: pkg.image,
    tag: pkg.location,
    days: pkg.duration_days,
    nights: pkg.duration_nights,
    href: `/tour/${pkg.slug}`,
  })) ?? [];

  return (
    <>
      <FestivalLayout
        bannerTitle={data.banner?.title ?? ""}
        bannerSubtitle={data.banner?.banner_text ?? ""}
        bannerImage={data.banner?.image ?? "/images/about-banner-pages.jpg"}
        breadcrumbs={[
          { label: "Home", href: "/" },
          { label: "Festivals", href: "/festivals" },
          { label: stateName || state, href: `/${state}/festivals` },
        ]}
        h1={data.meta?.h1_heading ?? data.banner?.title ?? "Festivals"}
        intro={data.short_description}
        faqs={faqItems}
        faqHeading={data.faqs?.title}
        faqSubtext={data.faqs?.sub_title}
      >
        {stateFestivals.length > 0 && (
          <StateFestivalGrid
            festivals={stateFestivals as any}
            heading={data.popular_festivals?.title}
          />
        )}

        {monthsData.length > 0 && monthFestivalsList.length > 0 && (
          <FestivalMonths
            months={monthsData as any}
            festivals={monthFestivalsList as any}
            heading="Explore Festivals by Months"
          />
        )}

        {featuredFestival && (
          <FeaturedFestival festival={featuredFestival as any} label="Featured Festival" />
        )}
        
       {packagesList.length > 0 && (
        <FestivalTourPackages
          packages={packagesList}
          heading={data.state_packages?.title}
        />
      )}

        {whyItems.length > 0 && (
          <FestivalWhyChoose
            items={whyItems}
            heading={data.why_visit?.title}
          />
        )}
      </FestivalLayout>

  

      {otherStates.length > 0 && (
        <ExploreMoreStates
          states={otherStates as any}
          heading={data.explore_more_destinations?.title}
          subtext={`Discover India's most vibrant festival destinations and experience unique cultural celebrations.`}
        />
      )}
    </>
  );
}
