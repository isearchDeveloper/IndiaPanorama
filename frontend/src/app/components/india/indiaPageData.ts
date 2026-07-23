export interface TourCategory {
  id: number;
  slug: string;
  title: string;
  image: string;
  tourCount: number;
  url: string;
}

export interface FaqItem {
  id: number;
  question: string;
  answer: string;
}

export interface PackageItem {
  id: number;
  title: string;
  image: string;
  rating: number;
  reviewCount: number;
  nights: number;
  days: number;
  destinations: string;
  url: string;
}

export interface ContentBlock {
  heading: string;
  paragraphs: string[];
}

export interface HeroContent {
  title: string;
  subtitle: string;
  bgImage: string;
  breadcrumb: { label: string; url: string }[];
}

// ── Hero ──
export const heroContent: HeroContent = {
  title: "All India Tour Packages",
  subtitle:
    "Indian Panorama offers thoughtfully curated India tour packages across the country. As a trusted Indian Tour operator and destination management company since 1995, we specialise in private, customised India tours designed by local experts.",
  bgImage: "/images/about-banner-pages.jpg",
  breadcrumb: [
    { label: "Home", url: "/" },
    { label: "India", url: "/tour-packages" },
  ],
};

// ── Tour Categories ──
export const sectionHeading = {
  title: "Best India Tour Packages : Luxury, Adventure & Budget",
  description:
    "Discover a diverse range of expertly designed tour experiences that bring the beauty, culture, and spirit of India and its neighbouring countries to life. Our India Tour Holiday packages are thoughtfully created to showcase the true essence of each destination, offering memorable journeys across India and beyond.",
};

export const tourCategories: TourCategory[] = [
  {
    id: 1,
    slug: "kerala",
    title: "Kerala Tours",
    image: "/images/kerala-tours-v2.webp",
    tourCount: 17,
    url: "/kerala-tours",
  },
  {
    id: 2,
    slug: "tamil-nadu",
    title: "Tamil Nadu Tours",
    image: "/images/tamil-nadu-tours-v2.webp",
    tourCount: 57,
    url: "/tamil-nadu-tours",
  },
  {
    id: 3,
    slug: "karnataka",
    title: "Karnataka Tours",
    image: "/images/karnataka-tours-v2.webp",
    tourCount: 59,
    url: "/karnataka-tours",
  },
  {
    id: 4,
    slug: "north-india",
    title: "North India Tours",
    image: "/images/north-india-tours-v2.webp",
    tourCount: 99,
    url: "/north-india-tours",
  },
  {
    id: 5,
    slug: "varanasi",
    title: "Varanasi Tours",
    image: "/images/varanasi-tours-v2.webp",
    tourCount: 17,
    url: "/varanasi-tours",
  },
  {
    id: 6,
    slug: "goa",
    title: "Goa Tours",
    image: "/images/goa-tours-v2.webp",
    tourCount: 15,
    url: "/goa-tours",
  },
  {
    id: 7,
    slug: "south-india",
    title: "South India Tours",
    image: "/images/south-india-tours-v2.webp",
    tourCount: 9,
    url: "/south-india-tours",
  },
  {
    id: 8,
    slug: "rajasthan",
    title: "Rajasthan Tours",
    image: "/images/rajasthan-tours-v2.webp",
    tourCount: 17,
    url: "/rajasthan-tours",
  },
  {
    id: 9,
    slug: "delhi",
    title: "Delhi Tours",
    image: "/images/delhi-tours-v2.webp",
    tourCount: 12,
    url: "/delhi-tours",
  },
];

// ── Content Section ──
export const contentSection: ContentBlock = {
  heading: "Luxury, Adventure & Budget",
  paragraphs: [
    "Discover a diverse range of expertly designed tour experiences that bring the beauty, culture, and spirit of India and its neighbouring countries to life. Our India Tour Holiday packages are thoughtfully created to showcase the true essence of each destination, offering memorable journeys across India and beyond.",
    "Explore our most popular tours and see why they are perfect for your next unforgettable escape. Our Kerala Tours promise relaxation amid tropical backwaters, lush tea plantations, and rejuvenating Ayurveda retreats. History and culture enthusiasts will love our Rajasthan Tours, featuring grand forts, desert landscapes, and vibrant traditions.",
    "Our Tamil Nadu Tours highlight the architectural and spiritual heritage of South India, while Karnataka Tours blend ancient ruins, wildlife experiences, and scenic coffee regions. North India Tours take you from the breathtaking Himalayan foothills to iconic landmarks like the Taj Mahal, while South India Tours reveal rich coastal beauty and cultural depth.",
    "Our Central India Tours explore tiger reserves, tribal cultures, and ancient caves. East India Tours uncover hidden spiritual destinations and green landscapes, while West India Tours showcase Goa's beaches and Gujarat's heritage trails. Our Complete India Tours seamlessly combine highlights from every region.",
    "We also extend our services beyond India with Sri Lanka Tours, Nepal Tours, and Bhutan Tours, each offering unique landscapes, wildlife, and spiritual experiences.",
    "What truly sets Indian Panorama apart as a trusted Indian Tour Operator and destination Management Company is over 25 years of expertise in the travel industry. We have received national awards for excellence in tourism services, a fleet of 150+ well-maintained vehicles, and access to 1500+ handpicked hotels across India and neighbouring countries. We specialise in North, South East, West India Tour Packages, delivering immersive, safe, and exceptional travel experiences.",
  ],
};

