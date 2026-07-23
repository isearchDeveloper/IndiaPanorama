"use client";

import { useEffect, useRef, useState } from "react";
import { createPortal } from "react-dom";
import { ChevronDown } from "lucide-react";
import styles from "./GoogleTranslate.module.css";

const LANGUAGES = [
  { code: "en",    label: "English",               flag: "/flag/uk.svg" },
  { code: "zh-CN", label: "Chinese (Simplified)",  flag: "/flag/cn.svg" },
  { code: "nl",    label: "Dutch",                 flag: "/flag/nl.svg" },
  { code: "fr",    label: "French",                flag: "/flag/fr.svg" },
  { code: "de",    label: "German",                flag: "/flag/de.svg" },
  { code: "iw",    label: "Hebrew",                flag: "/flag/il.svg" },
  { code: "it",    label: "Italian",               flag: "/flag/it.svg" },
  { code: "pt",    label: "Portuguese (Brazil)",   flag: "/flag/br.svg" },
  { code: "ru",    label: "Russian",               flag: "/flag/ru.svg" },
  { code: "es",    label: "Spanish",               flag: "/flag/es.svg" },
];

declare global {
  interface Window {
    google?: { translate?: { TranslateElement?: new (opts: object, id: string) => void } };
    googleTranslateElementInit?: () => void;
  }
}

/** Google Translate stores the active language as a `googtrans=/en/<lang>` cookie
 * and re-applies it on every page load, so restoring English requires clearing it.
 * The widget may set it on the PARENT domain (`.indianpanorama.in`) while we run on
 * `www.indianpanorama.in` — a cookie can only be deleted with its exact domain
 * attribute, so we clear every domain level from the full host up to the root. */
function clearGoogTransCookie() {
  const expire = "expires=Thu, 01 Jan 1970 00:00:00 UTC";
  const parts = location.hostname.split(".");

  // host-only cookie (how it's set on localhost)
  document.cookie = `googtrans=; ${expire}; path=/;`;

  // every domain level, with and without leading dot:
  // www.indianpanorama.in, .www.indianpanorama.in, indianpanorama.in, .indianpanorama.in
  for (let i = 0; i < parts.length - 1; i++) {
    const domain = parts.slice(i).join(".");
    document.cookie = `googtrans=; ${expire}; path=/; domain=${domain};`;
    document.cookie = `googtrans=; ${expire}; path=/; domain=.${domain};`;
  }
}

function getGoogTransLangCode(): string | null {
  const match = document.cookie.match(/googtrans=\/[a-zA-Z-]+\/([a-zA-Z-]+)/);
  return match ? match[1] : null;
}

export default function GoogleTranslate() {
  const [open, setOpen] = useState(false);
  const [current, setCurrent] = useState(LANGUAGES[0]);
  const [dropPos, setDropPos] = useState<{ top: number; right: number } | null>(null);
  const [mounted, setMounted] = useState(false);
  const btnRef = useRef<HTMLButtonElement>(null);
  const dropRef = useRef<HTMLUListElement>(null);

  useEffect(() => {
    setMounted(true);
    const activeCode = getGoogTransLangCode();
    if (activeCode) {
      const match = LANGUAGES.find((l) => l.code.toLowerCase() === activeCode.toLowerCase());
      if (match) setCurrent(match);
    }
  }, []);

  /* inject Google Translate script once */
  useEffect(() => {
    if (document.getElementById("gt-script")) return;

    window.googleTranslateElementInit = () => {
      new window.google!.translate!.TranslateElement!(
        { pageLanguage: "en", autoDisplay: false, multilanguagePage: false },
        "google_translate_element"
      );
    };

    const s = document.createElement("script");
    s.id = "gt-script";
    s.src = "//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit";
    s.async = true;
    document.body.appendChild(s);

    const observer = new MutationObserver(() => {
      if (document.body.style.top && document.body.style.top !== "0px") {
        document.body.style.top = "0px";
      }
    });
    observer.observe(document.body, { attributes: true, attributeFilter: ["style"] });

    return () => observer.disconnect();
  }, []);

  /* close on outside click */
  useEffect(() => {
    if (!open) return;
    const handler = (e: MouseEvent) => {
      if (
        btnRef.current?.contains(e.target as Node) ||
        dropRef.current?.contains(e.target as Node)
      ) return;
      setOpen(false);
    };
    document.addEventListener("mousedown", handler);
    return () => document.removeEventListener("mousedown", handler);
  }, [open]);

  const handleOpen = () => {
    if (!open && btnRef.current) {
      const rect = btnRef.current.getBoundingClientRect();
      setDropPos({
        top: rect.bottom + 6,
        right: document.documentElement.clientWidth - rect.right,
      });
    }
    setOpen((o) => !o);
  };

  const selectLang = (lang: typeof LANGUAGES[0]) => {
    setOpen(false);

    if (lang.code === "en") {
      // Clicking inside Google's translate banner doesn't work — it's a cross-origin
      // iframe, so contentDocument access is silently blocked and nothing happens,
      // leaving the googtrans cookie (and the actual translated page) unchanged.
      // Clearing the cookie and reloading is the only reliable way to restore English.
      clearGoogTransCookie();
      window.location.reload();
      return;
    }

    setCurrent(lang);

    const tryTranslate = (attempts = 0) => {
      const select = document.querySelector<HTMLSelectElement>(".goog-te-combo");
      if (select) {
        select.value = lang.code;
        select.dispatchEvent(new Event("change", { bubbles: true }));
      } else if (attempts < 15) {
        setTimeout(() => tryTranslate(attempts + 1), 200);
      }
    };

    tryTranslate();
  };

  return (
    <>
      {/* hidden GT widget — must not be display:none or Google select won't init */}
      <div
        id="google_translate_element"
        style={{ position: "absolute", width: 0, height: 0, overflow: "hidden", opacity: 0, pointerEvents: "none" }}
      />

      <button
        ref={btnRef}
        className={styles.btn}
        onClick={handleOpen}
        aria-haspopup="listbox"
        aria-expanded={open}
        aria-label="Select language"
      >
        <img src={current.flag} alt={current.label} width={20} height={14} className={styles.flagImg} />
        <span>{current.code.split("-")[0].toUpperCase()}</span>
        <ChevronDown size={12} className={open ? styles.chevronOpen : ""} />
      </button>

      {mounted && open && dropPos && createPortal(
        <ul
          ref={dropRef}
          className={styles.dropdown}
          role="listbox"
          style={{ top: dropPos.top, right: dropPos.right }}
        >
          {LANGUAGES.map((lang) => (
            <li
              key={lang.code}
              role="option"
              aria-selected={lang.code === current.code}
              className={`${styles.option} ${lang.code === current.code ? styles.optionActive : ""}`}
              onClick={() => selectLang(lang)}
            >
              <img src={lang.flag} alt={lang.label} width={26} height={18} className={styles.flagImg} />
              <span className={styles.langLabel}>{lang.label}</span>
            </li>
          ))}
        </ul>,
        document.body
      )}
    </>
  );
}
