/* eslint-disable @typescript-eslint/no-explicit-any */
// Experience module ke saare endpoints — baaki services jaisa hi:
// common API_BASE_URL (.env) + sirf last ka path yahan. Same server, koi alag env nahi.
const API_BASE_URL = process.env.API_BASE_URL;
const API_TOKEN = process.env.API_TOKEN;

async function fetchExperiences(path: string): Promise<any> {
  if (!API_BASE_URL) {
    console.error("Missing API_BASE_URL");
    return null;
  }

  try {
    const res = await fetch(`${API_BASE_URL}${path}`, {
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
    console.error(`Experiences fetch error [${path}]:`, error);
    return null;
  }
}

/* ── endpoints (sirf last ka path) ── */

// root /experiences hub
export const getExperiencesData = () =>
  fetchExperiences("/page/settings/experiences");

// category page — /experiences/{category}
export const getExperienceCategory = (slug: string) =>
  fetchExperiences(`/experiences/category/${slug}`);

// state hub — /{state}/experiences
export const getExperienceState = (state: string) =>
  fetchExperiences(`/experiences/state/${state}`);

// city hub — /{state}/{city}/experiences
export const getExperienceCity = (state: string, city: string) =>
  fetchExperiences(`/experiences/${state}/${city}`);

// subcategory listing — /experiences/{subcategory} (+ optional state filter)
export const getExperienceSubcategory = (slug: string, state?: string) =>
  fetchExperiences(`/experiences/subcategory/${slug}${state ? `?state=${state}` : ""}`);

// experience detail — /{state}/{city}/{slug}-experience (slug bina suffix ke)
export const getExperienceDetail = (slug: string) =>
  fetchExperiences(`/experiences/detail/${slug}`);
