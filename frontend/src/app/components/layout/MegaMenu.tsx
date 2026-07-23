"use client";
// ✅ SEO: All links always in DOM
// ✅ JS-off: CSS :hover on regionRow shows rightPanel (both are siblings in same li...
//    but rightPanel is in rightArea now — so JS-off tab switching not possible for right panel.
//    JS-off: all content visible via spacer, tabs switch on hover via JS.
// ✅ JS-on: useState keeps hovered region active even when mouse moves to right panel.

import { useEffect, useState } from "react";
import Link from "next/link";
import type { MegaMenu as MegaMenuType } from "@/services/headerService";
import styles from "./MegaMenu.module.css";

interface MegaMenuProps {
  megaMenu: MegaMenuType;
  /** appended to state/city links, e.g. "/tour-packages" for the Holidays menu; "" for Destination menu */
  linkSuffix?: string;
  onMouseEnter?: () => void;
  onMouseLeave?: () => void;
  onLinkClick?: () => void;
}

function slug(url: string) {
  return url.replace(/^\//, "");
}

// viewport ke hisab se max columns (.megaMenu 1024px se neeche hidden hi rahta hai)
function useMaxColumns() {
  const [maxCols, setMaxCols] = useState(4);
  useEffect(() => {
    const update = () => setMaxCols(window.innerWidth <= 1200 ? 3 : 4);
    update();
    window.addEventListener("resize", update);
    return () => window.removeEventListener("resize", update);
  }, []);
  return maxCols;
}

function RegionContent({ region, linkSuffix, onLinkClick, maxCols }: { region: MegaMenuType["items"][number]; linkSuffix: string; onLinkClick?: () => void; maxCols: number }) {
  // Holidays menu (linkSuffix="/tour-packages") → "All {Region}"
  // Destination menu (linkSuffix="") → "All {Region} Destination"
  const allRegionLink = region.url ? (
    <Link
      href={`/${slug(region.url)}${linkSuffix}`}
      className={styles.allRegionLink}
      onClick={onLinkClick}
    >
      All {region.title}{!linkSuffix && " Destination"}
    </Link>
  ) : null;

  // column count content ke hisab se — kam states hon (jaise North India ke
  // is-tarah ke thode groups) to utne hi columns bane, khali column na bache
  const columnCount = Math.min(maxCols, region.children.length);

  return region.children.length > 0 ? (
    <>
      {allRegionLink}
      <div className={styles.statesGrid} style={{ columnCount }}>
      {region.children.map((state) => {
        const stateSlug = slug(state.url);
        return (
          <div key={state.id} className={styles.stateGroup}>
            <Link
              href={`/${stateSlug}${linkSuffix}`}
              className={styles.stateTitle}
              onClick={onLinkClick}
            >
              {state.title}
            </Link>
            {state.children.length > 0 && (
              <ul className={styles.cityList}>
                {state.children.map((city) => (
                  <li key={city.id}>
                    <Link
                      href={`/${stateSlug}/${slug(city.url)}${linkSuffix}`}
                      className={styles.cityLink}
                      onClick={onLinkClick}
                    >
                      {city.title}
                    </Link>
                  </li>
                ))}
              </ul>
            )}
          </div>
        );
      })}
      </div>
    </>
  ) : (
    <>
      {allRegionLink}
      <p className={styles.emptyMsg}>No destinations available</p>
    </>
  );
}

export default function MegaMenu({ megaMenu, linkSuffix = "", onMouseEnter, onMouseLeave, onLinkClick }: MegaMenuProps) {
  const [activeId, setActiveId] = useState<number>(
    megaMenu.items[0]?.id ?? 0
  );
  const maxCols = useMaxColumns();

  if (!megaMenu.items.length) return null;

  const activeRegion = megaMenu.items.find((r) => r.id === activeId) ?? megaMenu.items[0];

  return (
    <div className={styles.megaMenu} aria-label="Mega navigation menu" onMouseEnter={onMouseEnter} onMouseLeave={onMouseLeave}>
      <div className={styles.inner}>

        {/* LEFT — region tabs */}
        <ul className={styles.leftPanel} role="tablist">
          <li className={styles.panelLabelItem}>
            <span className={styles.panelLabel}>Regions</span>
          </li>
          {megaMenu.items.map((region, idx) => (
            <li
              key={region.id}
              className={`${styles.regionRow} ${activeId === region.id ? styles.regionRowActive : ""} ${idx === 0 ? styles.regionRowFirst : ""}`}
              onMouseEnter={() => setActiveId(region.id)}
              role="tab"
              aria-selected={activeId === region.id}
            >
              <Link
                // Holidays menu → /south-india/tour-packages, Destination menu → /south-india
                href={region.url ? `/${slug(region.url)}${linkSuffix}` : "#"}
                className={styles.regionItem}
                target={region.target === "_blank" ? "_blank" : undefined}
                rel={region.target === "_blank" ? "noopener noreferrer" : undefined}
                onClick={onLinkClick}
              >
                <span>{region.title}</span>
                <span className={styles.arrow}>›</span>
              </Link>
            </li>
          ))}
        </ul>

        {/* RIGHT — active panel + spacer to drive height */}
        <div className={styles.rightArea}>
          {/* Spacer: invisible, drives rightArea height to match active content */}
          <div className={styles.rightPanelSpacer} aria-hidden="true">
            {activeRegion && <RegionContent region={activeRegion} linkSuffix={linkSuffix} onLinkClick={onLinkClick} maxCols={maxCols} />}
          </div>

          {/* All panels stacked — only active visible */}
          {megaMenu.items.map((region) => (
            <div
              key={region.id}
              className={`${styles.rightPanel} ${activeId === region.id ? styles.rightPanelActive : ""}`}
              role="tabpanel"
              aria-hidden={activeId !== region.id}
            >
              <RegionContent region={region} linkSuffix={linkSuffix} onLinkClick={onLinkClick} maxCols={maxCols} />
            </div>
          ))}
        </div>

      </div>
    </div>
  );
}
