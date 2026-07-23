import Breadcrumb from "@/app/components/common/Breadcrumb";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import Banner from "@/app/components/common/Banner";
import ThingsToDo from "@/app/components/city-guide/ThingsToDo";
import GalleryLightbox from "@/app/components/common/GalleryLightbox";
import NearbySwiper from "@/app/components/common/NearbySwiper";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import type { TADetailData } from "@/services/touristAttractions";
import styles from "./AttractionDetailContent.module.css";

interface BreadcrumbItem { label: string; href: string; }

interface Props {
  data: TADetailData;
  breadcrumbs: BreadcrumbItem[];
  /** base path for nearby/explore-more attraction links, e.g. `/kerala/munnar` */
  linkBase?: string;
}

export default function AttractionDetailContent({ data, breadcrumbs, linkBase = "/tourist-attractions" }: Props) {
  const name = data.banner.title;
  const pageH1 = data.meta.h1_heading ?? name;

  const nearbyItems = (data.nearby_attractions?.items ?? []).map((item, i) => ({
    id: i + 1,
    name: item.name,
    image: item.image,
    image_alt: item.image_alt,
    description: item.description,
    href: `${linkBase}/${item.slug}`,
  }));

  const exploreItems = (data.explore_more?.items ?? []).map((item, i) => ({
    id: i + 1,
    name: item.name,
    image: item.image,
    image_alt: item.name,
    description: "",
    href: `${linkBase}/${item.slug}`,
  }));

  const faqs = (data.faqs?.items ?? []).map((f, i) => ({
    id: i + 1,
    question: f.question,
    answer: f.answer,
  }));

  const qi = data.quick_information;

  return (
    <>
      <Banner
        title={name}
        subtitle={data.banner.tagline ?? undefined}
        bgImage={data.banner.image ?? "/images/about-banner-pages.jpg"}
        textPosition="center"
      />

      <div className={styles.contentLayout}>
        <div className={styles.leftCol}>

          <Breadcrumb items={breadcrumbs} />

          <div className={styles.introBlock}>
            <h1 className={styles.h1}>{pageH1}</h1>
            {data.short_description && (
              <ReadMoreHtml html={data.short_description} className={styles.intro} />
            )}
          </div>

          {/* Quick Information — tabhi dikhe jab kam se kam ek field me data ho */}
          {qi && (qi.location || qi.duration || qi.best_for || qi.best_season) && (
            <section className={styles.quickInfo}>
              <h2 className={styles.sectionHeading}>Quick Information</h2>
              <div className={styles.quickGrid}>
                {qi.location    && <div className={styles.quickCard}><span className={styles.quickLabel}>Location</span><span className={styles.quickValue}>{qi.location}</span></div>}
                {qi.duration    && <div className={styles.quickCard}><span className={styles.quickLabel}>Duration</span><span className={styles.quickValue}>{qi.duration}</span></div>}
                {qi.best_for    && <div className={styles.quickCard}><span className={styles.quickLabel}>Best For</span><span className={styles.quickValue}>{qi.best_for}</span></div>}
                {qi.best_season && <div className={styles.quickCard}><span className={styles.quickLabel}>Best Season</span><span className={styles.quickValue}>{qi.best_season}</span></div>}
              </div>
            </section>
          )}

          {/* Why Visit */}
          {data.why_visit && (
            <section className={styles.whyVisit}>
              <div className={styles.whyGrid}>
                <div className={styles.whyText}>
                  <h2 className={styles.sectionHeading}>{data.why_visit.title}</h2>
                  <ReadMoreHtml html={data.why_visit.description} className={styles.intro} />
                  {data.why_visit.highlights.length > 0 && (
                    <ul className={styles.highlights}>
                      {data.why_visit.highlights.map((h, i) => (
                        <li key={i} className={styles.highlightItem}>
                          <span className={styles.highlightDot} />
                          {h}
                        </li>
                      ))}
                    </ul>
                  )}
                </div>
                {data.why_visit.image && (
                  <div className={styles.whyImgWrap}>
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img
                      src={data.why_visit.image}
                      alt={data.why_visit.image_alt ?? name}
                      className={styles.whyImg}
                      loading="lazy"
                      decoding="async"
                    />
                  </div>
                )}
              </div>
            </section>
          )}

          {/* Things To Do */}
          {data.things_to_do && data.things_to_do.items.length > 0 && (
            <ThingsToDo
              heading={data.things_to_do.title}
              items={data.things_to_do.items.map((item, i) => ({
                id: i + 1,
                title: item.title,
                description: item.description,
              }))}
            />
          )}

          {/* Gallery */}
          {data.gallery && data.gallery.length > 0 && (
            <GalleryLightbox images={data.gallery} />
          )}

          {/* Nearby Attractions */}
          {nearbyItems.length > 0 && (
            <NearbySwiper
              heading={data.nearby_attractions?.title ?? "Nearby Attractions"}
              items={nearbyItems}
            />
          )}

          {/* Explore More */}
          {exploreItems.length > 0 && (
            <NearbySwiper
              heading={data.explore_more?.title ?? "Explore More Attractions"}
              items={exploreItems}
            />
          )}

        </div>

        <aside className={styles.rightCol} aria-label="Enquiry form">
          <SidebarForm />
        </aside>
      </div>

      {faqs.length > 0 && (
        <FaqSection
          heading={data.faqs?.title ?? "FAQ's"}
          subtext={data.faqs?.sub_title ?? `Find answers to common questions about visiting ${name}.`}
          items={faqs}
          sideImage={{ src: "/images/faq-side-image.webp", alt: name }}
        />
      )}
    </>
  );
}
