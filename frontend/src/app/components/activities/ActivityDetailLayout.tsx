import Banner from "@/app/components/common/Banner";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import FaqSection from "@/app/components/common/FaqSection";
import SidebarForm from "@/app/components/common/SidebarForm";
import PartnerSlider from "@/app/components/common/PartnerSlider";
import GalleryLightbox from "@/app/components/common/GalleryLightbox";
import NearbyAttractions from "@/app/components/tourist-attractions/NearbyAttractions";
import ThingsToDo from "@/app/components/city-guide/ThingsToDo";
import ItineraryList from "@/app/components/activities/ItineraryList";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import styles from "./ActivityDetailLayout.module.css";
import type { ActivityDetailData } from "@/services/activitiesService";

interface BreadcrumbItem { label: string; href: string; }

interface Props {
  slug: string;
  data: ActivityDetailData;
  breadcrumbs: BreadcrumbItem[];
  /** base path for explore-more attraction links, e.g. `/kerala/munnar` */
  linkBase?: string;
}

export default function ActivityDetailLayout({ slug, data, breadcrumbs, linkBase = "/tourist-attractions" }: Props) {
  const qi = data.quick_information;
  const hasExperiences = (data.experiences?.items?.length ?? 0) > 0;
  const hasPlaces = (data.places?.items?.length ?? 0) > 0;
  const hasThingsToDo = (data.things_to_do?.items?.length ?? 0) > 0;
  const hasItinerary = (data.itinerary?.items?.length ?? 0) > 0;
  const hasGallery = (data.gallery?.length ?? 0) > 0;
  const hasFaqs = (data.faqs?.items?.length ?? 0) > 0;
  const hasExploreMore = (data.explore_more_attractions?.items?.length ?? 0) > 0;

  return (
    <div className={styles.wrapper}>

      <Banner
        title={data.banner.title}
        subtitle={data.banner.tagline ?? undefined}
        bgImage={data.banner.image || "/images/about-banner-pages.jpg"}
      />

      <div className={styles.layout}>
        <div className={styles.leftCol}>

          <Breadcrumb items={breadcrumbs} />

          <div className={styles.introBlock}>
            <h1 className={styles.h1}>{data.meta.h1_heading ?? data.banner.title}</h1>
            {data.short_description && (
              <ReadMoreHtml html={data.short_description} className={`cms-intro ${styles.para}`} />
            )}
          </div>

          {/* Quick info bar */}
          {qi && (qi.location || qi.duration || qi.best_for || qi.best_season) && (
            <div className={styles.infoBar}>
              {qi.location && (
                <div className={styles.infoItem}>
                  <span className={styles.infoLabel}>Location</span>
                  <span className={styles.infoValue}>{qi.location}</span>
                </div>
              )}
              {qi.duration && (
                <div className={styles.infoItem}>
                  <span className={styles.infoLabel}>Duration</span>
                  <span className={styles.infoValue}>{qi.duration}</span>
                </div>
              )}
              {qi.best_for && (
                <div className={styles.infoItem}>
                  <span className={styles.infoLabel}>Best For</span>
                  <span className={styles.infoValue}>{qi.best_for}</span>
                </div>
              )}
              {qi.best_season && (
                <div className={styles.infoItem}>
                  <span className={styles.infoLabel}>Best Season</span>
                  <span className={styles.infoValue}>{qi.best_season}</span>
                </div>
              )}
            </div>
          )}

          {/* About */}
          {data.about?.description && (
            <>
              <section className={styles.section}>
                <h2 className={styles.sectionHeading}>{data.about.title ?? "About This Activity"}</h2>
                <ReadMoreHtml html={data.about.description} className={`cms-intro ${styles.para}`} />
              </section>
            </>
          )}

          {/* Experiences */}
          {hasExperiences && (
            <>
              <section className={styles.section}>
                <h2 className={styles.sectionHeading}>{data.experiences!.title ?? "Experiences"}</h2>
                <div className={styles.expGrid}>
                  {data.experiences!.items.map((exp, i) => (
                    <div key={i} className={styles.expCard}>
                      {exp.image && (
                        <div className={styles.expImgWrap}>
                          {/* eslint-disable-next-line @next/next/no-img-element */}
                          <img src={exp.image} alt={exp.image_alt ?? exp.title} className={styles.expImg} />
                        </div>
                      )}
                      <div className={styles.expBody}>
                        <p className={styles.expTitle}>{exp.title}</p>
                        <div className={`${styles.expDesc} cms-content`} dangerouslySetInnerHTML={{ __html: exp.description }} />
                      </div>
                    </div>
                  ))}
                </div>
              </section>
            </>
          )}

          {/* Places */}
          {hasPlaces && (
            <>
              <section className={styles.section}>
                <h2 className={styles.sectionHeading}>{data.places!.title ?? "Popular Places"}</h2>
                <div className={styles.placesGrid}>
                  {data.places!.items.map((place, i) => (
                    <div key={i} className={styles.placeCard}>
                      {place.image && (
                        <div className={styles.placeImgWrap}>
                          {/* eslint-disable-next-line @next/next/no-img-element */}
                          <img src={place.image} alt={place.image_alt ?? place.title} className={styles.placeImg} />
                        </div>
                      )}
                      <div className={styles.placeBody}>
                        <h3 className={styles.placeTitle}>{place.title}</h3>
                        <div className={`${styles.placeDesc} cms-content`} dangerouslySetInnerHTML={{ __html: place.description }} />
                        {place.activities_text && (
                          <p className={styles.placeActivities}>{place.activities_text}</p>
                        )}
                      </div>
                    </div>
                  ))}
                </div>
              </section>
            </>
          )}

          {/* Things To Do */}
          {hasThingsToDo && (
            <>
              <ThingsToDo
                heading={data.things_to_do!.title ?? "Things To Do"}
                items={data.things_to_do!.items.map((item, i) => ({
                  id: i + 1,
                  title: item.title,
                  description: item.description,
                }))}
              />
            </>
          )}

          {/* Itinerary — numbered list with amber circles */}
          {hasItinerary && (
            <>
              <ItineraryList
                heading={data.itinerary!.title ?? "Itinerary"}
                items={data.itinerary!.items.map((step, i) => ({
                  id: i + 1,
                  title: step.title,
                  description: step.description,
                }))}
              />
            </>
          )}

          {/* Gallery */}
          {hasGallery && (
            <>
              <GalleryLightbox
                heading="Gallery"
                images={data.gallery.map((img, i) => ({
                  image: img.image,
                  image_alt: img.image_alt ?? `Gallery ${i + 1}`,
                }))}
              />
            </>
          )}

          {/* Explore More */}
          {hasExploreMore && (
            <>
              <NearbyAttractions
                heading={data.explore_more_attractions!.title ?? "Explore More"}
                items={data.explore_more_attractions!.items.map((item, i) => ({
                  id: i + 1,
                  name: item.name,
                  image: item.image ?? undefined,
                  description: (item.description ?? "").replace(/<[^>]*>/g, "").slice(0, 100),
                  href: `${linkBase}/${item.slug}`,
                }))}
              />
            </>
          )}

          {/* FAQs */}
          {hasFaqs && (
            <>
              <FaqSection
                heading={data.faqs!.title ?? "Frequently Asked Questions"}
                subtext={data.faqs!.sub_title ?? ""}
                items={data.faqs!.items.map((f, i) => ({ id: i + 1, question: f.question, answer: f.answer }))}
              />
            </>
          )}

        </div>

        <aside className={styles.rightCol}>
          <SidebarForm />
        </aside>
      </div>

    </div>
  );
}
