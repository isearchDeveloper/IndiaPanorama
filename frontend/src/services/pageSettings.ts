const API_TOKEN = process.env.API_TOKEN;
const BASE = "https://projects.isearchsolution.com/crm/api/v1";

// generic CMS pages (privacy-policy, terms-and-conditions, ...) —
// GET /page/setting/{slug} → { title, slug, seo, sections[{type, content:{heading, body}}] }
// eslint-disable-next-line @typescript-eslint/no-explicit-any
export async function getPageSettings(slug: string): Promise<any> {
  try {
    const res = await fetch(`${BASE}/page/setting/${slug}`, {
      headers: {
        Accept: "application/json",
        "X-Public-Token": API_TOKEN ?? "",
      },
      next: { revalidate: 30 },
    });

    if (!res.ok) return null;

    const json = await res.json();
    return json?.data ?? null;
  } catch (error) {
    console.error(`Page settings fetch error [${slug}]:`, error);
    return null;
  }
}
