import Banner from "@/app/components/common/Banner";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import SidebarForm from "@/app/components/common/SidebarForm";
import FaqSection from "@/app/components/common/FaqSection";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import type { BreadcrumbItem } from "@/app/components/common/Breadcrumb";
import type { FaqItem } from "@/app/components/common/FaqSection";
import styles from "./FestivalLayout.module.css";

interface Props {
  bannerTitle: string;
  bannerSubtitle?: string;
  bannerImage: string;
  breadcrumbs: BreadcrumbItem[];
  h1: string;
  intro?: string;
  faqs?: FaqItem[];
  faqHeading?: string;
  faqSubtext?: string;
  children: React.ReactNode;
}

export default function FestivalLayout({
  bannerTitle,
  bannerSubtitle,
  bannerImage,
  breadcrumbs,
  h1,
  intro,
  faqs,
  faqHeading,
  faqSubtext,
  children,
}: Props) {
  return (
    <>
      <Banner
        title={bannerTitle}
        subtitle={bannerSubtitle}
        bgImage={bannerImage}
      />

      <div className={styles.contentLayout}>
        <div className={styles.leftCol}>
          <Breadcrumb items={breadcrumbs} />
          <h1 className={styles.h1}>{h1}</h1>
          {/* intro API se HTML aata hai (short_description) */}
          {intro && <ReadMoreHtml html={intro} className={`cms-intro ${styles.intro}`} />}
          {children}
        </div>
        <aside className={styles.rightCol}>
          <SidebarForm />
        </aside>
      </div>

      {faqs && faqs.length > 0 && (
        <FaqSection
          heading={faqHeading ?? "Frequently Asked Questions"}
          subtext={faqSubtext}
          items={faqs}
          sideImage={{ src: "/images/faq-side-image.webp", alt: h1 }}
        />
      )}
    </>
  );
}
