import styles from "./PartnerSlider.module.css";

const FALLBACK = "/images/about-banner-pages.jpg";

async function fetchPartners() {
  try {
    const res = await fetch("https://projects.isearchsolution.com/crm/api/v1/partners", {
      headers: { Accept: "application/json", "X-Public-Token": process.env.API_TOKEN ?? "" },
      next: { revalidate: 30 },
    });
    if (!res.ok) return [];
    const json = await res.json();
    return json?.data ?? [];
  } catch {
    return [];
  }
}

export default async function PartnerSlider() {
  const partners = await fetchPartners();
  if (!partners.length) return null;

  // Double for seamless infinite loop
  const logos = [...partners, ...partners];

  return (
    <section className={styles.section}>
      <div className={styles.sliderContainer}>
        <div className={styles.track}>
          {logos.map((p: any, i: number) => (
            <div key={i} className={styles.logoItem}>
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img src={p.image || FALLBACK} alt={p.alt ?? "Partner"} className={styles.logoImage} />
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
