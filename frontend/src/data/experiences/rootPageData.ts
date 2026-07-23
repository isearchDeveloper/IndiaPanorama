// ── Experience module ke shared TYPES ──
// (content ab API se aata hai — static arrays 2026-07-10 ko removed;
//  ye interfaces components/themePagesData use karte hain)

export interface ExpStat {
  stat: string;
  label: string;
}

export interface ExpTheme {
  name: string;
  slug: string;
  image: string;
  image_alt: string;
  description: string;
}

export interface ExpBestTime {
  id: number;
  title: string;
  description: string;
}

export interface ExpSignatureItem {
  title: string;
  slug: string;
  image: string;
  image_alt: string;
  toursCount: string;
  description: string;
  popularTag: string;
  href: string;
}

export interface ExpStateItem {
  name: string;
  slug: string;
  image: string;
  href: string;
  toursCount?: string;
}

export interface ExpWhyItem {
  icon: string;
  label: string;
}

export interface ExpFaq {
  id: number;
  question: string;
  answer: string;
}
