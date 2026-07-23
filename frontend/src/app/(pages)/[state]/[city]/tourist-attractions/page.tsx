import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { fetchTouristAttractionCity } from "@/services/touristAttractions";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import Banner from "@/app/components/common/Banner";
import AttractionCards from "@/app/components/tourist-attractions/AttractionCards";
import BestTimeCards from "@/app/components/tourist-attractions/BestTimeCards";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import styles from "@/app/components/tourist-attractions/TouristAttractionStateLayout.module.css";

export const dynamic = "force-dynamic";

type Props = { params: Promise<{ state: string; city: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { state, city } = await params;
  const data = await fetchTouristAttractionCity(state, city);
  if (!data) return { title: "Not Found" };

  const cityName = data.banner.title;
  return {
    title: data.meta.meta_title ?? `${cityName} | Indian Panorama`,
    description: data.meta.meta_description ?? data.short_description?.replace(/<[^>]*>/g, "").slice(0, 160) ?? `Explore top tourist attractions in ${cityName} with Indian Panorama.`,
    keywords: data.meta.meta_keywords ?? undefined,
    alternates: { canonical: `https://www.indianpanorama.in/${state}/${city}/tourist-attractions` },
    robots: { index: true, follow: true },
  };
}

export default async function CityTouristAttractionsPage({ params }: Props) {
  const { state, city } = await params;
  const data = await fetchTouristAttractionCity(state, city);
  if (!data) notFound();

  const cityName = data.banner.title;
  const pageH1 = data.meta.h1_heading ?? data.banner.title ?? cityName;

  const attractions = (data.top_attractions?.items ?? []).map((item, i) => ({
    id: i + 1,
    name: item.name,
    image: item.image ?? "/images/kerala-tours-v2.webp",
    image_alt: item.image_alt ?? item.name,
    description: item.description ?? "",
    href: `/${item.state ?? state}/${item.city ?? city}/${item.slug}`,
  }));

  const bestTime = (data.best_time_to_visit?.items ?? []).map((item, i) => ({
    id: i + 1,
    season: item.period ?? "",
    months: "",
    description: item.description ?? "",
    icon: "🌤️",
  }));

  const faqs = (data.faqs?.items ?? []).map((f, i) => ({
    id: i + 1,
    question: f.question,
    answer: f.answer,
  }));

  return (
    <>
      <Banner
        title={cityName}
        bgImage={data.banner.image ?? "/images/about-banner-pages.jpg"}
        textPosition="center"
      />

      <div className={styles.contentLayout}>
        <div className={styles.leftCol}>
          <Breadcrumb items={[
            { label: "Home", href: "/" },
            { label: state.replace(/-/g, " ").replace(/\b\w/g, (c) => c.toUpperCase()), href: `/${state}` },
            { label: city.replace(/-/g, " ").replace(/\b\w/g, (c) => c.toUpperCase()), href: `/${state}/${city}` },
            { label: "Tourist Attractions", href: `/${state}/${city}/tourist-attractions` },
          ]} />

          <div className={styles.introBlock}>
            <h1 className={styles.h1}>{pageH1}</h1>
            {data.short_description && (
              <ReadMoreHtml html={data.short_description} className={styles.intro} />
            )}
          </div>

          {attractions.length > 0 && (
            <>
              <AttractionCards
                heading={data.top_attractions?.title ?? `Explore ${cityName} Tourist Attractions`}
                items={attractions}
                columns={3}
              />
            </>
          )}

          {bestTime.length > 0 && (
            <>
              <BestTimeCards
                heading={data.best_time_to_visit?.title ?? `Best Time To Visit ${cityName}`}
                items={bestTime}
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
          heading={data.faqs?.title ?? "FAQ's"}
          subtext={data.faqs?.sub_title ?? `Find answers to the most common questions about tourist attractions in ${cityName}.`}
          items={faqs}
          sideImage={{ src: "/images/faq-side-image.webp", alt: cityName }}
        />
      )}
    </>
  );
}
