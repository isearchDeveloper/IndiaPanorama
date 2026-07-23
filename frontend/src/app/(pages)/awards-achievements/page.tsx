import type { Metadata } from "next";
import Banner from "@/app/components/common/Banner";
import AwardsSection from "@/app/components/awards-achievements/AwardsSection";
import { fetchAwardsPage } from "@/services/awardsService";

export async function generateMetadata(): Promise<Metadata> {
  const data = await fetchAwardsPage();
  const hero = data?.sections?.find((s: any) => s.type === "hero");
  return {
    title: hero?.content?.heading ?? "Awards & Achievements | Indian Panorama",
    description: "Explore the awards and recognitions earned by Indian Panorama.",
    alternates: { canonical: "https://www.indianpanorama.in/awards-achievements" },
    robots: { index: true, follow: true },
  };
}

export default async function AwardsAchievementsPage() {
  const data = await fetchAwardsPage();
  const sections = data?.sections ?? [];

  const hero = sections.find((s: any) => s.type === "hero");
  const text = sections.find((s: any) => s.type === "text");
  const awards = sections.find((s: any) => s.type === "awards");

  return (
    <>
      <Banner
        title={hero?.content?.heading ?? "Awards & Achievements"}
        subtitle={hero?.content?.subheading ?? ""}
        bgImage={hero?.content?.banner_image ?? "/images/about-banner-pages.jpg"}
      />
      <AwardsSection text={text} awards={awards} />
    </>
  );
}
