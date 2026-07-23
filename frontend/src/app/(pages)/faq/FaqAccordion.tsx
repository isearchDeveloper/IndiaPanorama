"use client";

import { useState } from "react";
import { Plus, Minus } from "lucide-react";
import type { FaqCategory } from "./FaqData";
import styles from "./FaqAccordion.module.css";

type Props = {
  categories: FaqCategory[];
};

export default function FaqAccordion({ categories }: Props) {
  const [activeCategory, setActiveCategory] = useState<string>(categories[0]?.id ?? "");
  const [openId, setOpenId] = useState<number | null>(categories[0]?.items[0]?.id ?? null);

  const handleCategoryChange = (id: string) => {
    const cat = categories.find((c) => c.id === id);
    setActiveCategory(id);
    setOpenId(cat?.items[0]?.id ?? null);
  };

  const toggle = (id: number) => setOpenId((prev) => (prev === id ? null : id));

  const currentCategory = categories.find((c) => c.id === activeCategory);

  return (
    <div className={styles.wrapper}>
      {/* ── Category tabs (horizontal scroll on mobile) ── */}
      <div className={styles.categoryRow} role="tablist" aria-label="FAQ Categories">
        {categories.map((cat) => (
          <button
            key={cat.id}
            role="tab"
            aria-selected={activeCategory === cat.id}
            aria-controls={`faq-panel-${cat.id}`}
            id={`faq-tab-${cat.id}`}
            className={`${styles.tab} ${activeCategory === cat.id ? styles.tabActive : ""}`}
            onClick={() => handleCategoryChange(cat.id)}
          >
            <span className={styles.tabIcon} aria-hidden="true">{cat.icon}</span>
            <span className={styles.tabLabel}>{cat.title}</span>
          </button>
        ))}
      </div>

      {/* ── FAQ Panel ── */}
      {currentCategory && (
        <div
          id={`faq-panel-${currentCategory.id}`}
          role="tabpanel"
          aria-labelledby={`faq-tab-${currentCategory.id}`}
          className={styles.panel}
        >
          <div className={styles.panelHeader}>
            <span className={styles.panelIcon} aria-hidden="true">{currentCategory.icon}</span>
            <h2 className={styles.panelTitle}>{currentCategory.title}</h2>
            <span className={styles.panelCount}>{currentCategory.items.length} questions</span>
          </div>

          <div className={styles.accordionList} role="list">
            {currentCategory.items.map((item) => {
              const isOpen = openId === item.id;
              return (
                <div
                  key={item.id}
                  className={`${styles.accordionItem} ${isOpen ? styles.accordionOpen : ""}`}
                  role="listitem"
                >
                  <button
                    className={styles.accordionBtn}
                    onClick={() => toggle(item.id)}
                    aria-expanded={isOpen}
                    aria-controls={`faq-answer-${item.id}`}
                    id={`faq-question-${item.id}`}
                  >
                    <span className={styles.accordionQ}>{item.question}</span>
                    <span className={styles.accordionIcon} aria-hidden="true">
                      {isOpen
                        ? <Minus size={16} />
                        : <Plus size={16} />
                      }
                    </span>
                  </button>
                  <div
                    id={`faq-answer-${item.id}`}
                    className={styles.accordionAnswer}
                    role="region"
                    aria-labelledby={`faq-question-${item.id}`}
                    aria-hidden={!isOpen}
                  >
                    <p className={styles.accordionAnswerText}>{item.answer}</p>
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      )}
    </div>
  );
}
