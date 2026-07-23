// Server-side only — browser mein nahi chalega

const API_BASE_URL = process.env.API_BASE_URL;
const API_TOKEN = process.env.API_TOKEN;

// ─── Types ────────────────────────────────────────────────────────────────

export interface MegaMenuCity {
  id: number;
  title: string;
  url: string;
  target: string;
  type: "location";
  status: number;
  children: [];
}

export interface MegaMenuState {
  id: number;
  title: string;
  url: string;
  target: string;
  type: "state";
  status: number;
  children: MegaMenuCity[];
}

export interface MegaMenuRegion {
  id: number;
  title: string;
  url: string;
  target: string;
  type: "region";
  status: number;
  children: MegaMenuState[];
}

export interface MegaMenu {
  display_source: string;
  display_mode: string;
  items: MegaMenuRegion[];
  banner: {
    image: string;
    alt: string;
    title: string;
    description: string;
    cta_text: string;
    cta_url: string;
  };
}

export interface NavItem {
  id: number;
  title: string;
  url: string;
  target: string;
  type: string;
  content_type: "normal" | "mega_menu";
  has_children: boolean;
  children: NavItem[];
  mega_menu?: MegaMenu;
}

// ─── API Call ─────────────────────────────────────────────────────────────

export async function getHeaderMenu(): Promise<NavItem[]> {
  if (!API_BASE_URL) {
    console.error("Missing API_BASE_URL");
    return [];
  }

  try {
    const res = await fetch(`${API_BASE_URL}/header-menu`, {
      headers: {
        Accept: "application/json",
        "X-Public-Token": API_TOKEN ?? "",
      },
      next: { revalidate: 30 },
    });

    if (!res.ok) {
      console.error(`Header API failed: ${res.status}`);
      return [];
    }

    const json = await res.json();
    return json?.data?.header?.items ?? [];
  } catch (error) {
    console.error("Header menu fetch error:", error);
    return [];
  }
}
