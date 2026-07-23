"use client";

import { CSSProperties, useLayoutEffect, useRef, useState } from "react";
import { ChevronDown, ChevronUp } from "lucide-react";
import styles from "./ReadMoreHtml.module.css";

interface ReadMoreHtmlProps {
  html: string;
  className?: string;
  style?: CSSProperties;
  /** Number of lines to show when collapsed — clips at a clean line boundary, never mid-sentence. */
  lines?: number;
}

export default function ReadMoreHtml({
  html,
  className = "",
  style,
  lines = 4,
}: ReadMoreHtmlProps) {
  const contentRef = useRef<HTMLDivElement>(null);
  const [expanded, setExpanded] = useState(false);
  const [overflowing, setOverflowing] = useState(false);

  // expanded ko ref me bhi rakha hai — expanded true hone par content unclamped
  // ho jaata hai (scrollHeight === clientHeight), toh us waqt measure() ko skip
  // karna hai, warna woh galti se "overflow nahi hai" samajh ke Show Less button
  // hi hata deta tha.
  const expandedRef = useRef(expanded);
  expandedRef.current = expanded;

  // -webkit-line-clamp se clamp karte hain (JS se lineHeight px calculate karke
  // maxHeight set karna fragile tha — font load/metrics mismatch pe line ke
  // beech mein hi cut ho jaata tha). Line-clamp hamesha clean line boundary pe cut karta hai.
  useLayoutEffect(() => {
    const el = contentRef.current;
    if (!el) return;

    const measure = () => {
      if (expandedRef.current) return;
      setOverflowing(el.scrollHeight > el.clientHeight + 1);
    };
    measure();

    // Webfont load ke baad reflow hone pe dobara measure karo
    const ro = new ResizeObserver(measure);
    ro.observe(el);
    document.fonts?.ready?.then(measure).catch(() => {});

    return () => ro.disconnect();
  }, [html, lines]);

  if (!html) return null;

  const collapsed = !expanded;

  return (
    <div className={`${styles.wrapper} ${className}`} style={style}>
      <div
        ref={contentRef}
        className={styles.content}
        style={
          collapsed
            ? {
                display: "-webkit-box",
                WebkitLineClamp: lines,
                WebkitBoxOrient: "vertical",
                overflow: "hidden",
              }
            : undefined
        }
      >
        {/* cms-content: CMS tags (p/ul/strong/img...) ka site-wide unified styling */}
        <div className="cms-content" dangerouslySetInnerHTML={{ __html: html }} />
      </div>

      {overflowing && (
        <button
          type="button"
          className={styles.toggle}
          onClick={() => setExpanded((prev) => !prev)}
          aria-expanded={expanded}
        >
          {expanded ? "Show Less" : "Read More"}
          {expanded ? (
            <ChevronUp size={16} aria-hidden="true" />
          ) : (
            <ChevronDown size={16} aria-hidden="true" />
          )}
        </button>
      )}
    </div>
  );
}
