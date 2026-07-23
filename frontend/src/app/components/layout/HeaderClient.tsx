"use client";

import { useState, useEffect, useRef, useCallback } from "react";
import Link from "next/link";
import Image from "next/image";
import { Phone, Mail, ChevronDown, Menu, X } from "lucide-react";
import type { NavItem } from "@/services/headerService";
import MegaMenu from "./MegaMenu";
import GoogleTranslate from "./GoogleTranslate";
import styles from "./Header.module.css";

interface HeaderClientProps {
  navItems: NavItem[];
}

export default function HeaderClient({ navItems }: HeaderClientProps) {

  /* ── State ── */
  const [mobileOpen, setMobileOpen]         = useState(false);
  const [mobileExpandId, setMobileExpandId] = useState<number | null>(null);
  const [mobileRegionId, setMobileRegionId] = useState<number | null>(null);
  const [desktopOpenId, setDesktopOpenId]   = useState<number | null>(null);

  /* ── Close timer ref (shared between navItem and megaMenu) ── */
  const closeTimer = useRef<ReturnType<typeof setTimeout> | null>(null);

  const scheduleClose = useCallback(() => {
    closeTimer.current = setTimeout(() => setDesktopOpenId(null), 150);
  }, []);

  const cancelClose = useCallback(() => {
    if (closeTimer.current) {
      clearTimeout(closeTimer.current);
      closeTimer.current = null;
    }
  }, []);

  const navRef = useRef<HTMLElement>(null);

  /* ── Body scroll lock on mobile only ── */
  useEffect(() => {
    if (mobileOpen) {
      const sb = window.innerWidth - document.documentElement.clientWidth;
      document.body.style.overflow = "hidden";
      document.body.style.paddingRight = `${sb}px`;
    } else {
      document.body.style.overflow = "";
      document.body.style.paddingRight = "";
    }
    return () => {
      document.body.style.overflow = "";
      document.body.style.paddingRight = "";
    };
  }, [mobileOpen]);


  const closeMobile = () => {
    setMobileOpen(false);
    setMobileExpandId(null);
    setMobileRegionId(null);
  };


  return (
    <>
      <header className={styles.header}>

        {/* ══ TOP BAR ══ */}
        <div className={styles.topBar}>
          <div className={styles.topBarInner}>

            <div className={styles.leftInfo}>
              <Image
                src="/images/trip-advisior-logo.png"
                alt="TripAdvisor Travellers' Choice 2025"
                width={76}
                height={76}
                className={styles.tripAdvisor}
                style={{ width: "auto", height: "auto" }}
              />
            </div>

            <div className={styles.logoWrap}>
              <Link href="/" className={styles.logoLink} onClick={closeMobile}>
                <span className={styles.logoImg} role="img" aria-label="Indian Panorama" />
              </Link>
            </div>

            <div className={styles.rightActions}>
              <a href="tel:+914314226122" className={styles.phoneLink}>
                <Phone size={14} /><span>+91 431 422 6122</span>
              </a>
              <span className={styles.desktopOnly}><GoogleTranslate /></span>
              <Link href="/contact-us" className={styles.contactBtn}>Contact Us</Link>
              <button
                className={styles.hamburger}
                onClick={() => setMobileOpen(true)}
                aria-label="Open menu"
                aria-expanded={mobileOpen}
              >
                <Menu size={20} />
              </button>
            </div>

          </div>
        </div>

        {/* ══ DESKTOP NAVBAR ══ */}
        <nav ref={navRef} className={styles.navbar} aria-label="Main navigation">
          <div className={styles.navInner}>
            <ul className={styles.navList}>
              {navItems.map((item) => {
                const hasMega = item.content_type === "mega_menu" && !!item.mega_menu && item.mega_menu.items.length > 0;
                const hasNormalDropdown = item.title.toLowerCase() === "more" && !!item.children && item.children.length > 0;
                const isOpen  = desktopOpenId === item.id;

                return (
                  <li
                    key={item.id}
                    className={`${styles.navItem} ${hasNormalDropdown ? styles.hasDropdown : ""}`}
                    data-nav-id={hasMega ? item.id : undefined}
                    onMouseEnter={() => {
                      cancelClose();
                      if (hasMega || hasNormalDropdown) setDesktopOpenId(item.id);
                      else setDesktopOpenId(null);
                    }}
                    onMouseLeave={scheduleClose}
                  >
                    <Link
                      href={item.url}
                      className={`${styles.navLink} ${isOpen ? styles.navLinkActive : ""}`}
                      target={item.target === "_blank" ? "_blank" : undefined}
                      rel={item.target === "_blank" ? "noopener noreferrer" : undefined}
                    >
                      {item.title}
                      {(hasMega || hasNormalDropdown) && (
                        <ChevronDown
                          size={13}
                          className={`${styles.chevron} ${isOpen ? styles.chevronOpen : ""}`}
                        />
                      )}
                    </Link>

                    {hasNormalDropdown && (
                      <ul className={`${styles.dropdownMenu} ${isOpen ? styles.dropdownMenuOpen : ""}`}>
                        {item.children.map((child) => (
                          <li key={child.id} className={styles.dropdownItem}>
                            <Link
                              href={child.url}
                              className={styles.dropdownLink}
                              target={child.target === "_blank" ? "_blank" : undefined}
                              rel={child.target === "_blank" ? "noopener noreferrer" : undefined}
                              onClick={() => setDesktopOpenId(null)}
                            >
                              {child.title}
                            </Link>
                          </li>
                        ))}
                      </ul>
                    )}
                  </li>
                );
              })}
            </ul>
          </div>

          {/* Mega menus — siblings of navInner, children of navbar (position:relative)
              position:absolute top:100% so they sit exactly below the navbar.
              pointer-events:none when hidden — never captures mouse from page content.
              JS controls open/close. CSS :hover on navbar not possible here,
              so JS is required for open — JS-off users see all links in drawer. */}
          {navItems
            .filter((item) => item.content_type === "mega_menu" && !!item.mega_menu && item.mega_menu.items.length > 0)
            .map((item) => {
              const isOpen = desktopOpenId === item.id;
              return (
                <div
                  key={item.id}
                  data-wrap-id={item.id}
                  className={`${styles.megaMenuWrap} ${isOpen ? styles.megaMenuWrapOpen : ""}`}
                >
                  <MegaMenu
                    megaMenu={item.mega_menu!}
                    linkSuffix={item.url === "/tour-packages" ? "/tour-packages" : ""}
                    onMouseEnter={cancelClose}
                    onMouseLeave={scheduleClose}
                    onLinkClick={() => setDesktopOpenId(null)}
                  />
                </div>
              );
            })}
        </nav>


      </header>

      {/* ══ MOBILE DRAWER ══ */}
      {mobileOpen && (
        <div
          className={styles.overlay}
          onClick={closeMobile}
          role="dialog"
          aria-modal="true"
          aria-label="Navigation menu"
        >
          <div className={styles.drawer} onClick={(e) => e.stopPropagation()}>

            <div className={styles.drawerTop}>
              <Link href="/" className={styles.logoLink} onClick={closeMobile}>
                <span className={styles.logoImgSm} role="img" aria-label="Indian Panorama" />
              </Link>
              <button className={styles.closeBtn} onClick={closeMobile} aria-label="Close menu">
                <X size={20} />
              </button>
            </div>

            <div className={styles.drawerTranslate}>
              <GoogleTranslate />
            </div>

            <ul className={styles.drawerNav}>
              {navItems.map((item) => {
                const hasMega  = item.content_type === "mega_menu" && !!item.mega_menu && item.mega_menu.items.length > 0;
                const hasNormalDropdown = item.title.toLowerCase() === "more" && !!item.children && item.children.length > 0;
                const expanded = mobileExpandId === item.id;
                return (
                  <li key={item.id}>
                    {!hasMega && !hasNormalDropdown ? (
                      <Link
                        href={item.url}
                        className={styles.drawerNavLink}
                        onClick={closeMobile}
                        target={item.target === "_blank" ? "_blank" : undefined}
                        rel={item.target === "_blank" ? "noopener noreferrer" : undefined}
                      >
                        <span>{item.title}</span>
                      </Link>
                    ) : (
                      <>
                        <button
                          className={styles.drawerNavLink}
                          onClick={() => {
                            const next = expanded ? null : item.id;
                            setMobileExpandId(next);
                            if (next && hasMega) setMobileRegionId(item.mega_menu!.items[0]?.id ?? null);
                          }}
                        >
                          <span>{item.title}</span>
                          <ChevronDown size={14} className={expanded ? styles.chevronOpen : ""} />
                        </button>

                        {expanded && hasMega && (
                          <div className={styles.mobileAccordion}>
                            <div className={styles.mobileRegionTabs}>
                              {item.mega_menu!.items.map((region) => (
                                <button
                                  key={region.id}
                                  className={`${styles.mobileRegionTab} ${mobileRegionId === region.id ? styles.mobileRegionTabActive : ""}`}
                                  onClick={() => setMobileRegionId(region.id)}
                                >
                                  {region.title}
                                </button>
                              ))}
                            </div>
                            {item.mega_menu!.items
                              .filter((r) => r.id === mobileRegionId)
                              .map((region) => (
                                <div key={region.id} className={styles.mobileStates}>
                                  {region.children.map((state) => {
                                    const stateSlug = state.url.replace(/^\//, "");
                                    const linkSuffix = item.url === "/tour-packages" ? "/tour-packages" : "";
                                    return (
                                      <div key={state.id} className={styles.mobileStateGroup}>
                                        <Link href={`/${stateSlug}${linkSuffix}`} className={styles.mobileStateTitle} onClick={closeMobile}>
                                          {state.title}
                                        </Link>
                                        <div className={styles.mobileCities}>
                                          {state.children.map((city) => (
                                            <Link key={city.id} href={`/${stateSlug}/${city.url.replace(/^\//, "")}${linkSuffix}`} className={styles.mobileCityLink} onClick={closeMobile}>
                                              {city.title}
                                            </Link>
                                          ))}
                                        </div>
                                      </div>
                                    );
                                  })}
                                </div>
                              ))}
                          </div>
                        )}

                        {expanded && hasNormalDropdown && (
                          <div className={styles.mobileNormalDropdown}>
                            {item.children.map((child) => (
                              <Link
                                key={child.id}
                                href={child.url}
                                className={styles.mobileDropdownLink}
                                onClick={closeMobile}
                                target={child.target === "_blank" ? "_blank" : undefined}
                                rel={child.target === "_blank" ? "noopener noreferrer" : undefined}
                              >
                                {child.title}
                              </Link>
                            ))}
                          </div>
                        )}
                      </>
                    )}
                  </li>
                );
              })}
            </ul>

            <div className={styles.drawerContact}>
              <a href="tel:+914314226122" className={styles.drawerContactItem}>
                <Phone size={14} /><span>+91 431 422 6122</span>
              </a>
              <a href="mailto:enquiries@indianpanorama.in" className={styles.drawerContactItem}>
                <Mail size={14} /><span>enquiries@indianpanorama.in</span>
              </a>
            </div>

            <div className={styles.drawerCta}>
              <Link href="/contact-us" className={styles.drawerCtaBtn} onClick={closeMobile}>
                Contact Us
              </Link>
            </div>

          </div>
        </div>
      )}
    </>
  );
}