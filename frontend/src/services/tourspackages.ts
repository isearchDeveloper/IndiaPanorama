import { cache } from "react";

const API_TOKEN = process.env.API_TOKEN;

const BASE_URL = "https://projects.isearchsolution.com/crm/api/v1";

const TOUR_PACKAGES_API_URL =
  `${BASE_URL}/page/settings/holidays/holidays-tour-packages-holiday`;

const STATE_PACKAGES_API_URL = `${BASE_URL}/packages/state`;

// Global popular packages (shown site-wide after FAQ)
// eslint-disable-next-line @typescript-eslint/no-explicit-any
export const fetchPopularPackages = cache(async (): Promise<any[]> => {
  try {
    const res = await fetch(`${BASE_URL}/packages/popular`, {
      headers: {
        Accept: "application/json",
        "X-Public-Token": API_TOKEN ?? "",
      },
      next: { revalidate: 30 },
    });

    if (!res.ok) {
      console.error(`Popular packages API failed: ${res.status}`);
      return [];
    }

    const json = await res.json();
    return Array.isArray(json?.data) ? json.data : [];
  } catch (error) {
    console.error("Popular packages fetch error:", error);
    return [];
  }
});

// â”€â”€ Root tour packages page â”€â”€
// eslint-disable-next-line @typescript-eslint/no-explicit-any
export const fetchTourPackageDetails = cache(async (): Promise<any> => {
  try {
    const res = await fetch(TOUR_PACKAGES_API_URL, {
      headers: {
        Accept: "application/json",
        "X-Public-Token": API_TOKEN ?? "",
      },
      next: { revalidate: 30 },
    });

    if (!res.ok) {
      console.error(`Tour package API failed: ${res.status}`);
      return null;
    }

    const json = await res.json();
    return json?.data ?? null;
  } catch (error) {
    console.error("Tour package fetch error:", error);
    return null;
  }
});

// â”€â”€ City-level packages page: /[state]/[city]/tour-packages â”€â”€
// eslint-disable-next-line @typescript-eslint/no-explicit-any
export const fetchCityTourPackages = cache(async (state: string, city: string): Promise<any> => {
  try {
    const res = await fetch(`${STATE_PACKAGES_API_URL}/${state}/city/${city}`, {
      headers: {
        Accept: "application/json",
        "X-Public-Token": API_TOKEN ?? "",
      },
      next: { revalidate: 30 },
    });

    if (!res.ok) {
      console.error(`City tour packages API failed [${state}/${city}]: ${res.status}`);
      return null;
    }

    const json = await res.json();
    return json?.data ?? null;
  } catch (error) {
    console.error(`City tour packages fetch error [${state}/${city}]:`, error);
    return null;
  }
});

// â”€â”€ State-level packages page: /[state]/tour-packages â”€â”€
// eslint-disable-next-line @typescript-eslint/no-explicit-any
export const fetchStateTourPackages = cache(async (state: string): Promise<any> => {
  try {
    const res = await fetch(`${STATE_PACKAGES_API_URL}/${state}`, {
      headers: {
        Accept: "application/json",
        "X-Public-Token": API_TOKEN ?? "",
      },
      next: { revalidate: 30 },
    });

    if (!res.ok) {
      console.error(`State tour packages API failed [${state}]: ${res.status}`);
      return null;
    }

    const json = await res.json();
    return json?.data ?? null;
  } catch (error) {
    console.error(`State tour packages fetch error [${state}]:`, error);
    return null;
  }
});

