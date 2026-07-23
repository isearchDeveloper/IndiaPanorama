import FaqSection from "@/app/components/common/FaqSection";
import PartnerSlider from "@/app/components/common/PartnerSlider";
import SidebarForm from "@/app/components/common/SidebarForm";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import Banner from "@/app/components/common/Banner";
import StatesSwiper from "@/app/components/common/StatesSwiper";
import RegionCards from "./RegionCards";
import CategoryCards from "./CategoryCards";
import TATopTours from "./TATopTours";
import TAPopularStates from "./TAPopularStates";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import styles from "./TouristAttractionHomeLayout.module.css";
import IndiaPopularPackages from "../india/IndiaPopularPackages";

type TopTour      = { id: number; name: string; image: string; label: string; href: string; };
type Region       = { id: number; title: string; slug: string; icon: string; description: string; href: string; };
type PopularState = { id: number; name: string; slug: string; image: string; label: string; isNew: boolean; href: string; toursCount?: number; };
type PopularCity  = { id: number; name: string; image: string; href: string; toursCount?: number; description?: string; popular?: string[]; };
type Category     = { id: number; title: string; slug: string; image: string; description: string; count?: number; href: string; };
type FaqItem      = { id: number; question: string; answer: string; };

type Props = {
  h1?: string;
  bannerTitle?: string;
  bannerImage?: string;
  bannerSubtitle?: string;
  intro?: string;
  topToursTitle?: string;
  topTours: TopTour[];
  regionsTitle?: string;
  regions: Region[];
  popularStatesTitle?: string;
  popularStates: PopularState[];
  popularCitiesTitle?: string;
  popularCities: PopularCity[];
  categoriesTitle?: string;
  categories: Category[];
  faqsTitle?: string;
  faqsSubtitle?: string;
  faqs: FaqItem[];
};

export default function TouristAttractionHomeLayout({
  h1,
  bannerTitle = "Tourist Attractions",
  bannerImage,
  bannerSubtitle,
  intro,
  topToursTitle = "Top Tourist Attraction Tours",
  topTours,
  regionsTitle = "Tourist Attraction By Region",
  regions,
  popularStatesTitle = "Explore Popular States",
  popularStates,
  popularCitiesTitle = "Popular City Experiences",
  popularCities,
  categoriesTitle = "Tourist Attraction By Category",
  categories,
  faqsTitle = "FAQ's",
  faqsSubtitle,
  faqs,
}: Props) {
  const pageH1 = h1 || bannerTitle;

  return (
    <>
      <Banner
        title={bannerTitle}
        subtitle={bannerSubtitle ?? "Indian Panorama"}
        bgImage={bannerImage || "/images/about-banner-pages.jpg"}
        textPosition="center"
      />

      <div className={styles.contentLayout}>
        <div className={styles.leftCol}>

          <Breadcrumb items={[
            { label: "Home", href: "/" },
            { label: "Tourist Attractions", href: "/tourist-attractions" },
          ]} />
          <div>
            <h1 className={styles.pageTitle}>{pageH1}</h1>

            {/* CMS se HTML aata hai (<br/>, <span>, entities) — plain text nahi,
                isliye dangerouslySetInnerHTML wale ReadMoreHtml se render */}
            {intro && (
              <div className={styles.introSection}>
                <ReadMoreHtml html={intro} className={`cms-intro ${styles.intro}`} />
              </div>
            )}
          </div>

          <TATopTours title={topToursTitle} items={topTours} />

          <RegionCards heading={regionsTitle} items={regions} />

          <TAPopularStates title={popularStatesTitle} items={popularStates} />

          {popularCities.length > 0 && (
            <StatesSwiper
              title={popularCitiesTitle}
              viewAllHref="/tourist-attractions"
              ctaLabel="View Attractions →"
              items={popularCities.map((c) => ({
                id: c.id,
                name: c.name,
                image: c.image ?? null,
                href: c.href,
                toursCount: c.toursCount ?? null,
                description: c.description ?? null,
                popular: c.popular,
              }))}
            />
          )}

          {categories.length > 0 && (
            <CategoryCards heading={categoriesTitle} items={categories} columns={2} />
          )}

        </div>

        <aside className={styles.rightCol} aria-label="Enquiry form">
          <SidebarForm />
        </aside>
      </div>

      {faqs.length > 0 && (
        <FaqSection
          heading={faqsTitle}
          subtext={faqsSubtitle ?? "Find answers to the most common questions about tourist attractions in India."}
          items={faqs}
          sideImage={{ src: "/images/faq-side-image.webp", alt: "Tourist Attractions India" }}
        />
      )}
      
    </>
  );
}
