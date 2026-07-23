import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { fetchActivitiesCity } from "@/services/activitiesService";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import ActivityPopularInIndia from "@/app/components/activities/ActivityPopularInIndia";
import MustDoSlider from "./MustDoSlider";
import WaterfallGallery from "./WaterfallGallery";
import ActivitiesCarousel from "./ActivitiesCarousel";
import CityTopAttractions from "@/app/components/activities/CityTopAttractions";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import styles from "./page.module.css";

export const dynamic = "force-dynamic";

type Props = { params: Promise<{ state: string; city: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { state, city } = await params;
  const data = await fetchActivitiesCity(state, city);
  if (!data) return {};
  const title = data.banner.title;
  return {
    title: data.meta.meta_title ?? `${title} | Indian Panorama`,
    description: data.meta.meta_description ?? data.short_description?.replace(/<[^>]*>/g, "").slice(0, 160) ?? undefined,
    keywords: data.meta.meta_keywords ?? undefined,
    alternates: { canonical: `https://www.indianpanorama.in/${state}/${city}/activities` },
    robots: { index: true, follow: true },
  };
}

export default async function CityActivitiesPage({ params }: Props) {
  const { state, city } = await params;
  const data = await fetchActivitiesCity(state, city);
  if (!data) notFound();

  const bannerTitle = data.banner.title;
  const cityLabel = bannerTitle
    .replace(" Tourist Activity", "")
    .replace(" Activities", "")
    .replace(" Activity", "");
  const bannerImage = data.banner.image ?? "/images/about-banner-pages.jpg";

  const hasTopActivities = data.top_activities && data.top_activities.items.length > 0;
  const hasActivitiesInCity = data.activities_in_city && Array.isArray(data.activities_in_city.items) && data.activities_in_city.items.length > 0;
  const hasWaterfalls = data.waterfalls && data.waterfalls.items.length > 0;
  const hasTopThingsToDo = data.top_things_to_do && data.top_things_to_do.items.length > 0;
  const hasTopAttractions = data.top_attractions && data.top_attractions.items.length > 0;
  const hasFaqs = data.faqs && data.faqs.items.length > 0;
  const hasPopularPackages = data.popular_packages && data.popular_packages.items.length > 0;

  return (
    <>
      {/* ── Banner ── */}
      <section className={styles.banner}>
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          src={bannerImage}
          alt={data.banner.image_alt ?? bannerTitle}
          className={styles.bannerImg}
          fetchPriority="high"
        />
        {/* overlay + text hidden — uncomment to restore
        <div className={styles.bannerOverlay} />
        <div className={styles.bannerContent}>
          <p className={styles.bannerTitle}>{bannerTitle}</p>
          <p className={styles.bannerSub}>Indian Panorama</p>
        </div>
        */}
      </section>

      {/* ── Main 2-col layout ── */}
      <div className={styles.layout}>
        <div className={styles.leftCol}>

          {/* Breadcrumb + H1 + About */}
          <div className={styles.introBlock}>
            <Breadcrumb items={[
              { label: "Home",       href: "/" },
              { label: state.replace(/-/g, " ").replace(/\b\w/g, (c) => c.toUpperCase()), href: `/${state}` },
              { label: cityLabel,    href: `/${state}/${city}` },
              { label: "Activities", href: `/${state}/${city}/activities` },
            ]} />
            <h1 className={styles.h1}>{data.meta.h1_heading ?? bannerTitle}</h1>
            {data.short_description && (
              <ReadMoreHtml html={data.short_description} className={styles.introDesc} />
            )}
          </div>

          {/* ── Must-do Experiences (top_activities) ── */}
          {hasTopActivities && (
            <>
              <MustDoSlider
                title={data.top_activities!.title}
                stateSlug={state}
                citySlug={city}
                items={data.top_activities!.items}
              />
            </>
          )}

          {/* ── Waterfalls ── */}
          {hasWaterfalls && (
            <>
              <WaterfallGallery
                title={data.waterfalls!.title}
                items={data.waterfalls!.items}
              />
            </>
          )}

          {/* ── Top Things To Do ── */}
          {hasTopThingsToDo && (
            <>
              <section>
                <h2 className={styles.sectionHeading}>{data.top_things_to_do!.title}</h2>
                <div className={styles.thingsList}>
                  {data.top_things_to_do!.items.map((item, i) => (
                    <div key={i} className={styles.thingCard}>
                      <div className={styles.thingArrow}>→</div>
                      <div className={styles.thingBody}>
                        <p className={styles.thingTitle}>{item.title}</p>
                        {item.description && (
                          <p className={styles.thingDesc}>{item.description}</p>
                        )}
                        <div className={styles.thingMeta}>
                          {item.duration_timing && (
                            <span className={styles.thingMetaItem}>
                              <span className={styles.thingMetaLabel}>Duration &amp; Timing:</span> {item.duration_timing}
                            </span>
                          )}
                          {item.best_for && (
                            <span className={styles.thingMetaItem}>
                              <span className={styles.thingMetaLabel}>Best For:</span> {item.best_for}
                            </span>
                          )}
                          {item.approximate_cost && (
                            <span className={styles.thingMetaItem}>
                              <span className={styles.thingMetaLabel}>Approximate Cost:</span> {item.approximate_cost}
                            </span>
                          )}
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              </section>
            </>
          )}

          {/* ── Top Attractions ── */}
          {hasTopAttractions && (
            <>
              <CityTopAttractions
                title={data.top_attractions!.title}
                stateSlug={state}
                citySlug={city}
                items={data.top_attractions!.items}
              />
            </>
          )}

          {/* ── Activities in City — full-width light bg carousel ── */}
          {hasActivitiesInCity && (
            <div className={styles.carouselWrap}>
              <div className={styles.carouselInner}>
                <ActivitiesCarousel
                  title={data.activities_in_city!.title}
                  subTitle={data.activities_in_city!.sub_title}
                  stateSlug={state}
                  citySlug={city}
                  items={data.activities_in_city!.items}
                />
              </div>
            </div>
          )}

          {/* ── FAQs ── */}
          {hasFaqs && (
            <>
              <FaqSection
                heading={data.faqs!.title ?? "Frequently Asked Questions"}
                subtext={data.faqs!.sub_title ?? ""}
                items={data.faqs!.items.map((f, i) => ({ id: i + 1, question: f.question, answer: f.answer }))}
              />
            </>
          )}

        </div>

        <aside className={styles.rightCol}>
          <SidebarForm />
        </aside>
      </div>

      {/* ── Popular Packages — full-width ── */}
      {hasPopularPackages && (
        <div className={styles.popularWrap}>
          <div className={styles.popularInner}>
            <ActivityPopularInIndia
              title={data.popular_packages!.title}
              items={data.popular_packages!.items}
            />
          </div>
        </div>
      )}
    </>
  );
}
