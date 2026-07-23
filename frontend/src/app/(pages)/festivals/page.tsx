import type { Metadata } from "next";
import { notFound } from "next/navigation";
import Banner from "@/app/components/common/Banner";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import FestivalStats from "@/app/components/festivals/FestivalStats";
import FestivalGrid from "@/app/components/festivals/FestivalGrid";
import FestivalStateCards from "@/app/components/festivals/FestivalStateCards";
import FestivalMonths from "@/app/components/festivals/FestivalMonths";
import FestivalWhyChoose from "@/app/components/festivals/FestivalWhyChoose";
import FestivalTourPackages from "@/app/components/festivals/FestivalTourPackages";
import { fetchFestivalsPage } from "@/services/festivalsService";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import styles from "./FestivalsPage.module.css";

export const dynamic = "force-dynamic";

export async function generateMetadata(): Promise<Metadata> {
  const data = await fetchFestivalsPage();
  if (!data) {
    return {
      title: "Fairs & Festivals of India | Indian Panorama",
      description: "Explore India's vibrant fairs and festivals. Plan your festival tour with Indian Panorama.",
    };
  }
  return {
    title: data.meta.meta_title ?? "Fairs & Festivals of India | Indian Panorama",
    description: data.meta.meta_description ?? undefined,
    keywords: data.meta.meta_keywords ?? undefined,
    alternates: { canonical: "https://www.indianpanorama.in/festivals" },
    robots: { index: true, follow: true },
  };
}

export default async function FestivalsPage() {
  const data = await fetchFestivalsPage();
  if (!data) notFound();

  // Map the new API data to the formats expected by the existing components
  // 1. Stats
  const statsList = data.stats.highlights.map((h, i) => ({
    id: i + 1,
    value: h.stat,
    label: h.label,
  }));

  // 2. Festivals (simple list of festivals)
  const festivalList = data.festivals.map((f) => ({
    name: f.name,
    slug: f.slug,
    image: f.image,
    image_alt: f.image_alt,
    // Add default values for required properties in the old mock Festival type
    description: "",
    date: "",
    duration: "",
    location: "",
    state: "",
    month: "",
  }));

  // 3. State cards
  const stateCardsList = data.explore_by_state.map((s) => ({
    name: s.state_name,
    slug: s.state_slug,
    image: s.image,
    description: `Explore the rich cultural celebrations and unique local heritage festivals in the state of ${s.state_name}.`,
    featuredFestivals: s.popular_festivals,
  }));

  // 4. Months configuration mapping
  const monthsData = data.explore_by_month.map((m) => ({
    slug: m.month_name.toLowerCase(),
    name: m.month_name,
    shortName: m.month_name,
    festivals: m.festivals.map((f) => f.name),
  }));

  const monthFestivalsList = data.explore_by_month.flatMap((m) =>
    m.festivals.map((f) => ({
      name: f.name,
      slug: f.name.toLowerCase().replace(/\s+/g, "-"),
      image: f.image,
      image_alt: f.image_alt,
      month: m.month_name.toLowerCase(),
      description: "",
      date: "",
      duration: "",
      location: "",
      state: f.state_slug,
    }))
  );


  // 6. Why Experience items mapping
  const whyItems = data.why_experience.items.map((item) => ({
    title: item.title,
    description: item.tagline || "",
  }));

  // 7. Tour Packages mapping
  const packagesList = data.festival_packages.map((pkg) => ({
    slug: pkg.slug,
    name: pkg.title,
    image: pkg.image,
    tag: pkg.location,
    days: pkg.duration_days,
    nights: pkg.duration_nights,
    href: `/tour/${pkg.slug}`,
  }));

  // 8. FAQs mapping
  const faqList = data.faqs.list.map((faq, i) => ({
    id: i + 1,
    question: faq.question,
    answer: faq.answer,
  }));

  return (
    <>
      <Banner
        title={data.banner.title}
        subtitle={data.banner.banner_text}
        bgImage={data.banner.image}
      />

      <div className={styles.contentLayout}>
        <div className={styles.leftCol}>
          <Breadcrumb
            items={[
              { label: "Home", href: "/" },
              { label: "Festivals", href: "/festivals" },
            ]}
          />

          <div className={styles.intro}>
            <h1 className={styles.h1}>{data.meta.h1_heading ?? data.banner.title}</h1>
            {data.short_description && (
              <ReadMoreHtml html={data.short_description} className={`cms-intro ${styles.introText}`} />
            )}
          </div>

          <FestivalStats stats={statsList} />

          <FestivalGrid festivals={festivalList as any} heading="Explore All Festivals" />


          <FestivalStateCards states={stateCardsList as any} heading="Explore Festivals by State" />


          <FestivalMonths months={monthsData as any} festivals={monthFestivalsList as any} heading="Explore Festivals by Months" />


          <FestivalWhyChoose items={whyItems} heading={data.why_experience.title} />
        </div>

        <aside className={styles.rightCol}>
          <SidebarForm />
        </aside>
      </div>

      <FaqSection
        heading={data.faqs.title}
        subtext={data.faqs.sub_title}
        items={faqList}
        sideImage={{ src: "/images/faq-side-image.webp", alt: "India Festivals" }}
      />

      <FestivalTourPackages packages={packagesList} heading={data.festival_packages.length > 0 ? "Festival Tour Packages" : undefined} />
    </>
  );
}
