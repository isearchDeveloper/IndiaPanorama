import { cache } from "react";

const BASE = "https://projects.isearchsolution.com/crm/api/v1";
const HEADERS = { Accept: "application/json", "X-Public-Token": process.env.API_TOKEN ?? "" };
const OPTS = { next: { revalidate: 30 } } as const;

async function apiFetch(path: string) {
  try {
    const res = await fetch(`${BASE}${path}`, { headers: HEADERS, ...OPTS });
    if (!res.ok) return null;
    return await res.json();
  } catch {
    return null;
  }
}

// Home page: GET /car-rental
export const fetchCarRentalData = cache(async () => {
  const json = await apiFetch("/car-rental");
  return json?.data ?? null;
});

// [slug] page: tries all 4 endpoints, returns { type, data }
export const fetchCarRentalDetails = cache(async (slug: string) => {
  const endpoints = [
    { type: "city", path: `/car-rental/city/${slug}` },
    { type: "route", path: `/car-rental/route/${slug}` },
    { type: "fleet", path: `/car-rental/category/${slug}` },
    { type: "package", path: `/car-rental/packages/${slug}` },
    { type: "vehicle", path: `/car-rental/detail/${slug}` },
  ];

  for (const { type, path } of endpoints) {
    const json = await apiFetch(path);
    if (json?.data) return { type, data: json.data };
  }

  return null;
});

