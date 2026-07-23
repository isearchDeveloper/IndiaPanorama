/* eslint-disable @typescript-eslint/no-explicit-any */
import type { Metadata } from "next";
import { notFound } from "next/navigation";
import Banner from "@/app/components/common/Banner";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import { getPageSettings } from "@/services/pageSettings";
import styles from "./page.module.css";

// PURA page API-driven — /page/setting/terms-and-conditions
// sections[] jitne aayenge utne render honge.

export async function generateMetadata(): Promise<Metadata> {
  const api = await getPageSettings("terms-and-conditions");
  const seo = api?.seo;

  const title = seo?.meta_title ?? "Terms and Conditions | Indian Panorama";
  const description =
    seo?.meta_description ??
    "Read the terms and conditions for booking India tour packages with Indian Panorama — payments, cancellations, refunds and travel policies.";

  return {
    title,
    description,
    keywords: seo?.meta_keywords ?? undefined,
    openGraph: { title, description, type: "website", url: "https://www.indianpanorama.in/terms-and-conditions" },
    twitter: { card: "summary", title, description },
    alternates: { canonical: "https://www.indianpanorama.in/terms-and-conditions" },
    robots: { index: true, follow: true },
  };
}

export default async function TermsAndConditionsPage() {
  const api = await getPageSettings("terms-and-conditions");
  if (!api) notFound();

  const sections = (api.sections ?? []).filter(
    (s: any) => s?.content?.heading || s?.content?.body
  );

  const pageTitle = api.title ?? sections[0]?.content?.heading ?? "Terms and Conditions";

  return (
    <div className={styles.page}>
      <Banner title={pageTitle} bgImage="/images/about-banner-pages.jpg" />

      <div className={styles.container}>
        <div className={styles.breadcrumbRow}>
          <Breadcrumb
            items={[
              { label: "Home", href: "/" },
              { label: pageTitle, href: "/terms-and-conditions" },
            ]}
          />
        </div>

        <div className={styles.mainContent}>
          {sections.map((s: any, i: number) => (
            <section key={s.id ?? i} className={styles.section}>
              {s.content?.heading &&
                (i === 0 ? (
                  <h1 className={styles.pageTitle}>{s.content.heading}</h1>
                ) : (
                  <h2 className={styles.sectionTitle}>{s.content.heading}</h2>
                ))}
              {s.content?.body && (
                <div
                  className={`cms-content cms-intro ${styles.sectionBody}`}
                  dangerouslySetInnerHTML={{ __html: s.content.body }}
                />
              )}
            </section>
          ))}
        </div>
      </div>
    </div>
  );
}
