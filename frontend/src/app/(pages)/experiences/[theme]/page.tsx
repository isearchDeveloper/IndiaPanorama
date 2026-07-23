/* eslint-disable @typescript-eslint/no-explicit-any */
import type { Metadata } from "next";
import { notFound } from "next/navigation";
import Banner from "@/app/components/common/Banner";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import ThemeQuickInfo from "@/app/components/experiences/ThemeQuickInfo";
import ExperienceThemesGrid from "@/app/components/experiences/ExperienceThemesGrid";
import ActivityPerfectFor from "@/app/components/activities/ActivityPerfectFor";
import SubcategoryListing from "@/app/components/experiences/SubcategoryListing";
import {
  getExperienceCategory,
  getExperienceSubcategory,
  getExperienceState,
} from "@/services/experiencesService";
import styles from "./ThemePage.module.css";

type Props = { params: Promise<{ theme: string }> };

const FALLBACK_BANNER = "/images/about-banner-pages.jpg";

// ── /experiences/[slug] RESOLVER — pura API-driven ──
// 1. "{sub}-in-{state}"  → subcategory API + ?state= filter
// 2. subcategory API     → all-India listing
// 3. category API        → category page
// kuch na mile → 404. URL kabhi 2 directory se deep nahi jaata.

function slugToLabel(slug: string) {
  return slug.replace(/-/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
}

function parseInState(slug: string): { subSlug: string; stateSlug: string } | null {
  const idx = slug.lastIndexOf("-in-");
  if (idx === -1) return null;
  const subSlug = slug.slice(0, idx);
  const stateSlug = slug.slice(idx + 4);
  if (!subSlug || !stateSlug) return null;
  return { subSlug, stateSlug };
}

// SEO guard: "-in-{state}" ka state REAL hona chahiye (state API se verify),
// warna /experiences/xyz-in-fakestate jaise infinite fake URLs index ho jaate.
// Cached 30s hai to extra call sasta hai. Valid state ka proper naam bhi milta hai.
async function validateState(stateSlug: string): Promise<string | null> {
  const st = await getExperienceState(stateSlug);
  return st?.state_name ?? null;
}

// listing data resolve: pehle -in- combo (validated state ke saath), phir seedha subcategory
async function resolveListing(theme: string) {
  const inState = parseInState(theme);
  if (inState) {
    const stateName = await validateState(inState.stateSlug);
    if (stateName) {
      const data = await getExperienceSubcategory(inState.subSlug, inState.stateSlug);
      if (data) return { data, activeState: inState.stateSlug, activeStateName: stateName };
    }
    // invalid state ya subcategory nahi mila → plain try bhi bekar ("-in-" wala slug
    // kabhi subcategory/category slug nahi ho sakta — reserved separator) → 404
    return null;
  }
  const data = await getExperienceSubcategory(theme);
  if (data) return { data, activeState: null as string | null, activeStateName: null as string | null };
  return null;
}

// category resolve: plain slug ya "{category}-in-{state}" (state-context mode)
async function resolveCategory(theme: string) {
  const inState = parseInState(theme);
  if (inState) {
    const stateName = await validateState(inState.stateSlug);
    if (stateName) {
      const data = await getExperienceCategory(inState.subSlug);
      if (data) return { data, stateSlug: inState.stateSlug, stateName };
    }
    return null;
  }
  const data = await getExperienceCategory(theme);
  if (data) return { data, stateSlug: null as string | null, stateName: null as string | null };
  return null;
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { theme } = await params;

  const listing = await resolveListing(theme);
  if (listing) {
    const { data, activeState, activeStateName } = listing;
    const stateName = activeStateName;
    void activeState;
    const title = stateName ? `${data.name} in ${stateName}` : `${data.name} in India`;
    return {
      title: data.meta?.meta_title ?? `${title} | Indian Panorama`,
      description:
        data.meta?.meta_description ??
        data.banner?.description?.replace(/<[^>]*>/g, "").slice(0, 160) ??
        undefined,
      alternates: { canonical: `https://www.indianpanorama.in/experiences/${theme}` },
      robots: { index: true, follow: true },
    };
  }

  const catRes = await resolveCategory(theme);
  if (catRes) {
    const cat = catRes.data;
    const stateName = catRes.stateName;
    // subcategory-less category seedha listing hai → listing-style title ("X in India")
    const isDirectListing =
      (cat.subcategories ?? []).length === 0 && (cat.experiences ?? []).length > 0;
    const baseTitle = isDirectListing
      ? `${cat.name} in ${stateName ?? "India"}`
      : stateName
        ? `${cat.banner?.title ?? cat.name} in ${stateName}`
        : cat.banner?.title ?? cat.name;
    return {
      title: cat.meta?.meta_title ?? `${baseTitle} | Indian Panorama`,
      description:
        cat.meta?.meta_description ??
        cat.short_description?.replace(/<[^>]*>/g, "").slice(0, 160) ??
        undefined,
      keywords: cat.meta?.meta_keywords ?? undefined,
      alternates: { canonical: `https://www.indianpanorama.in/experiences/${theme}` },
      robots: { index: true, follow: true },
    };
  }

  return {};
}

export default async function ExperienceThemePage({ params }: Props) {
  const { theme } = await params;

  // ── case 1 & 2: subcategory listing (with/without state filter) ──
  const listing = await resolveListing(theme);
  if (listing) {
    const { data, activeState, activeStateName } = listing;

    // production guard: bina state/city slug ke item ka valid URL ban hi nahi sakta → hide
    const items = (data.items ?? [])
      .filter((it: any) => it.slug && it.state_slug && it.city_slug)
      .map((it: any) => ({
        name: it.title,
        tagline: it.city_name && it.state_name ? `${it.city_name}, ${it.state_name}` : undefined,
        description: it.tagline ?? "",
        image: it.image ?? FALLBACK_BANNER,
        image_alt: it.image_alt ?? it.title,
        href: `/${it.state_slug}/${it.city_slug}/${it.slug}-experience`,
      }));

    return (
      <SubcategoryListing
        sub={{
          name: data.name ?? slugToLabel(theme),
          slug: data.slug ?? theme,
          categoryName: data.category?.name,
          categorySlug: data.category?.slug,
          bannerImage: data.banner?.image,
          description: data.banner?.description,
        }}
        items={items}
        states={(data.states ?? []).filter((s: any) => s.slug && s.slug !== "all")}
        activeState={activeState}
        activeStateName={activeStateName}
      />
    );
  }

  // ── case 3: CATEGORY page (plain ya state-context) — jo section na aaye wo hide ──
  const catRes = await resolveCategory(theme);
  if (!catRes) notFound();

  const cat = catRes.data;
  const stateSlug = catRes.stateSlug;   // e.g. "kerala" | null
  const stateName = catRes.stateName;   // API se proper naam (validated)

  const name = cat.name ?? slugToLabel(theme);

  // ── SUBCATEGORY-LESS category (e.g. temples): backend subcategories [] + experiences[]
  //    me directly items bhejta hai → beech ka category page SKIP, seedha LISTING dikhao ──
  const directExperiences: any[] =
    (cat.subcategories ?? []).length === 0 ? cat.experiences ?? [] : [];

  if (directExperiences.length > 0) {
    // production guard: bina state/city slug ke item ka valid URL nahi banta (/null/ URL) → hide
    const linkable = directExperiences.filter(
      (it: any) => it.slug && it.state_slug && it.city_slug
    );

    // state-context mode me items client-side filter (category endpoint ?state= nahi leta)
    const visible = stateSlug
      ? linkable.filter((it: any) => it.state_slug === stateSlug)
      : linkable;

    const items = visible.map((it: any) => ({
      name: it.title,
      tagline: it.city_name && it.state_name ? `${it.city_name}, ${it.state_name}` : undefined,
      description: it.tagline ?? "",
      image: it.image ?? FALLBACK_BANNER,
      image_alt: it.image_alt ?? it.title,
      href: `/${it.state_slug}/${it.city_slug}/${it.slug}-experience`,
    }));

    // state chips linkable items se derive (jin states me dikhne wale items hain)
    const seen = new Set<string>();
    const states = linkable
      .filter((it: any) => it.state_slug && !seen.has(it.state_slug) && seen.add(it.state_slug))
      .map((it: any) => ({
        slug: it.state_slug,
        name: it.state_name ?? slugToLabel(it.state_slug),
      }));

    return (
      <SubcategoryListing
        sub={{
          name,
          slug: cat.slug ?? theme,   // chips base-slug pe link karti hain
          bannerImage: cat.banner?.image,
          description: cat.short_description,
        }}
        items={items}
        states={states}
        activeState={stateSlug}
        activeStateName={stateName}
      />
    );
  }

  const baseH1 = cat.meta?.h1_heading ?? cat.banner?.title ?? name;
  // state mode me h1/banner me state ka naam
  const h1 = stateName ? `${baseH1} in ${stateName}` : baseH1;

  const subcategories = (cat.subcategories ?? []).map((s: any) => ({
    name: s.name,
    slug: s.slug,
    image: s.image ?? FALLBACK_BANNER,
    image_alt: s.image_alt ?? s.name,
    description: s.description ?? "",
    // popular_experience object ({title, slug, image}) ya string dono aa sakta hai
    popularTag:
      typeof s.popular_experience === "string"
        ? s.popular_experience
        : s.popular_experience?.title ?? undefined,
  }));

  const perfectFor = (cat.perfect_for ?? []).map((p: any) => ({
    icon: p.icon ?? null,
    title: p.title,
    description: p.description ?? undefined,
  }));

  const faqs = (cat.faqs?.items ?? []).map((f: any, i: number) => ({
    id: i + 1,
    question: f.question,
    answer: f.answer,
  }));

  return (
    <>
      <Banner
        title={stateName ? `${cat.banner?.title ?? name} in ${stateName}` : cat.banner?.title ?? name}
        subtitle={cat.banner?.tagline ?? ""}
        bgImage={cat.banner?.image ?? FALLBACK_BANNER}
      />

      <div className={styles.pageLayout}>
        <div className={styles.mainCol}>
          <Breadcrumb
            items={[
              { label: "Home", href: "/" },
              { label: "Experiences", href: "/experiences" },
              ...(stateName && stateSlug
                ? [{ label: stateName, href: `/${stateSlug}/experiences` }]
                : []),
              { label: name, href: `/experiences/${theme}` },
            ]}
          />

          {/* Intro — illustration right float, API ka HTML */}
          <div className={styles.introBlock}>
            {cat.intro_image && (
              /* eslint-disable-next-line @next/next/no-img-element */
              <img
                src={cat.intro_image}
                alt=""
                aria-hidden="true"
                className={styles.illustration}
                loading="lazy"
              />
            )}
            <h1 className={styles.pageH1}>{h1}</h1>
            {cat.short_description && (
              <ReadMoreHtml
                html={cat.short_description}
                className={`cms-intro ${styles.intro}`}
              />
            )}
          </div>

          {/* Quick Information */}
          {(cat.quick_info ?? []).length > 0 && (
            <ThemeQuickInfo items={cat.quick_info} />
          )}

          {/* Sub-categories — state mode me links bhi -in-{state} ke saath */}
          {subcategories.length > 0 && (
            <ExperienceThemesGrid
              heading={
                stateName
                  ? `Explore ${name} Themes in ${stateName}`
                  : `Explore ${name} Themes`
              }
              themes={subcategories}
              basePath="/experiences"
              linkSuffix={stateSlug ? `-in-${stateSlug}` : ""}
              showViewTours
            />
          )}

          {/* state mode: wapas state hub ka rasta */}
          {stateName && stateSlug && (
            <p className={styles.stateHubLink}>
              <a href={`/${stateSlug}/experiences`}>
                View all {stateName} experiences →
              </a>
            </p>
          )}

          {/* Perfect For */}
          {perfectFor.length > 0 && (
            <div className={styles.sectionGap}>
              <ActivityPerfectFor title="Perfect For" items={perfectFor} />
            </div>
          )}

          {/* FAQ */}
          {faqs.length > 0 && (
            <div className={styles.faqWrap}>
              <FaqSection heading={cat.faqs?.title ?? "FAQ's"} items={faqs} />
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
