/* eslint-disable @typescript-eslint/no-explicit-any */
import type { Metadata } from "next";
import Banner from "@/app/components/common/Banner";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import IconCards from "@/app/components/common/IconCards";
import ExperienceStats from "@/app/components/experiences/ExperienceStats";
import ExperienceThemesGrid from "@/app/components/experiences/ExperienceThemesGrid";
import SignatureExperiences from "@/app/components/experiences/SignatureExperiences";
import PopularStatesSection from "@/app/components/experiences/PopularStatesSection";
import FestivalWhyChoose from "@/app/components/festivals/FestivalWhyChoose";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import { getExperiencesData } from "@/services/experiencesService";
import styles from "./ExperiencesPage.module.css";

// PURA page API-driven (activities root jaisa pattern):
// jo section backend nahi bhejta wo render hi nahi hota — koi static fallback nahi.
// Endpoint common API_BASE_URL (.env) + path se banta hai — baaki services jaisa.

const FALLBACK_BANNER = "/images/about-banner-pages.jpg";

export async function generateMetadata(): Promise<Metadata> {
  const api = await getExperiencesData();
  const title =
    api?.meta?.meta_title ??
    "India Travel Experiences | Cultural, Spiritual, Wildlife & Heritage Tours";
  const description =
    api?.meta?.meta_description ??
    "Explore India's rich cultural, spiritual, heritage and wildlife experiences with curated travel packages and destinations.";

  return {
    title,
    description,
    keywords: api?.meta?.meta_keywords ?? undefined,
    alternates: { canonical: "https://www.indianpanorama.in/experiences" },
    robots: { index: true, follow: true },
    openGraph: {
      title,
      description,
      url: "https://www.indianpanorama.in/experiences",
      siteName: "Indian Panorama",
      type: "website",
    },
  };
}

export default async function ExperiencesRootPage() {
  const api = await getExperiencesData();

  /* ── section data — jo API me nahi, wo empty → section render hi nahi hota ── */
  const stats = api?.stats?.items ?? [];

  const themes = (api?.category ?? []).map((t: any) => ({
    name: t.name,
    slug: t.slug,
    image: t.image ?? FALLBACK_BANNER,
    image_alt: t.image_alt ?? t.name,
    description: t.description ?? "",
  }));

  const bestTime = (api?.best_time?.list ?? []).map((b: any, i: number) => ({
    id: i + 1,
    title: b.label ?? b.title ?? "",
    description: b.text ?? b.description ?? "",
  }));

  const signature = (api?.signature_experiences?.items ?? []).map((s: any, i: number) => ({
    title: s.title,
    slug: s.slug ?? `signature-${i}`,
    image: s.image ?? FALLBACK_BANNER,
    image_alt: s.image_alt ?? s.title,
    toursCount: s.tours_count ?? "",
    description: s.description ?? "",
    popularTag: s.popular_tag ?? "",
    href:
      s.url ??
      (s.state_slug && s.city_slug
        ? `/${s.state_slug}/${s.city_slug}/experiences`
        : "/tour-packages"),
  }));

  // featured + grid — popular_states aaye to wahi; warna destinations field se grid
  const featuredStates = (api?.popular_states?.featured ?? []).map((f: any) => ({
    name: f.name,
    slug: f.slug,
    image: f.image ?? FALLBACK_BANNER,
    toursCount: f.tours_count ?? undefined,
    href: f.city_slug ? `/${f.slug}/${f.city_slug}/experiences` : `/${f.slug}/experiences`,
  }));
  const statesGrid = (api?.states ?? []).map((d: any) => ({
    name: d.name,
    slug: d.slug,
    image: d.image ?? FALLBACK_BANNER,
    href: `/${d.slug}/experiences`,
  }));

  const whyChoose = api?.why_choose;
  // items strings ya objects dono ho sakte hain
  const whyItems = (whyChoose?.items ?? []).map((w: any) =>
    typeof w === "string" ? { title: w } : { title: w.title, description: w.description ?? undefined }
  );

  const faqs = (api?.faqs?.list ?? []).map((f: any, i: number) => ({
    id: i + 1,
    question: f.question,
    answer: f.answer,
  }));

  return (
    <>
      <Banner
        title={api?.banner?.title ?? "Travel Experiences Across India"}
        subtitle={api?.banner?.banner_text ?? ""}
        bgImage={api?.banner?.image ?? FALLBACK_BANNER}
      />

      <div className={styles.pageLayout}>
        <div className={styles.mainCol}>
          <Breadcrumb
            items={[
              { label: "Home", href: "/" },
              { label: "Experiences", href: "/experiences" },
            ]}
          />

          <h1 className={styles.pageH1}>
            {api?.meta?.h1_heading ?? api?.banner?.title ?? "Travel Experiences Across India"}
          </h1>

          {/* Intro — API ka HTML */}
          {api?.short_description && (
            <ReadMoreHtml
              html={api.short_description}
              className={`cms-intro ${styles.intro}`}
            />
          )}

          {/* Stats row */}
          {stats.length > 0 && <ExperienceStats items={stats} />}

          {/* Popular Themes — 2×2 cards */}
          {themes.length > 0 && (
            <ExperienceThemesGrid heading="Popular Themes in India" themes={themes} />
          )}

          {/* Best Time */}
          {bestTime.length > 0 && (
            <IconCards
              heading={api?.best_time?.title ?? "Best Time to Visit India"}
              items={bestTime}
            />
          )}

          {/* Signature slider */}
          {signature.length > 0 && (
            <SignatureExperiences
              heading={api?.signature_experiences?.title ?? "Signature India Experiences"}
              items={signature}
            />
          )}

          {/* Featured + states grid */}
          {(featuredStates.length > 0 || statesGrid.length > 0) && (
            <PopularStatesSection
              heading={api?.popular_states?.title ?? "Explore Popular States"}
              featured={featuredStates}
              states={statesGrid}
            />
          )}

          {/* Why choose */}
          {whyItems.length > 0 && (
            <FestivalWhyChoose
              heading={whyChoose?.title ?? "Why Choose Indian Panorama?"}
              subtext={whyChoose?.description ?? undefined}
              items={whyItems}
            />
          )}

          {/* FAQ */}
          {faqs.length > 0 && (
            <div className={styles.faqWrap}>
              <FaqSection
                heading={api?.faqs?.title ?? "FAQ's"}
                subtext={api?.faqs?.sub_title ?? undefined}
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
