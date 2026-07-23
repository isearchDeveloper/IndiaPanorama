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
import SignatureExperiences from "@/app/components/experiences/SignatureExperiences";
import { getExperienceState } from "@/services/experiencesService";
import styles from "./StateExperiencesPage.module.css";

// PURA page API-driven — /experiences/state/{slug}
// jo section backend nahi bhejta wo render hi nahi hota.

type Props = { params: Promise<{ state: string }> };

const FALLBACK_BANNER = "/images/about-banner-pages.jpg";

function slugToLabel(slug: string) {
  return slug.replace(/-/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { state } = await params;
  const api = await getExperienceState(state);
  if (!api) return {};

  const name = api.state_name ?? slugToLabel(state);
  return {
    title: api.meta?.meta_title ?? `${name} Experiences | Tours, Activities & Attractions | Indian Panorama`,
    description:
      api.meta?.meta_description ??
      api.banner?.description?.replace(/<[^>]*>/g, "").slice(0, 160) ??
      undefined,
    keywords: api.meta?.meta_keywords ?? undefined,
    alternates: { canonical: `https://www.indianpanorama.in/${state}/experiences` },
    robots: { index: true, follow: true },
  };
}

export default async function StateExperiencesPage({ params }: Props) {
  const { state } = await params;
  const api = await getExperienceState(state);
  if (!api) notFound();

  const name = api.state_name ?? slugToLabel(state);
  const h1 = api.meta?.h1_heading ?? api.banner?.title ?? `${name} Experiences`;

  /* ── sections — empty = hide ── */
  const themeTours = (api.category ?? []).map((c: any) => ({
    name: c.name,
    description: c.description ?? "",
    image: c.image ?? FALLBACK_BANNER,
    image_alt: c.image_alt ?? c.name,
    // state context URL me bhi rahe — category-in-state page
    href: `/experiences/${c.slug}-in-${state}`,
  }));

  // API fields: city_slug + state_slug + experiences_count (slug nahi!)
  const cityTours = (api.cities ?? []).map((c: any) => ({
    title: c.name,
    slug: c.city_slug ?? c.slug,
    image: c.image ?? FALLBACK_BANNER,
    image_alt: c.image_alt ?? c.name,
    toursCount: c.experiences_count
      ? `${String(c.experiences_count).padStart(2, "0")} Experiences`
      : c.tours_count ?? "",
    description: "",
    popularTag: "",
    href: `/${c.state_slug ?? state}/${c.city_slug ?? c.slug}/experiences`,
  }));

  // attractions — slugs already -tourist-attractions suffixed → unified detail route
  const attractions = (api.attractions ?? []).map((a: any) => ({
    title: a.name,
    slug: a.slug,
    image: a.image ?? FALLBACK_BANNER,
    image_alt: a.image_alt ?? a.name,
    toursCount: "",
    description: "",
    popularTag: "",
    href: `/${a.state ?? state}/${a.city}/${a.slug}`,
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
  // production guard: city_slug null ho to /null/ URL ban jata hai → aise items hide
  const touristActivities = (api.tourist_activities ?? [])
    .filter((t: any) => t.slug && t.city_slug)
    .map((t: any) => ({
      name: t.name,
      description: t.description ?? "",
      image: t.image ?? FALLBACK_BANNER,
      image_alt: t.image_alt ?? t.name,
      href: `/${state}/${t.city_slug}/${t.slug}`,
    }));

  const faqs = (api.faqs?.list ?? []).map((f: any, i: number) => ({
    id: i + 1,
    question: f.question,
    answer: f.answer,
  }));

  return (
    <>
      <Banner
        title={api.banner?.title ?? `${name} Experiences`}
        subtitle=""
        bgImage={api.banner?.image ?? FALLBACK_BANNER}
      />

      <div className={styles.pageLayout}>
        <div className={styles.mainCol}>
          <Breadcrumb
            items={[
              { label: "Home", href: "/" },
              { label: name, href: `/${state}` },
              { label: `${name} Experiences`, href: `/${state}/experiences` },
            ]}
          />

          <h1 className={styles.pageH1}>{h1}</h1>
          {api.banner?.description && (
            <ReadMoreHtml
              html={api.banner.description}
              className={`cms-intro ${styles.intro}`}
            />
          )}

          {/* Theme tours — categories */}
          {themeTours.length > 0 && (
            <ExploreGrid heading={`${name} Theme Tours`} items={themeTours} />
          )}

          {/* City tours slider */}
          {cityTours.length > 0 && (
            <SignatureExperiences
              heading={`${name} City Tours`}
              items={cityTours}
              linkLabel="Explore Now →"
            />
          )}

          {/* Top attractions slider — unified detail links */}
          {attractions.length > 0 && (
            <SignatureExperiences
              heading={`Top Tourist Attractions in ${name}`}
              items={attractions}
              linkLabel="Explore Now →"
            />
          )}

          {/* Adventure experiences — arrow list */}
          {adventures.length > 0 && (
            <div className={styles.sectionGap}>
              <ThingsToDo
                heading={api.activities?.title ?? `Adventure Experiences in ${name}`}
                items={adventures}
              />
            </div>
          )}

          {/* Highlights — arrow list */}
          {highlights.length > 0 && (
            <div className={styles.sectionGap}>
              <ThingsToDo
                heading={api.highlights?.title ?? `What Makes ${name} Special?`}
                items={highlights}
              />
            </div>
          )}

          {/* Tourist activities — unified detail links */}
          {touristActivities.length > 0 && (
            <ExploreGrid
              heading={`Discover the Best Activities in ${name}`}
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
