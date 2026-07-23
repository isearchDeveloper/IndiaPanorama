import type { Metadata } from "next";
import Image from "next/image";
import Banner from "@/app/components/common/Banner";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import PlanJourneySection from "@/app/components/about-us/PlanJourneySection";
import WhatWeDoSection from "@/app/components/about-us/WhatWeDoSection";
import BestExperienceSection from "@/app/components/about-us/BestExperienceSection";
import AwardsSection from "@/app/components/about-us/AwardsSection";
import ExperienceSoulSection from "@/app/components/about-us/ExperienceSoulSection";
import { fetchAboutPage } from "@/services/aboutService";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import styles from "./AboutPage.module.css";

export async function generateMetadata(): Promise<Metadata> {
  const data = await fetchAboutPage();
  const hero = data?.sections?.find((s: any) => s.type === "hero");
  return {
    title: data?.seo?.meta_title ?? hero?.content?.heading ?? "About Us | Indian Panorama",
    description: data?.seo?.meta_description ?? undefined,
    alternates: { canonical: "https://www.indianpanorama.in/about-us" },
    robots: { index: true, follow: true },
  };
}

export default async function AboutPage() {
  const data = await fetchAboutPage();
  const sections: any[] = data?.sections ?? [];

  const hero       = sections.find((s) => s.type === "hero");
  const imageSecs  = sections.filter((s) => s.type === "image_text");
  const cards      = sections.find((s) => s.type === "cards");
  const experience = sections.find((s) => s.type === "experience");
  const awards     = sections.find((s) => s.type === "awards");
  const cta        = sections.find((s) => s.type === "cta");

  // image_text sections by sort_order
  const story    = imageSecs.find((s) => s.sort_order === 1);
  const planning = imageSecs.find((s) => s.sort_order === 2);
  const whatWeDo = imageSecs.find((s) => s.sort_order === 4);

  return (
    <div className={styles.pageWrapper}>

      <Banner
        title={hero?.content?.heading ?? "About Indian Panorama"}
        subtitle={hero?.content?.subheading}
        bgImage={hero?.content?.banner_image ?? "/images/about-banner-pages.jpg"}
      />

      {/* Our Story */}
      {story && (
        <section className={styles.ourStorySection}>
          <div className={styles.breadcrumbWrap}>
            <Breadcrumb items={[{ label: "Home", href: "/" }, { label: "About Us", href: "/about-us" }]} />
          </div>
          <div className={styles.storyInner}>
            <div className={styles.storyTextCol}>
              <h2 className={styles.storyHeading}>{story.content.heading}</h2>
              <ReadMoreHtml html={story.content.body ?? ""} className={styles.storyPara} />
            </div>
            {story.content.image && (
              <div className={styles.peacockCol}>
                <div className={styles.peacockWrap}>
                  <Image src={story.content.image} alt={story.content.image_alt ?? ""} fill priority sizes="(max-width: 768px) 50vw, 420px" className={styles.peacockImg} />
                </div>
              </div>
            )}
          </div>
        </section>
      )}

      {/* Planning Your Trip */}
      {planning && (
        <section className={styles.planningSection}>
          <div className={styles.planningInner}>
            {planning.content.image && (
              <div className={styles.redFortCol}>
                <div className={styles.redFortWrap}>
                  <Image src={planning.content.image} alt={planning.content.image_alt ?? ""} fill sizes="(max-width: 768px) 100vw, 45vw" className={styles.redFortImg} />
                </div>
              </div>
            )}
            <div className={styles.planningTextCol}>
              <h2 className={styles.planningHeading}>{planning.content.heading}</h2>
              <ReadMoreHtml html={planning.content.body ?? ""} className={styles.planningParaWrap} />
            </div>
          </div>
        </section>
      )}

      <PlanJourneySection data={cards} />
      <WhatWeDoSection data={whatWeDo} />
      <BestExperienceSection data={experience} />
      <AwardsSection data={awards} />
      <ExperienceSoulSection data={cta} />

    </div>
  );
}
