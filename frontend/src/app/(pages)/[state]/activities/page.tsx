import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { fetchActivitiesState } from "@/services/activitiesService";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import ActivityPopularInIndia from "@/app/components/activities/ActivityPopularInIndia";
import StateActivityIntroAPI from "@/app/components/activities/StateActivityIntroAPI";
import StateTopActivities from "@/app/components/activities/StateTopActivities";
import StatePopularExperience from "@/app/components/activities/StatePopularExperience";
import StateTopDestinations from "@/app/components/activities/StateTopDestinations";
import StateFeaturedCategory from "@/app/components/activities/StateFeaturedCategory";
import StateWaterfallGallery from "@/app/components/activities/StateWaterfallGallery";
import styles from "./page.module.css";

export const dynamic = "force-dynamic";

type Props = { params: Promise<{ state: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { state } = await params;
  const data = await fetchActivitiesState(state);
  if (!data) return {};
  const stateName = data.banner.title
    .replace(" Tourist Activity", "")
    .replace(" Activities", "");
  return {
    title: data.meta.meta_title ?? `Activities in ${stateName} | Indian Panorama`,
    description: data.meta.meta_description ?? data.short_description?.replace(/<[^>]*>/g, "").slice(0, 160) ?? undefined,
    keywords: data.meta.meta_keywords ?? undefined,
    alternates: { canonical: `https://www.indianpanorama.in/${state}/activities` },
    robots: { index: true, follow: true },
  };
}

export default async function StateActivitiesPage({ params }: Props) {
  const { state } = await params;
  const data = await fetchActivitiesState(state);
  if (!data) notFound();

  const bannerTitle = data.banner.title;
  const stateName = bannerTitle
    .replace(" Tourist Activity", "")
    .replace(" Activities", "");
  const pageH1 = data.meta.h1_heading ?? `${stateName} Tour Activities`;

  const hasTopActivities    = data.top_activities && data.top_activities.items.length > 0;
  const hasPopularExp       = data.popular_experience && data.popular_experience.items.length > 0;
  const hasTopDestinations  = data.top_destinations && data.top_destinations.items.length > 0;
  const hasFeaturedCategory = data.featured_category && data.featured_category.items.length > 0;
  const hasWaterfalls       = data.waterfalls && data.waterfalls.items.length > 0;
  const hasThingsToDo       = data.top_things_to_do && data.top_things_to_do.items.length > 0;
  const hasFaqs             = data.faqs && data.faqs.items.length > 0;
  const hasPopularPackages  = data.popular_packages && data.popular_packages.items.length > 0;

  return (
    <>
      {/* ── Banner ── */}
      <section className={styles.banner}>
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          src={data.banner.image}
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

          {/* Breadcrumb + H1 + Intro */}
          <div className={styles.introBlock}>
            <Breadcrumb items={[
              { label: "Home",       href: "/" },
              { label: stateName,    href: `/${state}` },
              { label: "Activities", href: `/${state}/activities` },
            ]} />
            <h1 className={styles.h1}>{pageH1}</h1>
            <StateActivityIntroAPI
              shortDescription={data.short_description}
              aboutImage={data.about_image}
              aboutImageAlt={data.about_image_alt}
            />
          </div>

          {/* ── Top Activities ── */}
          {hasTopActivities && (
            <>
              <StateTopActivities
                title={data.top_activities!.title}
                stateSlug={state}
                items={data.top_activities!.items}
              />
            </>
          )}

          {/* ── Popular Experience ── */}
          {hasPopularExp && (
            <>
              <StatePopularExperience
                title={data.popular_experience!.title}
                items={data.popular_experience!.items}
              />
            </>
          )}

          {/* ── Top Destinations Swiper ── */}
          {hasTopDestinations && (
            <>
              <StateTopDestinations
                title={data.top_destinations!.title}
                stateSlug={state}
                items={data.top_destinations!.items}
              />
            </>
          )}

          {/* ── Featured Category ── */}
          {hasFeaturedCategory && (
            <>
              <StateFeaturedCategory
                title={data.featured_category!.title}
                stateSlug={state}
                items={data.featured_category!.items}
              />
            </>
          )}

          {/* ── Waterfalls ── */}
          {hasWaterfalls && (
            <>
              <StateWaterfallGallery
                title={data.waterfalls!.title}
                items={data.waterfalls!.items}
              />
            </>
          )}

          {/* ── Top Things To Do ── */}
          {hasThingsToDo && (
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

      {/* ── Popular Packages — full width ── */}
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
