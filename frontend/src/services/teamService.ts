import { cache } from "react";

const BASE = "https://projects.isearchsolution.com/crm/api/v1";
const HEADERS = { Accept: "application/json", "X-Public-Token": process.env.API_TOKEN ?? "" };

export const fetchTeamPage = cache(async () => {
  try {
    const res = await fetch(`${BASE}/page/setting/our-team`, {
      headers: HEADERS,
      next: { revalidate: 30 },
    });
    if (!res.ok) return null;
    const json = await res.json();
    return json?.data ?? null;
  } catch {
    return null;
  }
});

