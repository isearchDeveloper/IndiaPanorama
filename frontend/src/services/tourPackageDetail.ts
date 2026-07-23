import { cache } from "react";

const BASE_URL = "https://projects.isearchsolution.com/crm/api/v1";
const API_TOKEN = process.env.API_TOKEN;

// eslint-disable-next-line @typescript-eslint/no-explicit-any
export const fetchTourPackageBySlug = cache(async (slug: string): Promise<any> => {
  try {
    const res = await fetch(`${BASE_URL}/package/${slug}`, {
      headers: {
        Accept: "application/json",
        "X-Public-Token": API_TOKEN ?? "",
      },
      next: { revalidate: 30 },
    });

    if (!res.ok) {
      console.error(`Package detail API failed [${slug}]: ${res.status}`);
      return null;
    }

    const json = await res.json();
    return json?.data ?? null;
  } catch (error) {
    console.error(`Package detail fetch error [${slug}]:`, error);
    return null;
  }
});

