"use client";

import Image from "next/image";
import Link from "next/link";
import {
  RiLinkedinFill,
  RiInstagramLine,
  RiFacebookFill,
  RiYoutubeFill,
  RiSendPlaneLine
} from "react-icons/ri";
import styles from "./Footer.module.css";

export default function Footer() {
  return (
    <footer className={styles.footer}>
      {/* ── Top Main Footer ── */}
      <div className={styles.mainFooter}>

        {/* Brand Column */}
        <div className={styles.brandCol}>
          <div className={styles.logoWrapper}>
            <Image
              src="/images/indian-panorama-logo.png"
              alt="Indian Panorama Logo"
              width={200}
              height={60}
              className={styles.logo}
              style={{ height: "auto" }}
            />
          </div>
          <p className={styles.description}>
            Indian Panorama is a leading India Tour Operator offering the best India tour
            packages, thoughtfully designed by expert travel agents to match your interests and
            preferences.
          </p>
          <div className={styles.socialList}>
            <a href="#" className={styles.socialIcon} aria-label="LinkedIn">
              <RiLinkedinFill />
            </a>
            <a href="#" className={styles.socialIcon} aria-label="Instagram">
              <RiInstagramLine />
            </a>
            <a href="#" className={styles.socialIcon} aria-label="Facebook">
              <RiFacebookFill />
            </a>
            <a href="#" className={styles.socialIcon} aria-label="YouTube">
              <RiYoutubeFill />
            </a>
          </div>
        </div>

        {/* Quick Link Column */}
        <div className={styles.linkCol}>
          <h3 className={styles.columnTitle}>Quick link</h3>
          <ul className={styles.columnLinks}>
            <li><a href="#">Popular Temples In India</a></li>
            <li><a href="/festivals">Festivals In India</a></li>
            <li><a href="#">Travel Experience</a></li>
            <li><a href="/tourist-attractions">Tourist Attraction</a></li>
            <li><a href="/car-rental">Car Rental In India</a></li>
          </ul>
        </div>

        {/* Support Column */}
        <div className={styles.linkCol}>
          <h3 className={styles.columnTitle}>Support</h3>
          <ul className={styles.columnLinks}>
            <li><a href="#">Discover India</a></li>
            <li><Link href="/contact-us">Contact Us</Link></li>
            <li><Link href="/awards-achievements">Awards &amp; Achievements</Link></li>
            <li><Link href="/our-team">Our Team</Link></li>
            <li><Link href="/privacy-policy">Privacy Policy</Link></li>
            <li><Link href="/terms-and-conditions">Terms And Conditions</Link></li>
            <li><Link href="/faq">FAQ</Link></li>
          </ul>
        </div>

        {/* Tours By Category Column */}
        <div className={styles.linkCol}>
          <h3 className={styles.columnTitle}>Tours by Category</h3>
          <ul className={styles.columnLinks}>
            <li><a href="#">Family Tour</a></li>
            <li><a href="#">Trip To Kerala</a></li>
            <li><a href="#">Tamilnadu Tour</a></li>
            <li><a href="#">Hill Stations Tour</a></li>
            <li><a href="#">Trip To India</a></li>
            <li><a href="#">Rajasthan Tour</a></li>
          </ul>
        </div>

        {/* Contact & Newsletter Column */}
        <div className={styles.contactCol}>
          <h3 className={styles.columnTitle}>Contact</h3>
          <div className={styles.contactInfo}>
            <address>
              Cholan Tours Private Limited<br />
              No.4, Annai Avenue, Vasanth Nagar EXTN, Srirangam, Trichy, Tamilnadu, 620006.
            </address>
            <div className={styles.contactMeta}>
              <div>CIN: U31100TN2010PTC078389</div>
              <div>DUNS Number: 859496557</div>
            </div>
            <div>
              <a href="mailto:info@indianpanorama.com" className={styles.emailLink}>
                info@indianpanorama.com
              </a>
            </div>
          </div>

          <h3 className={styles.newsletterTitle}>Newsletter</h3>
          <form className={styles.newsletterForm} onSubmit={(e) => e.preventDefault()}>
            <input
              type="email"
              placeholder="Enter your email address"
              className={styles.newsletterInput}
              required
            />
            <button type="submit" className={styles.submitBtn} aria-label="Subscribe">
              <RiSendPlaneLine />
            </button>
          </form>
        </div>

      </div>

      {/* ── Bottom Sub Bar ── */}
      <div className={styles.copyrightBar}>
        <div className={styles.copyrightContainer}>
          <div>© 2026 Indian Panorama All rights reserved</div>
          <div className={styles.bottomLinks}>
            <Link href="/privacy-policy">Privacy Policy</Link>
            <span className={styles.divider}>|</span>
            <Link href="/terms-and-conditions">Terms &amp; Conditions</Link>
          </div>
        </div>
      </div>
    </footer>
  );
}
