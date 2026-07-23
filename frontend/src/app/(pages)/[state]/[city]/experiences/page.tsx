/* eslint-disable @typescript-eslint/no-explicit-any */
import type { Metadata } from "next";
import { notFound } from "next/navigation";
import Banner from "@/app/components/common/Banner";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import ThingsToDo from "@/app/components/city-guide/ThingsToDo";
import ExploreGrid from "@/app/components/experiences/ExploreGrid";
import ExperienceThemesGrid from "@/app/components/experiences/ExperienceThemesGrid";
import { getExperienceCity } from "@/services/experiencesService";
import styles from "./CityExperiencesPage.module.css";

// PURA page API-driven — /experiences/{state}/{city}
// jo section backend nahi bhejta wo render hi nahi hota.

type Props = { params: Promise<{ state: string; city: string }> };

const FALLBACK_BANNER = "/images/about-banner-pages.jpg";

function slugToLabel(slug: string) {
  return slug.replace(/-/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { state, city } = await params;
  const api = await getExperienceCity(state, city);
  if (!api) return {};

  const cityName = api.city_name ?? slugToLabel(city);
  return {
    title: api.meta?.meta_title ?? `${cityName} Experiences | Things To Do & Activities | Indian Panorama`,
    description:
      api.meta?.meta_description ??
      api.banner?.description?.replace(/<[^>]*>/g, "").slice(0, 160) ??
      undefined,
    keywords: api.meta?.meta_keywords ?? undefined,
    alternates: { canonical: `https://www.indianpanorama.in/${state}/${city}/experiences` },
    robots: { index: true, follow: true },
  };
}

export default async function CityExperiencesPage({ params }: Props) {
  const { state, city } = await params;
  const api = await getExperienceCity(state, city);
  if (!api) notFound();

  const stateName = api.state_name ?? slugToLabel(state);
  const cityName = api.city_name ?? slugToLabel(city);
  const h1 = api.meta?.h1_heading ?? api.banner?.title ?? `${cityName} Experiences`;

  /* ── sections — empty = hide ── */
  const categories = (api.category ?? []).map((c: any) => ({
    name: c.name,
    slug: c.slug,
    image: c.image ?? FALLBACK_BANNER,
    image_alt: c.image_alt ?? c.name,
    description: c.description ?? "",
  }));

  // attractions — slugs already -tourist-attractions suffixed → unified detail route
  const attractions = (api.attractions ?? []).map((a: any) => ({
    name: a.name,
    image: a.image ?? FALLBACK_BANNER,
    image_alt: a.image_alt ?? a.name,
    href: `/${a.state ?? state}/${a.city ?? city}/${a.slug}`,
  }));

  const adventures = (api.activities?.list ?? []).map((a: any, i: number) => ({
    id: i + 1,
    title: a.title ?? a.name ?? "",
    description: a.description ?? a.text ?? "",
  }));

  const highlights = (api.highlights?.list ?? []).map((h: any, i: number) => ({
    id: i + 1,
    title: h.title ?? h.name ?? "",
    description: h.description ?? h.text ?? "",
  }));

  // tourist activities — slugs already -activity suffixed → unified detail route
  const touristActivities = (api.tourist_activities ?? []).map((t: any) => ({
    name: t.name,
    description: t.description ?? "",
    image: t.image ?? FALLBACK_BANNER,
    image_alt: t.image_alt ?? t.name,
    href: `/${state}/${t.city_slug ?? city}/${t.slug}`,
  }));

  const faqs = (api.faqs?.list ?? []).map((f: any, i: number) => ({
    id: i + 1,
    question: f.question,
    answer: f.answer,
  }));

  return (
    <>
      <Banner
        title={api.banner?.title ?? `${cityName} Experiences`}
        subtitle=""
        bgImage={api.banner?.image ?? FALLBACK_BANNER}
      />

      <div className={styles.pageLayout}>
        <div className={styles.mainCol}>
          <Breadcrumb
            items={[
              { label: "Home", href: "/" },
              { label: stateName, href: `/${state}` },
              { label: cityName, href: `/${state}/${city}` },
              { label: `${cityName} Experiences`, href: `/${state}/${city}/experiences` },
            ]}
          />

          <h1 className={styles.pageH1}>{h1}</h1>
          {api.banner?.description && (
            <ReadMoreHtml
              html={api.banner.description}
              className={`cms-intro ${styles.intro}`}
            />
          )}

          {/* Experience themes — categories (2×2 cards) */}
          {categories.length > 0 && (
            <ExperienceThemesGrid
              heading={`Explore Experience Themes in ${cityName}`}
              themes={categories}
              basePath="/experiences"
              linkSuffix={`-in-${state}`}
            />
          )}

          {/* Adventure experiences — arrow list */}
          {adventures.length > 0 && (
            <div className={styles.sectionGap}>
              <ThingsToDo
                heading={api.activities?.title ?? `Adventure Experiences in ${cityName}`}
                items={adventures}
              />
            </div>
          )}

          {/* Highlights — arrow list */}
          {highlights.length > 0 && (
            <div className={styles.sectionGap}>
              <ThingsToDo
                heading={api.highlights?.title ?? `What Makes ${cityName} Special?`}
                items={highlights}
              />
            </div>
          )}

          {/* Top attractions — unified detail links */}
          {attractions.length > 0 && (
            <ExploreGrid heading={`Top Attractions in ${cityName}`} items={attractions} />
          )}

          {/* Tourist activities — unified detail links */}
          {touristActivities.length > 0 && (
            <ExploreGrid
              heading={`Activities in ${cityName}`}
              items={touristActivities}
            />
          )}

          {/* FAQ */}
          {faqs.length > 0 && (
            <div className={styles.faqWrap}>
              <FaqSection
                heading={api.faqs?.title ?? "FAQ's"}
                subtext={api.faqs?.sub_title ?? undefined}
                items={faqs}
              />
            </div>
          )}
        </div>

        <aside className={styles.sidebarCol} aria-label="Enquiry form">
          <SidebarForm />
        </aside>
      </div>
    </>
  );
}
