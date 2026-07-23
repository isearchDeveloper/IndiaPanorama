"use client";

import { useState, useRef, useEffect } from "react";
import { ChevronDown } from "lucide-react";
import styles from "./ItineraryAccordion.module.css";

interface ItineraryItem {
  day: number;
  title: string;
  html?: string;
  description?: string;
}

function AccordionItem({
  item,
  isOpen,
  onToggle,
}: {
  item: ItineraryItem;
  isOpen: boolean;
  onToggle: () => void;
}) {
  const bodyRef = useRef<HTMLDivElement>(null);
  const [height, setHeight] = useState(0);

  useEffect(() => {
    if (bodyRef.current) {
      setHeight(isOpen ? bodyRef.current.scrollHeight : 0);
    }
  }, [isOpen]);

  const content = item.html ?? item.description ?? "";
  const isHtml = content.trim().startsWith("<");

  return (
    <div className={`${styles.item} ${isOpen ? styles.open : ""}`}>
      <button className={styles.trigger} onClick={onToggle} aria-expanded={isOpen}>
        <span className={styles.dayBadge}>Day {String(item.day).padStart(2, "0")}</span>
        <span className={styles.dayTitle}>{item.title}</span>
        <ChevronDown size={18} className={styles.chevron} aria-hidden="true" />
      </button>
      <div
        className={styles.bodyWrap}
        style={{ height: `${height}px` }}
        aria-hidden={!isOpen}
      >
        <div ref={bodyRef} className={styles.body}>
          {isHtml ? (
            <div
              className={styles.desc}
              dangerouslySetInnerHTML={{ __html: content }}
            />
          ) : (
            <p className={styles.desc}>{content}</p>
          )}
        </div>
      </div>
    </div>
  );
}

export default function ItineraryAccordion({ itinerary }: { itinerary: ItineraryItem[] }) {
  const [openDay, setOpenDay] = useState<number>(1);

  if (!itinerary.length) return null;

  return (
    <section className={styles.section}>
      <h2 className={styles.heading}>Detailed Itinerary</h2>
      <div className={styles.list}>
        {itinerary.map((item) => (
          <AccordionItem
            key={item.day}
            item={item}
            isOpen={openDay === item.day}
            onToggle={() => setOpenDay(openDay === item.day ? 0 : item.day)}
          />
        ))}
      </div>
    </section>
  );
}
