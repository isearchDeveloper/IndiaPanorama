import Breadcrumb from "@/app/components/common/Breadcrumb";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import PartnerSlider from "@/app/components/common/PartnerSlider";
import AttractionBanner from "./AttractionBanner";
import AttractionCards from "./AttractionCards";
import BestTimeCards from "./BestTimeCards";
import CategoryCards from "./CategoryCards";
import CityExperiencesSlider from "./CityExperiencesSlider";
import styles from "./TouristAttractionCityLayout.module.css";

type CityAttractionData = {
  slug: string; name: string; stateName: string; stateSlug: string;
  h1?: string; intro?: string;
  banner: { title: string; subtitle?: string; image: string };
  topAttractions: { id: number; name: string; image: string; description: string; href?: string }[];
  bestTime: { id: number; season: string; months: string; description: string; icon: string }[];
  categories: { id: number; title: string; slug: string; image: string; description: string; count?: number; href: string }[];
  cityExperiences: { id: number; name: string; image: string; description: string; label?: string; href?: string }[];
  faqs: { id: number; question: string; answer: string }[];
};

interface Props {
  data: CityAttractionData;
}

export default function TouristAttractionCityLayout({ data }: Props) {
  const breadcrumbs = [
    { label: "Home", href: "/" },
    { label: "Tourist Attractions", href: "/tourist-attractions" },
    {
      label: data.stateName,
      href: `/${data.stateSlug}/tourist-attractions`,
    },
    {
      label: `${data.name} Tourist Attractions`,
      href: `/${data.stateSlug}/${data.slug}/tourist-attractions`,
    },
  ];

  const faqItems = data.faqs.map((f) => ({
    id: f.id,
    question: f.question,
    answer: f.answer,
  }));

  return (
    <>
      <AttractionBanner
        title={data.banner.title}
        subtitle={data.banner.subtitle}
        image={data.banner.image}
      />

      <div className={styles.contentLayout}>
        <div className={styles.leftCol}>
          <Breadcrumb items={breadcrumbs} />

          <div className={styles.introBlock}>
            <h1 className={styles.h1}>{data.h1}</h1>
            {data.intro && <p className={styles.intro}>{data.intro}</p>}
          </div>

          {data.topAttractions.length > 0 && (
            <>
              <AttractionCards
                heading={`Top Attractions in ${data.name}`}
                items={data.topAttractions}
                columns={3}
              />
            </>
          )}

          {data.bestTime.length > 0 && (
            <>
              <BestTimeCards
                heading={`Best Time To Visit ${data.name}`}
                items={data.bestTime}
              />
            </>
          )}

          {data.categories.length > 0 && (
            <>
              <CategoryCards
                heading="Tourist Attraction By Category"
                items={data.categories}
                columns={2}
              />
            </>
          )}

          {data.cityExperiences.length > 0 && (
            <>
              <CityExperiencesSlider
                heading="Popular City Experiences"
                viewAllHref="#"
                items={data.cityExperiences.map((e, i) => ({
                  id: e.id,
                  name: e.name,
                  image: e.image,
                  description: e.description,
                  label: e.label,
                  href: e.href ?? "#",
                }))}
              />
            </>
          )}
        </div>

        <aside className={styles.rightCol} aria-label="Enquiry form">
          <SidebarForm />
        </aside>
      </div>

      {faqItems.length > 0 && (
        <FaqSection
          heading="FAQ's"
          subtext={`Find answers to the most common questions about our India tour packages, travel planning, bookings, accommodations, and customized holiday experiences.`}
          items={faqItems}
          sideImage={{ src: "/images/faq-side-image.webp", alt: data.name }}
        />
      )}

      <PartnerSlider />
    </>
  );
}

