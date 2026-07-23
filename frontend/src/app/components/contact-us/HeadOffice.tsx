import { MapPin, Phone, Mail, Clock } from "lucide-react";
import styles from "./HeadOffice.module.css";

export default function HeadOffice() {
  return (
    <section className={styles.section}>
      {/* faint world-map watermark — swap /images/world-map-bg.png when ready */}
      <div className={styles.mapBg} />

      <div className={styles.inner}>

        {/* ── Left : info ── */}
        <div className={styles.infoCol}>
          <h2 className={styles.title}>Head Office / Trichy</h2>
          <div className={styles.titleDivider} />

          <ul className={styles.infoList}>
            <li className={styles.infoItem}>
              <MapPin size={15} className={styles.icon} />
              <span>
                No 4, Annai Avenue, Srirangam,<br />
                Trichy, Tamil Nadu – 620 006,<br />
                South India.
              </span>
            </li>

            <li className={styles.infoItem}>
              <Phone size={15} className={styles.icon} />
              <a href="tel:+914314226122" className={styles.link}>
                +91 431 4226122
              </a>
            </li>

            <li className={styles.infoItem}>
              <Mail size={15} className={styles.icon} />
              <a href="mailto:enquiries@indianpanorama.in" className={styles.link}>
                enquiries@indianpanorama.in
              </a>
            </li>

            <li className={styles.infoItem}>
              <Clock size={15} className={styles.icon} />
              <span>
                Monday to Saturday<br />
                9:30 am to 6:00 pm
              </span>
            </li>
          </ul>
        </div>

        {/* ── Right : Google Map ── */}
        <div className={styles.mapCol}>
          <div className={styles.mapCard}>
            <iframe
              src="https://maps.google.com/maps?q=Cholan+Tours+Private+Limited,+4+Annai+Ave,+Srirangam,+Vasanth+Nagar,+Tiruchirappalli,+Tamil+Nadu+620006&t=&z=14&ie=UTF8&iwloc=&output=embed"
              className={styles.iframe}
              allowFullScreen
              loading="lazy"
              referrerPolicy="no-referrer-when-downgrade"
              title="Head Office Location - Cholan Tours Private Limited, Trichy"
            />
          </div>
        </div>

      </div>
    </section>
  );
}
