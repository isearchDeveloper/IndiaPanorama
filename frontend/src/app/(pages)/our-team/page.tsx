import type { Metadata } from "next";
import Banner from "@/app/components/common/Banner";
import TeamSection from "@/app/components/our-team/TeamSection";
import { fetchTeamPage } from "@/services/teamService";

export async function generateMetadata(): Promise<Metadata> {
  const data = await fetchTeamPage();
  const hero = data?.sections?.find((s: any) => s.type === "hero");
  return {
    title: hero?.content?.heading ?? "Our Team | Indian Panorama",
    description: "Meet the dedicated travel experts behind Indian Panorama.",
    alternates: { canonical: "https://www.indianpanorama.in/our-team" },
    robots: { index: true, follow: true },
  };
}

export default async function OurTeamPage() {
  const data = await fetchTeamPage();
  const sections = data?.sections ?? [];

  const hero = sections.find((s: any) => s.type === "hero");
  const text = sections.find((s: any) => s.type === "text");
  const team = sections.find((s: any) => s.type === "team");

  return (
    <>
      <Banner
        title={hero?.content?.heading ?? "Team of Indian Panorama"}
        subtitle={hero?.content?.subheading ?? "Honoring Excellence, Inspiring Journeys"}
        bgImage={hero?.content?.banner_image ?? "/images/about-banner-pages.jpg"}
        textPosition="bottom"
      />
      <TeamSection text={text} team={team} />
    </>
  );
}
