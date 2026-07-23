"use client";

import { Trophy } from "lucide-react";
import PageLayout from "@/app/components/common/PageLayout";
import SidebarForm from "@/app/components/common/SidebarForm";
import ReadMoreHtml from "@/app/components/common/ReadMoreHtml";
import Image from "next/image";
import styles from "./AwardsSection.module.css";

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export default function AwardsSection({ text, awards }: { text: any; awards: any }) {
  const heading = text?.content?.heading ?? "";
  const body = text?.content?.body ?? null;
  const awardsList: any[] = awards?.awards ?? [];

  return (
    <PageLayout
      breadcrumb={[
        { label: "Home", href: "/" },
        { label: "Awards & Achievements", href: "/awards-achievements" },
      ]}
      sidebar={<SidebarForm />}
    >
      {heading && <h1 className={styles.heading}>{heading}</h1>}
      {body && <ReadMoreHtml html={body} className={styles.para} />}

      {awardsList.length > 0 ? (
        <div className={styles.cardList}>
          {awardsList.map((award: any) => (
            <div key={award.id} className={styles.card}>
              <div className={styles.cardImage}>
                {award.banner_image && (
                  <Image
                    src={award.banner_image}
                    alt={award.title ?? ""}
                    fill
                    sizes="280px"
                    className={styles.cardImageEl}
                  />
                )}
              </div>
              <div className={styles.cardBody}>
                <div className={styles.cardTitleRow}>
                  <Trophy size={22} className={styles.cardIcon} />
                  <h3 className={styles.cardTitle}>{award.title}</h3>
                </div>
                {award.description && <p className={styles.cardDesc}>{award.description}</p>}
              </div>
            </div>
          ))}
        </div>
      ) : (
        <p className={styles.emptyMsg}>No awards to display yet.</p>
      )}
    </PageLayout>
  );
}
