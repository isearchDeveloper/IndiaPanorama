"use client";

import { useState } from "react";
import Image from "next/image";
import { Plus, Minus } from "lucide-react";
import styles from "./FaqSection.module.css";

export interface FaqItem {
  id: number;
  question: string;
  answer: string;
}

interface FaqSectionProps {
  heading?: string;
  subtext?: string;
  items: FaqItem[];
  sideImage?: { src: string; alt: string };
}

export default function FaqSection({
  heading = "FAQ's",
  subtext,
  items,
  sideImage,
}: FaqSectionProps) {
  const [openId, setOpenId] = useState<number | null>(items[0]?.id ?? null);

  const toggle = (id: number) => setOpenId((prev) => (prev === id ? null : id));

  return (
    <section className={styles.section} aria-label="Frequently Asked Questions">
      <div className={`${styles.inner} ${!sideImage ? styles.innerFull : ""}`}>

        {/* ── FAQ column ── */}
        <div className={styles.faqCol}>
          <h2 className={styles.heading}>{heading}</h2>
          {/* subtext CMS se HTML ke saath aa sakta hai */}
          {subtext && (
            <div
              className={`${styles.subtext} cms-content`}
              dangerouslySetInnerHTML={{ __html: subtext }}
            />
          )}

        <noscript>
          <div className={styles.list}>
            {items.map((item) => (
              <div key={item.id} className={`${styles.item} ${styles.itemOpen}`}>
                <div className={styles.question} style={{ cursor: 'default' }}>
                  <span>{item.question}</span>
                </div>
                <div className={styles.answer} style={{ display: 'block' }}>
                  <div className={`${styles.answerText} cms-content`} dangerouslySetInnerHTML={{ __html: item.answer }} />
                </div>
              </div>
            ))}
          </div>
        </noscript>

        <div className={styles.list} role="list">
          {items.map((item) => {
            const isOpen = openId === item.id;
            return (
              <div
                key={item.id}
                className={`${styles.item} ${isOpen ? styles.itemOpen : ""}`}
                role="listitem"
              >
                <button
                  className={styles.question}
                  onClick={() => toggle(item.id)}
                  aria-expanded={isOpen}
                  aria-controls={`faq-answer-${item.id}`}
                >
                  <span>{item.question}</span>
                  {isOpen
                    ? <Minus size={16} className={styles.icon} aria-hidden="true" />
                    : <Plus size={16} className={styles.icon} aria-hidden="true" />
                  }
                </button>
                <div
                  id={`faq-answer-${item.id}`}
                  className={styles.answer}
                  role="region"
                  aria-hidden={!isOpen}
                >
                  <div className={`${styles.answerText} cms-content`} dangerouslySetInnerHTML={{ __html: item.answer }} />
                </div>
              </div>
            );
          })}
        </div>
        </div>

        {/* ── Optional side image ── */}
        {sideImage && (
          <div className={styles.imageCol} aria-hidden="true">
            <div className={styles.imgWrap}>
              <Image
                src={sideImage.src}
                alt={sideImage.alt}
                fill
                sizes="(max-width: 900px) 0px, 380px"
                className={styles.img}
              />
            </div>
          </div>
        )}

      </div>
    </section>
  );
}
