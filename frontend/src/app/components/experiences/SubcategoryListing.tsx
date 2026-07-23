import Link from "next/link";
import Banner from "@/app/components/common/Banner";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import SidebarForm from "@/app/components/common/SidebarForm";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import ExploreGrid, { type ExploreGridItem } from "@/app/components/experiences/ExploreGrid";
import styles from "./SubcategoryListing.module.css";

// pura API-driven listing — /experiences/{subcategory} + /experiences/{subcategory}-in-{state}
interface Props {
  sub: {
    name: string;
    slug: string;
    categoryName?: string;
    categorySlug?: string;
    bannerImage?: string | null;
    description?: string | null;
  };
  items: ExploreGridItem[];
  /** subcategory ke items jin states me hain — filter chips */
  states: { slug: string; name: string }[];
  /** selected state slug (subcategory-in-state page), warna null = All India */
  activeState?: string | null;
  activeStateName?: string | null;
}

export default function SubcategoryListing({
  sub,
  items,
  states,
  activeState = null,
  activeStateName = null,
}: Props) {
  const h1 = activeStateName ? `${sub.name} in ${activeStateName}` : `${sub.name} in India`;

  const crumbs = [
    { label: "Home", href: "/" },
    { label: "Experiences", href: "/experiences" },
    ...(sub.categoryName && sub.categorySlug
      ? [{ label: sub.categoryName, href: `/experiences/${sub.categorySlug}` }]
      : []),
    { label: sub.name, href: `/experiences/${sub.slug}` },
    ...(activeStateName
      ? [{ label: activeStateName, href: `/experiences/${sub.slug}-in-${activeState}` }]
      : []),
  ];

  return (
    <>
      <Banner
        title={h1}
        subtitle=""
        bgImage={sub.bannerImage ?? "/images/about-banner-pages.jpg"}
      />

      <div className={styles.pageLayout}>
        <div className={styles.mainCol}>
          <Breadcrumb items={crumbs} />

          <h1 className={styles.pageH1}>{h1}</h1>
          {sub.description && (
            <ReadMoreHtml html={sub.description} className={`cms-intro ${styles.intro}`} />
          )}

          {/* ── State filter chips ── */}
          {states.length > 0 && (
            <div className={styles.chipsRow} aria-label="Filter by state">
              <Link
                href={`/experiences/${sub.slug}`}
                className={`${styles.chip} ${!activeState ? styles.chipActive : ""}`}
              >
                All States
              </Link>
              {states.map((st) => (
                <Link
                  key={st.slug}
                  href={`/experiences/${sub.slug}-in-${st.slug}`}
                  className={`${styles.chip} ${activeState === st.slug ? styles.chipActive : ""}`}
                >
                  {st.name}
                </Link>
              ))}
            </div>
          )}

          {/* ── Items grid ── */}
          {items.length > 0 ? (
            <ExploreGrid
              heading={activeStateName ? `Top ${sub.name} in ${activeStateName}` : `Top ${sub.name}`}
              items={items}
            />
          ) : (
            <p className={styles.empty}>
              Experiences coming soon in this section.
              {sub.categoryName && sub.categorySlug && (
                <>
                  {" "}
                  <Link href={`/experiences/${sub.categorySlug}`} className={styles.emptyLink}>
                    Explore more {sub.categoryName} experiences →
                  </Link>
                </>
              )}
            </p>
          )}

          {/* state page se wapas state hub ka rasta */}
          {activeStateName && activeState && (
            <p className={styles.stateHubLink}>
              <Link href={`/${activeState}/experiences`}>
                View all {activeStateName} experiences →
              </Link>
            </p>
          )}
        </div>

        <aside className={styles.sidebarCol} aria-label="Enquiry form">
          <SidebarForm />
        </aside>
      </div>
    </>
  );
}
