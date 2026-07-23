import Breadcrumb from "@/app/components/common/Breadcrumb";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import Banner from "@/app/components/common/Banner";
import StatesSwiper from "@/app/components/common/StatesSwiper";
import AttractionCards from "./AttractionCards";
import BestTimeCards from "./BestTimeCards";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import styles from "./TouristAttractionStateLayout.module.css";

type Attraction = { id: number; name: string; image: string; image_alt: string; description: string; href: string; };
type City       = { id: number; name: string; image: string | null; href: string; };
type BestTime   = { id: number; season: string; months: string; description: string; icon: string; };
type FaqItem    = { id: number; question: string; answer: string; };

type Props = {
  stateSlug: string;
  stateName: string;
  bannerImage?: string;
  bannerImageAlt?: string;
  h1?: string;
  intro?: string;
  attractionsTitle?: string;
  attractions: Attraction[];
  cities: City[];
  bestTimeTitle?: string;
  bestTime: BestTime[];
  faqsTitle?: string;
  faqsSubtitle?: string;
  faqs: FaqItem[];
};

export default function TouristAttractionStateLayout({
  stateSlug,
  stateName,
  bannerImage,
  h1,
  intro,
  attractionsTitle,
  attractions,
  cities,
  bestTimeTitle,
  bestTime,
  faqsTitle,
  faqsSubtitle,
  faqs,
}: Props) {
  const pageH1 = h1 || stateName;

  return (
    <>
      <Banner
        title={stateName}
        bgImage={bannerImage || "/images/about-banner-pages.jpg"}
        textPosition="center"
      />

      <div className={styles.contentLayout}>
        <div className={styles.leftCol}>
          <Breadcrumb items={[
            { label: "Home", href: "/" },
            { label: stateSlug.replace(/-/g, " ").replace(/\b\w/g, (c) => c.toUpperCase()), href: `/${stateSlug}` },
            { label: "Tourist Attractions", href: `/${stateSlug}/tourist-attractions` },
          ]} />

          <div className={styles.introBlock}>
            <h1 className={styles.h1}>{pageH1}</h1>
            {intro && <ReadMoreHtml html={intro} className={styles.intro} />}
          </div>

          {attractions.length > 0 && (
            <>
              <AttractionCards
                heading={attractionsTitle ?? `Explore ${stateName} Tourist Attractions`}
                items={attractions}
                columns={3}
              />
            </>
          )}

          {bestTime.length > 0 && (
            <>
              <BestTimeCards
                heading={bestTimeTitle ?? `Best Time To Visit ${stateName}`}
                items={bestTime}
              />
            </>
          )}

          {cities.length > 0 && (
            <>
              <StatesSwiper
                title={`Popular Cities in ${stateName}`}
                items={cities}
                ctaLabel="View Attractions →"
              />
            </>
          )}
        </div>

        <aside className={styles.rightCol} aria-label="Enquiry form">
          <SidebarForm />
        </aside>
      </div>

      {faqs.length > 0 && (
        <FaqSection
          heading={faqsTitle ?? "FAQ's"}
          subtext={faqsSubtitle ?? `Find answers to the most common questions about tourist attractions in ${stateName}.`}
          items={faqs}
          sideImage={{ src: "/images/faq-side-image.webp", alt: stateName }}
        />
      )}
    </>
  );
}
