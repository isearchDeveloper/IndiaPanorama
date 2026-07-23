/* eslint-disable @typescript-eslint/no-explicit-any */
import { MapPin, Phone } from "lucide-react";
import styles from "./ConnectWithUs.module.css";

// pura API-driven — /page/setting/contact-us ke branches se aata hai.
// items na aaye to section render hi nahi hota (API-only rule, koi static fallback nahi).
interface Props {
  heading?: string | null;
  body?: string | null;
  items: any[];
}

export default function ConnectWithUs({ heading, body, items }: Props) {
  if (!items || items.length === 0) return null;

  return (
    <section className={styles.section}>
      <div className={styles.inner}>

        <div className={styles.header}>
          <h2 className={styles.title}>{heading ?? "Connect with us"}</h2>
          {body && (
            <p
              className={styles.subtitle}
              dangerouslySetInnerHTML={{ __html: body }}
            />
          )}
        </div>

        <div className={styles.grid}>
          {items.map((office: any) => (
            <div key={office.id ?? office.name} className={styles.card}>
              <h3 className={styles.city}>{office.name}</h3>
              <div className={styles.divider} />

              {office.address && (
                <div className={styles.infoRow}>
                  <MapPin size={14} className={styles.icon} />
                  <p className={styles.address}>{office.address}</p>
                </div>
              )}

              {(office.phones ?? []).length > 0 && (
                <div className={styles.infoRow}>
                  <Phone size={14} className={styles.icon} />
                  <p className={styles.phones}>
                    {office.phones.map((phone: string, i: number) => (
                      <span key={i}>
                        <a href={`tel:${phone.replace(/\s/g, "")}`} className={styles.phoneLink}>
                          {phone}
                        </a>
                        {i < office.phones.length - 1 && ", "}
                      </span>
                    ))}
                  </p>
                </div>
              )}
            </div>
          ))}
        </div>

      </div>
    </section>
  );
}
