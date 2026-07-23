"use client";

import { useState, useEffect } from "react";
import PageLayout from "@/app/components/common/PageLayout";
import SidebarForm from "@/app/components/common/SidebarForm";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import Image from "next/image";
import styles from "./TeamSection.module.css";

// eslint-disable-next-line @typescript-eslint/no-explicit-any
function MemberModal({ member, onClose }: { member: any; onClose: () => void }) {
  useEffect(() => {
    const onKey = (e: KeyboardEvent) => { if (e.key === "Escape") onClose(); };
    window.addEventListener("keydown", onKey);
    const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
    document.body.style.overflow = "hidden";
    document.body.style.paddingRight = `${scrollbarWidth}px`;
    return () => {
      window.removeEventListener("keydown", onKey);
      document.body.style.overflow = "";
      document.body.style.paddingRight = "";
    };
  }, [onClose]);

  return (
    <div className={styles.overlay} onClick={onClose} role="dialog" aria-modal="true">
      <div className={styles.modal} onClick={(e) => e.stopPropagation()}>
        <button type="button" className={styles.modalClose} onClick={onClose} aria-label="Close">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round">
            <path d="M18 6L6 18M6 6l12 12"/>
          </svg>
        </button>

        <div className={styles.modalInner}>
          <div className={styles.modalImg}>
            {member.profile_image
              ? <Image
                  src={member.profile_image}
                  alt={member.name ?? ""}
                  fill
                  sizes="240px"
                  className={styles.modalPhoto}
                />
              : <div className={styles.modalPlaceholder}>{(member.name ?? "?")[0]}</div>
            }
          </div>
          <div className={styles.modalContent}>
            <h2 className={styles.modalName}>{member.name}</h2>
            {member.description && <p className={styles.modalDesig}>{member.description}</p>}
            {member.department?.name && (
              <span className={styles.modalDept}>{member.department.name}</span>
            )}
            {member.about && (
              <p className={styles.modalAbout}>{member.about}</p>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export default function TeamSection({ text, team }: { text: any; team: any }) {
  const heading = text?.content?.heading ?? "";
  const body = text?.content?.body ?? null;

  const rawDepts: any[] = team?.departments ?? [];
  const uniqueDepts = Array.from(
    new Map(
      rawDepts.filter((d: any) => d.id !== null).map((d: any) => [d.id, d.name])
    ).values()
  ) as string[];
  const departments = ["All", ...uniqueDepts];

  const members: any[] = team?.members ?? [];
  const [activeTab, setActiveTab] = useState("All");
  const [selected, setSelected] = useState<any>(null);

  const filtered = activeTab === "All"
    ? members
    : members.filter((m: any) => m.department?.name === activeTab);

  const renderCard = (member: any, featured = false) => (
    <div key={member.id} className={styles.card}>
      <div className={featured ? styles.featuredImageWrap : styles.imageWrap}>
        {member.profile_image
          ? <Image
              src={member.profile_image}
              alt={member.name ?? ""}
              fill
              sizes="(max-width: 640px) 100vw, 33vw"
              className={styles.image}
            />
          : <div className={styles.imagePlaceholder}>{(member.name ?? "?")[0]}</div>
        }
      </div>
      <div className={styles.body}>
        <h3 className={styles.name}>{member.name}</h3>
        {member.description && <p className={styles.designation}>{member.description}</p>}
        {member.about && (
          <>
            <p className={styles.about}>{member.about}</p>
            <button
              type="button"
              className={styles.readMore}
              onClick={() => setSelected(member)}
            >
              Read More
            </button>
          </>
        )}
      </div>
    </div>
  );

  const directors = filtered.filter((m: any) => m.department?.name === "Directors");
  const rest = filtered.filter((m: any) => m.department?.name !== "Directors");

  return (
    <>
      <PageLayout
        breadcrumb={[{ label: "Home", href: "/" }, { label: "Our Team", href: "/our-team" }]}
        sidebar={<SidebarForm />}
      >
        {heading && <h1 className={styles.heading}>{heading}</h1>}
        {body && <ReadMoreHtml html={body} className={styles.para} />}

        {departments.length > 1 && (
          <div className={styles.tabs}>
            {departments.map((cat) => (
              <button
                key={cat}
                type="button"
                className={`${styles.tab} ${activeTab === cat ? styles.tabActive : ""}`}
                onClick={() => setActiveTab(cat)}
              >
                {cat}
              </button>
            ))}
          </div>
        )}

        {filtered.length > 0 ? (
          <>
            {directors.length > 0 && (
              <div className={styles.featuredGrid}>
                {directors.map((m: any) => renderCard(m, true))}
              </div>
            )}
            {rest.length > 0 && (
              <div className={styles.normalGrid}>
                {rest.map((m: any) => renderCard(m, false))}
              </div>
            )}
          </>
        ) : (
          <p className={styles.emptyMsg}>No team members in this category yet.</p>
        )}
      </PageLayout>

      {selected && <MemberModal member={selected} onClose={() => setSelected(null)} />}
    </>
  );
}
