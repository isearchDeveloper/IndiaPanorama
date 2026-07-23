import { cache } from "react";

const API_TOKEN = process.env.API_TOKEN;

const CITY_GUIDE_API_URL =
  "https://projects.isearchsolution.com/crm/api/v1/page/settings/city-guide";

export const fetchCityGuideDetails = cache(async (): Promise<any> => {
  try {
    const res = await fetch(CITY_GUIDE_API_URL, {
      headers: {
        Accept: "application/json",
        "X-Public-Token": API_TOKEN ?? "",
      },
      next: { revalidate: 30 },
    });

    if (!res.ok) {
      console.error(`City guide API failed: ${res.status}`);
      return null;
    }

    const json = await res.json();
    return json?.data ?? null;
  } catch (error) {
    console.error("City guide fetch error:", error);
    return null;
  }
});

// Region page (e.g. west-india) — opens like a state destination page
export const fetchRegionDetails = cache(async (region: string): Promise<any> => {
  try {
    const url = `https://projects.isearchsolution.com/crm/api/v1/packages/region/${region}`;
    const res = await fetch(url, {
      headers: {
        Accept: "application/json",
        "X-Public-Token": API_TOKEN ?? "",
      },
      next: { revalidate: 30 },
    });

    if (!res.ok) {
      console.error(`Region API failed: ${res.status}`);
      return null;
    }

    const json = await res.json();
    return json?.data ?? null;
  } catch (error) {
    console.error("Region fetch error:", error);
    return null;
  }
});

export const fetchStateGuideDetails = cache(async (state: string): Promise<any> => {
  try {
    const url = `https://projects.isearchsolution.com/crm/api/v1/state/${state}`;
    const res = await fetch(url, {
      headers: {
        Accept: "application/json",
        "X-Public-Token": API_TOKEN ?? "",
      },
      next: { revalidate: 30 },
    });

    if (!res.ok) {
      console.error(`State guide API failed: ${res.status}`);
      return null;
    }

    const json = await res.json();
    return json?.data ?? null;
  } catch (error) {
    console.error("State guide fetch error:", error);
    return null;
  }
});

export const fetchCityPageDetails = cache(async (state: string, city: string): Promise<any> => {
  try {
    const url = `https://projects.isearchsolution.com/crm/api/v1/state/city/${state}/${city}`;
    const res = await fetch(url, {
      headers: {
        Accept: "application/json",
        "X-Public-Token": API_TOKEN ?? "",
      },
      next: { revalidate: 30 },
    });

    if (!res.ok) {
      console.error(`City page API failed: ${res.status}`);
      return null;
    }

    const json = await res.json();
    return json?.data ?? null;
  } catch (error) {
    console.error("City page fetch error:", error);
    return null;
  }
});
