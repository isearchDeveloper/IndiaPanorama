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
  const [maxPx, setMaxPx] = useState<number | null>(null);

  useLayoutEffect(() => {
    const el = contentRef.current;
    if (!el) return;

    const prevMaxHeight = el.style.maxHeight;
    const prevOverflow = el.style.overflow;
    el.style.maxHeight = "none";
    el.style.overflow = "visible";

    const computed = getComputedStyle(el);
    let lineHeight = parseFloat(computed.lineHeight);
    if (Number.isNaN(lineHeight)) {
      lineHeight = parseFloat(computed.fontSize) * 1.6;
    }
    const clampPx = Math.round(lineHeight * lines);
    const naturalHeight = el.scrollHeight;

    el.style.maxHeight = prevMaxHeight;
    el.style.overflow = prevOverflow;

    setMaxPx(clampPx);
    setOverflowing(naturalHeight > clampPx + 1);
  }, [html, lines]);

  if (!html) return null;

  const collapsed = !expanded && maxPx !== null;

  return (
    <div className={`${styles.wrapper} ${className}`} style={style}>
      <div
        ref={contentRef}
        className={styles.content}
        style={collapsed ? { maxHeight: maxPx as number, overflow: "hidden" } : undefined}
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
