import type { Metadata } from "next";
import Image from "next/image";
import { getHomeData, extractGoogleVerification } from "@/services/homeService";
import HeroBanner from "@/app/components/home/HeroBanner";
import IndiaTourPackages from "@/app/components/home/IndiaTourPackages";
import CustomizedIndiaTours from "@/app/components/home/CustomizedIndiaTours";
import AboutUs from "@/app/components/home/AboutUs";
import WhyIndianPanorama from "@/app/components/home/WhyIndianPanorama";
import ExploreIndiaForm from "@/app/components/home/ExploreIndiaForm";
import LatestBlogs from "@/app/components/home/LatestBlogs";
import ImagesSection from "@/app/components/home/ImagesSection";
import PopularPackages from "@/app/components/common/PopularPackages";

// ── Page-level SEO — layout.tsx ke default metadata ko override karta hai ──
export async function generateMetadata(): Promise<Metadata> {
  const data = await getHomeData();
  const seo = data?.seo_meta;

  if (!seo) return {};

  // Google verification — extra_meta_head se safely extract karo
  const googleVerification = extractGoogleVerification(seo.extra_meta_head ?? "");

  return {
    title: seo.meta_title,
    description: seo.meta_description,
    keywords: seo.meta_keywords,
    ...(googleVerification && {
      verification: { google: googleVerification },
    }),
    openGraph: {
      title: seo.meta_title,
      description: seo.meta_description,
      type: "website",
    },
  };
}

export default async function Home() {
  const data = await getHomeData();

  return (
    <>
      {/* Banner + hanging logos wrapper */}
      <div className="relative">
        <HeroBanner slides={data?.hero_banner?.slides ?? []} />

        {/* Left decorative image — desktop only */}
        <div
          className="hidden lg:block absolute bottom-0 left-6 z-20"
          style={{ transform: "translateY(100%)" }}
        >
          <Image
            src="/images/left-image.png"
            alt="Decoration Left"
            width={200}
            height={300}
            className="object-contain drop-shadow-xl"
            style={{ width: "auto", height: "auto" }}
          />
        </div>

        {/* Right decorative image — desktop only */}
        <div
          className="hidden lg:block absolute bottom-0 right-6 z-20"
          style={{ transform: "translateY(100%)" }}
        >
          <Image
            src="/images/right-image.png"
            alt="Decoration Right"
            width={170}
            height={260}
            className="object-contain drop-shadow-xl"
            style={{ width: "auto", height: "auto" }}
          />
        </div>
      </div>

      <IndiaTourPackages data={data?.india_tour_packages ?? null} />
      <CustomizedIndiaTours data={data?.customized_tours ?? null} />
      <AboutUs data={data?.trusted_operator ?? null} />
      <WhyIndianPanorama data={data?.why_indian_panorama ?? null} />
      <ExploreIndiaForm />
      <LatestBlogs data={data?.latest_blogs ?? null} />
      <ImagesSection data={data?.promo_banner ?? null} />
      {/* master site-wide Popular Packages — home is outside the (pages)
          route group, so its layout doesn't add this automatically */}
      <PopularPackages />
    </>
  );
}
