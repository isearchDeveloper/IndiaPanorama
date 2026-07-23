import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import styles from "./CityGuideLayout.module.css";
import AttractionGrid from "./AttractionGrid";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import CityGuideBanner from "./CityGuideBanner";
import CityGuideAbout from "./CityGuideAbout";
import QuickFacts from "./QuickFacts";
import DestinationCards from "./DestinationCards";
import PackageCards from "./PackageCards";
import ThingsToDo from "./ThingsToDo";
import HowToReach from "./HowToReach";
import TravelTips from "./TravelTips";
import FairsAndFestivals from "./FairsAndFestivals";
import RichTextSection from "./RichTextSection";

type CityGuideData = {
  attractions: any[];
  banner?: { title?: string; banner_text?: string; image?: string };
  short_description?: string;
  quick_facts?: { label: string; text: string }[];
  packages?: any[];
  cities?: {
    description: string; slug: string; name: string; image?: string 
}[];
  top_tourist_places?: { name: string; image?: string; description?: string; category?: string }[];
  things_to_do?: {
    approx_cost: any;
    t: any; title: string; description: string; duration?: string; best_for?: string; cost?: string 
}[];
  how_to_reach?: { mode: string; description: string }[];
  travel_tips?: string;
  festivals?: { intro?: string; list?: any[] };
  things_to_know?: string;
  religious_tourism?: { intro?: string };
  souvenirs?: string;
  popular_dishes?: string;
  faqs?: { title?: string; list?: any[] };
};

interface Props {
  data: CityGuideData;
  state: string;
  city?: string;
}

export default function  CityGuideLayout({ data, state, city }: Props) {

  const name = data.banner?.title ?? "";

  const stateLabel = state.replace(/-/g, " ").replace(/\b\w/g, c => c.toUpperCase());
  const cityLabel  = city ? city.replace(/-/g, " ").replace(/\b\w/g, c => c.toUpperCase()) : "";
  const breadcrumbs = [
    { label: "Home", href: "/" },
    { label: stateLabel, href: `/${state}` },
    ...(city ? [{ label: cityLabel, href: `/${state}/${city}` }] : []),
  ];

  const attractions = (data.attractions ?? []).map((c, i) => ({
    id: i + 1,
    name: c.title,
    image: c.image ?? "",
    description: c.description ?? "",
    category: "City",
    href: `/${state}/${c.city}/${c.slug}`,
  }));

  const touristAttractions = (data.top_tourist_places ?? []).map((p, i) => ({
    id: i + 1,
    name: p.name,
    image: p.image ?? "",
    description: p.description ?? "",
    category: p.category ?? "",
  }));

  const thingsToDo = (data.things_to_do ?? []).map((t, i) => ({
    id: i + 1,
    title: t.title,
    description: t.description,
    duration: t.duration,
    bestFor: t.best_for,
    cost: t.approx_cost,
  }));

  const howToReach = (data.how_to_reach ?? []).map((h, i) => ({
    id: i + 1,
    mode: h.mode,
    description: h.description,
  }));

  const quickFacts = (data.quick_facts ?? []).map((f) => ({
    title: f.label,
    value: f.text,
  }));

  const faqList = Array.isArray(data.faqs?.list)
    ? data.faqs!.list.filter((f: any) => f?.question).map((f: any, i: number) => ({
        id: i + 1,
        question: f.question,
        answer: f.answer ?? "",
      }))
    : [];

  const richSections = [
    data.religious_tourism?.intro && { id: "religious-tourism", heading: "Religious Tourism", description: data.religious_tourism.intro },
    data.souvenirs && { id: "souvenirs", heading: "Souvenirs", description: data.souvenirs },
    data.popular_dishes && { id: "popular-dishes", heading: "Popular Dishes", description: data.popular_dishes },
  ].filter(Boolean) as { id: string; heading: string; description: string }[];

  return (
    <>
      <CityGuideBanner
        title={data.banner?.title ?? ""}
        description={data.banner?.banner_text}
        image={data.banner?.image ?? ""}
      />

      <div className={styles.contentLayout}>
        <div className={styles.leftCol}>

          <Breadcrumb items={breadcrumbs} />

          {data.short_description && (
            <CityGuideAbout title={name} content={data.short_description} />
          )}

          {quickFacts.length > 0 && (
            <>
              <QuickFacts heading={`Quick Facts & Information About ${name}`} items={quickFacts} />
            </>
          )}

          {(data.packages?.length ?? 0) > 0 && (
            <>
              <PackageCards
                heading={`${name} Tour Packages`}
                packages={data.packages}
              />
            </>
          )}

          {touristAttractions.length > 0 && (
            <>
              <AttractionGrid attractions={touristAttractions} heading={`Top Tourist Places to Visit in ${name}`} />
              {/* <DestinationCards heading={`${name} Destinations`} destinations={attractions} viewAllHref="/tour-packages" /> */}
            </>
          )}

          {thingsToDo.length > 0 && (
            <>
              <ThingsToDo heading={`Top Things to Do in ${name}`} items={thingsToDo} />
            </>
          )}

          {attractions.length > 0 && (
            <>
              <DestinationCards heading={`${name} Tourist Attractions`} destinations={attractions} viewAllHref="/tour-packages" showBadge={false} />
            </>
          )}

          {howToReach.length > 0 && (
            <>
              <HowToReach heading={`How To Reach ${name}?`} items={howToReach} />
            </>
          )}

          {data.travel_tips && (
            <>
              <TravelTips heading={`Important Travel Tips For ${name}`} tips={[data.travel_tips]} />
            </>
          )}

          {(data.festivals?.list && data.festivals.list.length > 0) && (
            <>
              <FairsAndFestivals
                heading={`Fairs And Festivals Of ${name}`}
                description={data.festivals.intro}
                festivalHeading={`${name} Festivals`}
                festivals={data.festivals.list.map((f: any, i: number) => ({
                  id: i,
                  name: f.title ?? f.name,
                  image: f.image,
                }))}
                viewAllHref="/tour-packages"
              />
            </>
          )}

          {data.things_to_know && (
            <>
              <TravelTips heading={`Things To Know Before Visiting ${name}`} tips={[data.things_to_know]} />
            </>
          )}

          {richSections.map((section) => (
            <div key={section.id}>
              <RichTextSection heading={section.heading} description={section.description} />
            </div>
          ))}

        </div>

        <aside className={styles.rightCol} aria-label="Enquiry form">
          <SidebarForm />
        </aside>
      </div>

      {faqList.length > 0 && (
        <FaqSection
          heading={data.faqs?.title ?? "Frequently Asked Questions"}
          items={faqList}
          sideImage={{ src: "/images/faq-side-image.webp", alt: name }}
        />
      )}
    </>
  );
}

