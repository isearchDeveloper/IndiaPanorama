/* eslint-disable @typescript-eslint/no-explicit-any */
import type { Metadata } from "next";
import Banner from "@/app/components/common/Banner";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import ExploreIndiaForm from "@/app/components/home/ExploreIndiaForm";
import HeadOffice from "@/app/components/contact-us/HeadOffice";
import ConnectWithUs from "@/app/components/contact-us/ConnectWithUs";
import { getPageSettings } from "@/services/pageSettings";
import styles from "./page.module.css";

// pura page API-driven — /page/setting/contact-us (baaki CMS pages jaisa)
const FALLBACK_BANNER = "/images/contact-banner.jpg";

export async function generateMetadata(): Promise<Metadata> {
  const data = await getPageSettings("contact-us");
  const seo = data?.seo;

  return {
    title: seo?.meta_title ?? "Contact Us - Get in Touch with Indian Panorama",
    description:
      seo?.meta_description ??
      "Contact Indian Panorama for customised India tour packages. Reach our offices across India — Delhi, Chennai, Bangalore, Kerala and more.",
    keywords: seo?.meta_keywords ?? undefined,
    alternates: { canonical: "https://www.indianpanorama.in/contact-us" },
    robots: { index: true, follow: true },
    openGraph: {
      title: seo?.meta_title ?? "Contact Indian Panorama - India Tour Operator",
      description:
        seo?.meta_description ??
        "Get in touch with our expert travel planners across our offices in India.",
      type: "website",
    },
  };
}

export default async function ContactPage() {
  const data = await getPageSettings("contact-us");
  const banner = data?.banner;
  const branches = data?.branches;

  return (
    <>
      <Banner
        title={banner?.heading ?? "Contact Us"}
        subtitle={banner?.subheading ?? ""}
        bgImage={banner?.banner_image ?? FALLBACK_BANNER}
      />
      <div className={styles.crumbWrap}>
        <Breadcrumb items={[{ label: "Home", href: "/" }, { label: "Contact Us", href: "/contact-us" }]} />
      </div>
      <ExploreIndiaForm />
      <ConnectWithUs
        heading={branches?.heading}
        body={branches?.body}
        items={branches?.items ?? []}
      />
      <HeadOffice />
    </>
  );
}
