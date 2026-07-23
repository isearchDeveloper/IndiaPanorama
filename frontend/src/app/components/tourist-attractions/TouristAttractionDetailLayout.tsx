import Breadcrumb from "@/app/components/common/Breadcrumb";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import PartnerSlider from "@/app/components/common/PartnerSlider";
import AttractionBanner from "./AttractionBanner";
import QuickFacts from "./QuickFacts";
import WhyVisit from "./WhyVisit";
import ThingsToDo from "./ThingsToDo";
import GallerySection from "./GallerySection";
import NearbyAttractions from "./NearbyAttractions";
import RelatedAttractions from "./RelatedAttractions";
import styles from "./TouristAttractionDetailLayout.module.css";

type AttractionDetail = {
  slug: string; name: string; stateSlug: string; citySlug: string;
  intro?: string;
  banner: { title: string; subtitle?: string; image: string };
  quickFacts: { label: string; value: string }[];
  whyVisit: { title: string; description: string; points: string[]; image: string };
  thingsToDo: { id: number; title: string; description: string }[];
  gallery: { id: number; src: string; alt: string; span?: "wide" | "tall" | "normal" }[];
  nearbyAttractions: { id: number; name: string; image: string; description: string; href: string }[];
  faqs: { id: number; question: string; answer: string }[];
};

interface Props {
  data: AttractionDetail;
  relatedAttractions?: AttractionDetail[];
}

export default function TouristAttractionDetailLayout({ data, relatedAttractions = [] }: Props) {
  const breadcrumbs = [
    { label: "Home", href: "/" },
    { label: "Tourist Attractions", href: "/tourist-attractions" },
    {
      label: data.stateSlug.replace(/-/g, " ").replace(/\b\w/g, (c) => c.toUpperCase()),
      href: `/${data.stateSlug}/tourist-attractions`,
    },
    {
      label: data.citySlug.replace(/-/g, " ").replace(/\b\w/g, (c) => c.toUpperCase()),
      href: `/${data.stateSlug}/${data.citySlug}/tourist-attractions`,
    },
    { label: data.name, href: `/tourist-attractions/${data.slug}` },
  ];

  const faqItems = data.faqs.map((f) => ({
    id: f.id,
    question: f.question,
    answer: f.answer,
  }));

  const relatedItems = relatedAttractions
    .filter((a) => a.slug !== data.slug)
    .map((a) => ({
      id: a.slug.length,
      name: a.name,
      image: a.banner.image,
      description: (a.intro ?? "").slice(0, 80) + "…",
      href: `/tourist-attractions/${a.slug}`,
    }));

  const nearbyItems = data.nearbyAttractions.map((n) => ({
    id: n.id,
    name: n.name,
    image: n.image,
    description: n.description,
    href: n.href,
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

          {data.intro && <p className={styles.intro}>{data.intro}</p>}


          {data.quickFacts.length > 0 && (
            <QuickFacts heading="Quick Information" items={data.quickFacts} />
          )}


          <WhyVisit
            title={data.whyVisit.title}
            description={data.whyVisit.description}
            points={data.whyVisit.points}
            image={data.whyVisit.image}
          />

          {data.thingsToDo.length > 0 && (
            <>
              <ThingsToDo
                heading={`Things To Do At ${data.name}`}
                items={data.thingsToDo}
              />
            </>
          )}

          {data.gallery.length > 0 && (
            <>
              <GallerySection heading="Gallery" images={data.gallery} />
            </>
          )}

          {nearbyItems.length > 0 && (
            <>
              <NearbyAttractions
                heading="Nearby Attractions"
                viewAllHref={`/${data.stateSlug}/${data.citySlug}/tourist-attractions`}
                items={nearbyItems}
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
          heading={`FAQ's`}
          subtext={`Find answers to common questions about visiting ${data.name}, including timings, entry details, tea estate tours, nearby attractions, and travel tips.`}
          items={faqItems}
          sideImage={{ src: "/images/faq-side-image.webp", alt: data.name }}
        />
      )}

      {relatedItems.length > 0 && (
        <RelatedAttractions
          heading={`Explore More Attractions in ${data.citySlug.replace(/-/g, " ").replace(/\b\w/g, (c) => c.toUpperCase())}`}
          items={[...relatedItems, ...nearbyItems.map((n, i) => ({ ...n, id: i + 100 }))]}
        />
      )}

      <PartnerSlider />
    </>
  );
}

