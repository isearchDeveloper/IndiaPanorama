/* eslint-disable @typescript-eslint/no-explicit-any */
import type { Metadata } from "next";
import { notFound, permanentRedirect } from "next/navigation";
import { fetchActivityDetail } from "@/services/activitiesService";
import { fetchTouristAttractionDetail } from "@/services/touristAttractions";
import { getExperienceDetail } from "@/services/experiencesService";
import ActivityDetailLayout from "@/app/components/activities/ActivityDetailLayout";
import AttractionDetailContent from "@/app/components/tourist-attractions/AttractionDetailContent";
import ExperienceDetailLayout from "@/app/components/experiences/ExperienceDetailLayout";

// experience detail API → ExperienceDetailLayout ka shape
function mapExperience(d: any) {
  return {
    title: d.title ?? "",
    tagline: d.tagline ?? null,
    description: d.description ?? "",
    images: d.images ?? [],
    highlights: d.highlights ?? [],
    bestTime: d.quick_info?.best_time ?? null,
    duration: d.quick_info?.duration ?? null,
    entryFee: d.quick_info?.entry_fee ?? null,
    location: d.quick_info?.location ?? null,
    faqs: (d.faqs ?? []).map((f: any, i: number) => ({
      id: i + 1,
      question: f.question,
      answer: f.answer,
    })),
    relatedSpots: (d.related ?? []).map((r: any) => ({
      slug: r.slug,
      title: r.title,
      image: r.image ?? "/images/about-banner-pages.jpg",
      themeSlug: r.subcategory_slug ?? r.subcategory?.slug ?? "",
      stateSlug: r.state_slug,
      citySlug: r.city_slug,
    })),
  };
}

export const dynamic = "force-dynamic";

type Props = { params: Promise<{ state: string; city: string; slug: string }> };

function slugToLabel(slug: string) {
  return slug.replace(/-/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
}

function buildBreadcrumbs(state: string, city: string, slug: string) {
  return [
    { label: "Home", href: "/" },
    { label: slugToLabel(state), href: `/${state}` },
    { label: slugToLabel(city), href: `/${state}/${city}` },
    { label: slugToLabel(slug), href: `/${state}/${city}/${slug}` },
  ];
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { state, city, slug } = await params;

  // Check if it is an experience slug — API se
  if (slug.endsWith("-experience")) {
    const baseSlug = slug.slice(0, -"-experience".length);
    const d = await getExperienceDetail(baseSlug);
    if (d) {
      return {
        title: d.meta?.meta_title ?? `${d.title} | Indian Panorama`,
        description:
          d.meta?.meta_description ??
          d.tagline ??
          d.description?.replace(/<[^>]*>/g, "").slice(0, 160) ??
          undefined,
        keywords: d.meta?.meta_keywords ?? undefined,
        alternates: {
          // canonical hamesha experience ke ASLI state/city se — URL segments se nahi
          canonical: `https://www.indianpanorama.in/${d.state_slug ?? state}/${d.city_slug ?? city}/${slug}`,
        },
        robots: { index: true, follow: true },
      };
    }
  }

  const activityData = await fetchActivityDetail(slug);
  const data =
    activityData?.type === "activity"
      ? activityData
      : await fetchTouristAttractionDetail(slug);

  if (!data) return {};

  return {
    title: data.meta?.meta_title ?? `${data.banner?.title} | Indian Panorama`,
    description: data.meta?.meta_description ?? data.short_description?.replace(/<[^>]*>/g, "").slice(0, 160) ?? undefined,
    keywords: data.meta?.meta_keywords ?? undefined,
    alternates: {
      canonical: `https://www.indianpanorama.in/${state}/${city}/${slug}`,
    },
    robots: { index: true, follow: true },
  };
}

export default async function DetailPage({ params }: Props) {
  const { state, city, slug } = await params;

  // Try checking experience type first if slug has suffix — API se
  if (slug.endsWith("-experience")) {
    const baseSlug = slug.slice(0, -"-experience".length);


    const d = await getExperienceDetail(baseSlug);
    if (d) {
      // production/SEO guard: experience ka asli address API se aata hai —
      // backend me city assign hi nahi (null) → is URL ka koi valid address nahi → 404
      if (!d.state_slug || !d.city_slug) notFound();
      // galat state/city segment (jaise /kerala/null/...) → canonical URL pe permanent redirect
      if (state !== d.state_slug || city !== d.city_slug) {
        permanentRedirect(`/${d.state_slug}/${d.city_slug}/${slug}`);
      }
      return (
        <ExperienceDetailLayout
          spot={mapExperience(d)}
          breadcrumbs={buildBreadcrumbs(state, city, slug)}
        />
      );
    }
  }

  const activityData = await fetchActivityDetail(slug);

  if (activityData?.type === "activity") {
    return (
      <ActivityDetailLayout
        slug={slug}
        data={activityData}
        breadcrumbs={buildBreadcrumbs(state, city, slug)}
        linkBase={`/${state}/${city}`}
      />
    );
  }

  const attractionData = await fetchTouristAttractionDetail(slug);
  if (!attractionData) notFound();

  return (
    <AttractionDetailContent
      data={attractionData}
      breadcrumbs={buildBreadcrumbs(state, city, slug)}
      linkBase={`/${state}/${city}`}
    />
  );
}
