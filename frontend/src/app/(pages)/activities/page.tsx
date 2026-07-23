import type { Metadata } from "next";
import { fetchActivitiesLanding } from "@/services/activitiesService";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import ActivityIntroStats from "@/app/components/activities/ActivityIntroStats";
import ActivityCategoryGrid from "@/app/components/activities/ActivityCategoryGrid";
import ActivityByCategoryList from "@/app/components/activities/ActivityByCategoryList";
import ActivityPerfectFor from "@/app/components/activities/ActivityPerfectFor";
import ActivitySeasonalSection from "@/app/components/activities/ActivitySeasonalSection";
import ActivityPopularStates from "@/app/components/activities/ActivityPopularStates";
import ActivityPopularCities from "@/app/components/activities/ActivityPopularCities";
import ActivityPopularInIndia from "@/app/components/activities/ActivityPopularInIndia";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import styles from "./page.module.css";

export const dynamic = "force-dynamic";

export async function generateMetadata(): Promise<Metadata> {
  const data = await fetchActivitiesLanding();
  return {
    title: data?.meta.meta_title ?? "All Tour Activities in India | Adventure, Wildlife, Culture | Indian Panorama",
    description: data?.meta.meta_description ?? "Explore 500+ curated tour activities across India — adventure trekking, wildlife safaris, wellness yoga, water sports, cultural tours and more. Book with Indian Panorama.",
    keywords: data?.meta.meta_keywords ?? undefined,
    alternates: { canonical: "https://www.indianpanorama.in/activities" },
    robots: { index: true, follow: true },
  };
}

export default async function ActivitiesRootPage() {
  const data = await fetchActivitiesLanding();

  const bannerImage = data?.banner?.image ?? "/images/about-banner-pages.jpg";
  const bannerAlt = data?.banner?.image_alt ?? "";
  const bannerTitle = data?.meta.h1_heading ?? data?.banner?.title ?? "All Tour Activities";

  return (
    <>
      {/* Banner */}
      <section className={styles.banner}>
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          src={bannerImage}
          alt={bannerAlt}
          className={styles.bannerImg}
          fetchPriority="high"
        />
        {/* overlay + text hidden — uncomment to restore
        <div className={styles.bannerOverlay} />
        <div className={styles.bannerContent}>
          <p className={styles.bannerTitle}>{bannerTitle}</p>
          {data?.banner?.text && <p className={styles.bannerSub}>{data.banner.text}</p>}
        </div>
        */}
      </section>

      {/* Main layout: content left + sidebar right */}
      <div className={styles.pageLayout}>
        <div className={styles.mainCol}>

          <div className={styles.introBlock}>
            <Breadcrumb items={[
              { label: "Home", href: "/" },
              { label: "Activities", href: "/activities" },
            ]} />
            <h1 className={styles.pageH1}>
              {data?.meta.h1_heading ?? data?.banner?.title ?? "All Tour Activities in India"}
            </h1>
            <ActivityIntroStats
              shortDescription={data?.short_description ?? null}
              stats={data?.stats ?? { image: null, image_alt: null, items: [] }}
            />
          </div>


          {/* 3×3 Category Grid (activity types) */}
          {data?.activity_types?.items?.length ? (
            <>
              <ActivityCategoryGrid
                title={data.activity_types.title}
                items={data.activity_types.items}
              />
            </>
          ) : null}

          {/* By Category list */}
          {data?.categories?.items?.length ? (
            <>
              <ActivityByCategoryList
                title={data.categories.title}
                items={data.categories.items}
              />
            </>
          ) : null}

          {/* Perfect For */}
          {data?.perfect_for?.items?.length ? (
            <>
              <ActivityPerfectFor
                title={data.perfect_for.title}
                items={data.perfect_for.items}
              />
            </>
          ) : null}

          {/* Seasonal */}
          {data?.seasonal_activities?.items?.length ? (
            <>
              <ActivitySeasonalSection
                title={data.seasonal_activities.title}
                items={data.seasonal_activities.items}
              />
            </>
          ) : null}

          {/* Popular States */}
          {data?.top_activities_destination?.items?.length ? (
            <>
              <ActivityPopularStates
                title={data.top_activities_destination.title}
                items={data.top_activities_destination.items}
              />
            </>
          ) : null}

          {/* Popular Cities */}
          {data?.city_experiences?.items?.length ? (
            <>
              <ActivityPopularCities
                title={data.city_experiences.title}
                items={data.city_experiences.items}
              />
            </>
          ) : null}

          {/* FAQs */}
          {data?.faqs?.items?.length ? (
            <FaqSection
              heading={data.faqs.title}
              subtext={data.faqs.sub_title ?? ""}
              items={data.faqs.items.map((f, i) => ({ id: i + 1, question: f.question, answer: f.answer }))}
            />
          ) : null}

        </div>

        <aside className={styles.sidebarCol}>
          <SidebarForm />
        </aside>
      </div>

      {/* Popular Activities in India — full width grey bg */}
      {data?.popular_packages?.items?.length ? (
        <div className={styles.popularWrap}>
          <div className={styles.popularInner}>
            <ActivityPopularInIndia
              title={data.popular_packages.title}
              items={data.popular_packages.items}
            />
          </div>
        </div>
      ) : null}
    </>
  );
}
