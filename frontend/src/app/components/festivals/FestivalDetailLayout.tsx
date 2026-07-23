import Breadcrumb from "@/app/components/common/Breadcrumb";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import HowToReach from "@/app/components/city-guide/HowToReach";
import FestivalDetailBanner from "./detail/FestivalDetailBanner";
import FestivalDetailIntro from "./detail/FestivalDetailIntro";
import FestivalHighlightsGrid from "./detail/FestivalHighlightsGrid";
import FestivalKeyExperiences from "./detail/FestivalKeyExperiences";
import FestivalPopularPlaces from "./detail/FestivalPopularPlaces";
import FestivalLongDescription from "./detail/FestivalLongDescription";
import FestivalPackages from "./detail/FestivalPackages";
import FestivalWhyVisit from "./detail/FestivalWhyVisit";
import FestivalStats from "./detail/FestivalStats";
import FestivalExploreMore from "./detail/FestivalExploreMore";
import type { BreadcrumbItem } from "@/app/components/common/Breadcrumb";
import type { FestivalDetailData } from "@/services/festivalsService";
import styles from "./FestivalDetailLayout.module.css";

interface Props {
  data: FestivalDetailData;
  breadcrumbs: BreadcrumbItem[];
}

export default function FestivalDetailLayout({ data, breadcrumbs }: Props) {
  const pageH1          = data.meta.h1_heading ?? data.banner.title;
  const hasStats        = data.stats && data.stats.length > 0;
  const hasHighlights   = data.highlights && data.highlights.items.length > 0;
  const hasKeyExp       = data.key_experiences && data.key_experiences.items.length > 0;
  const hasPopPlaces    = data.popular_places && data.popular_places.items.length > 0;
  const hasHowToReach   = data.how_to_reach && data.how_to_reach.length > 0;
  const hasPackages     = data.festival_packages && data.festival_packages.items.length > 0;
  const hasWhyVisit     = data.why_visit && data.why_visit.items.length > 0;
  const hasExploreMore  = data.explore_more_destinations && data.explore_more_destinations.items.length > 0;
  const hasFaqs         = data.faqs && data.faqs.list.length > 0;

  return (
    <>
      <FestivalDetailBanner
        title={data.banner.title}
        image={data.banner.image}
        imageAlt={data.banner.image_alt}
        stateName={data.state?.name}
      />

      <div className={styles.contentLayout}>
        <div className={styles.leftCol}>
          <Breadcrumb items={breadcrumbs} />

          {/* ── Intro: H1 + short desc + intro image ── */}
          <FestivalDetailIntro
            h1={pageH1}
            shortDescription={data.short_description}
            introImage={data.intro_image}
            introImageAlt={data.intro_image_alt}
            bannerTitle={data.banner.title}
          />

          {/* ── Stats ── */}
          {hasStats && (
            <>
              <FestivalStats items={data.stats!} />
            </>
          )}

          {/* ── Highlights grid ── */}
          {hasHighlights && (
            <>
              <FestivalHighlightsGrid items={data.highlights!.items} />
            </>
          )}

          {/* ── Key Experiences pills ── */}
          {hasKeyExp && (
            <>
              <FestivalKeyExperiences
                title={data.key_experiences!.title}
                items={data.key_experiences!.items}
              />
            </>
          )}

          {/* ── Popular Places ovals ── */}
          {hasPopPlaces && (
            <>
              <FestivalPopularPlaces
                title={data.popular_places!.title}
                items={data.popular_places!.items}
              />
            </>
          )}

          {/* ── Long description ── */}
          {data.long_description && (
            <>
             
              <FestivalLongDescription html={data.long_description} />
            </>
          )}

          {/* ── How To Reach ── */}
          {hasHowToReach && (
            <>
              <HowToReach
                heading="How to Reach?"
                items={data.how_to_reach.map((item, i) => ({
                  id: i + 1,
                  mode: item.mode,
                  description: item.description,
                }))}
              />
            </>
          )}

          {/* ── Festival Packages ── */}
          {hasPackages && (
            <>
             
              <FestivalPackages
                title={data.festival_packages!.title}
                items={data.festival_packages!.items}
              />
            </>
          )}

          {/* ── Why Visit ── */}
          {hasWhyVisit && (
            <>
              <FestivalWhyVisit
                title={data.why_visit!.title}
                items={data.why_visit!.items}
              />
            </>
          )}

       

          {/* ── FAQs ── */}
          {hasFaqs && (
            <>
              <FaqSection
                heading={data.faqs!.title}
                items={data.faqs!.list.map((f, i) => ({ id: i + 1, question: f.question, answer: f.answer }))}
              />
            </>
          )}
        </div>

        <aside className={styles.rightCol}>
          <SidebarForm />
        </aside>

          
      </div>
       {/* ── Explore More Destinations ── */}
          {hasExploreMore && (
            <>
              <FestivalExploreMore
                title={data.explore_more_destinations!.title}
                items={data.explore_more_destinations!.items}
              />
            </>
          )}
    </>
  );
}