// ── FAQ ──
export const faqItems: FaqItem[] = [
  {
    id: 1,
    question: "What are the best India tour packages for first-time travelers?",
    answer:
      "At Indian Panorama, first-time visitors can choose from customised India tour packages such as Golden Triangle Tours, Kerala Backwater Tours, Rajasthan Heritage Tours, South India Cultural Tours, and Complete India Tours. These itineraries are designed to provide a safe, comfortable, and authentic travel experience with flexible schedules, private transportation, expert local guides, and handpicked hotels.",
  },
  {
    id: 2,
    question: "Which destinations are included in popular India tour packages?",
    answer:
      "Popular India tour packages cover a wide range of destinations including Delhi, Agra, Jaipur (Golden Triangle), Kerala backwaters, Rajasthan forts and palaces, Goa beaches, Varanasi ghats, Tamil Nadu temples, Karnataka wildlife sanctuaries, and Himalayan hill stations. We also offer extensions to neighbouring countries like Sri Lanka, Nepal, and Bhutan.",
  },
  {
    id: 3,
    question: "What is the ideal duration for an India tour?",
    answer:
      "The ideal duration depends on the regions you wish to explore. A focused Golden Triangle tour typically takes 6–8 days, while a South India cultural journey may span 10–14 days. For a comprehensive all-India experience covering multiple regions, we recommend 18–21 days. Our expert tour planners can craft an itinerary that fits your schedule and interests perfectly.",
  },
  {
    id: 4,
    question: "Can I customise my India tour itinerary?",
    answer:
      "Absolutely. Every tour at Indian Panorama is fully customisable. We understand that every traveller is unique, so our experienced team works closely with you to tailor the pace, destinations, accommodation style, activities, and budget to your specific preferences. Simply share your interests and dates and we will design a bespoke itinerary just for you.",
  },
];

// ── Popular Packages ──
export const popularPackages: PackageItem[] = [
  {
    id: 1,
    title: "South India Explorer",
    image: "/images/south-india-package-v2.webp",
    rating: 5,
    reviewCount: 48,
    nights: 16,
    days: 17,
    destinations: "Chennai · Odutn · Madurai",
    url: "/packages/south-india-explorer",
  },
  {
    id: 2,
    title: "History of Maharashtra",
    image: "/images/maharashtra-package-v2.webp",
    rating: 4,
    reviewCount: 32,
    nights: 9,
    days: 10,
    destinations: "Maharashtra · Panchgani · Mumbai",
    url: "/packages/history-of-maharashtra",
  },
  {
    id: 3,
    title: "West Bengal",
    image: "/images/west-bengal-package-v2.webp",
    rating: 4,
    reviewCount: 21,
    nights: 8,
    days: 9,
    destinations: "Kolkata",
    url: "/packages/west-bengal",
  },
  {
    id: 4,
    title: "Taj Mahal",
    image: "/images/taj-mahal-package-v2.webp",
    rating: 5,
    reviewCount: 67,
    nights: 13,
    days: 14,
    destinations: "Jaisalmer · Jodhpur · Udaipur · Agra · Delhi",
    url: "/packages/taj-mahal",
  },
  {
    id: 5,
    title: "Kerala Backwaters",
    image: "/images/kerala-tours-v2.webp",
    rating: 5,
    reviewCount: 54,
    nights: 6,
    days: 7,
    destinations: "Kochi · Alleppey · Kumarakom",
    url: "/packages/kerala-backwaters",
  },
  {
    id: 6,
    title: "Rajasthan Heritage",
    image: "/images/rajasthan-tours-v2.webp",
    rating: 5,
    reviewCount: 89,
    nights: 11,
    days: 12,
    destinations: "Jaipur · Jodhpur · Udaipur · Jaisalmer",
    url: "/packages/rajasthan-heritage",
  },
  {
    id: 7,
    title: "Goa Beach Escape",
    image: "/images/goa-tours-v2.webp",
    rating: 4,
    reviewCount: 43,
    nights: 5,
    days: 6,
    destinations: "North Goa · South Goa",
    url: "/packages/goa-beach-escape",
  },
  {
    id: 8,
    title: "North India Discovery",
    image: "/images/north-india-tours-v2.webp",
    rating: 5,
    reviewCount: 61,
    nights: 9,
    days: 10,
    destinations: "Delhi · Agra · Jaipur · Varanasi",
    url: "/packages/north-india-discovery",
  },
];
