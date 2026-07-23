import type { FaqItem } from "@/app/components/common/FaqSection";

import type { TourSliderItem } from "./TourSlider";

// ── Hero ──
export const heroContent = {
  title: "Travel Experiences Across India",
  subtitle: "Many Journeys, One Incredible India",
  bgImage: "/images/about-banner-pages.jpg",
};

// ── Popular Experiences ──
export interface ExperienceCard {
  id: number;
  title: string;
  description: string;
  image: string;
  url: string;
}

export const experienceSectionHeading = {
  title: "Popular Experiences",
  description:
    "India, being the seventh largest country globally, stands out as an extraordinary destination due to its diverse landscape and rich cultural heritage. It is often hailed as the best choice for travelers, boasting numerous breathtaking and culturally significant destinations offering a wide range of experiences for travelers who possess different travel dreams.",
};

export const experienceCards: ExperienceCard[] = [
  {
    id: 1,
    title: "Heritage Tours",
    description:
      "India is a land where you can get a glimpse into the rich history, culture, stunning architecture and sculptures and their sites whilst the various traditions, religions and civilisations.",
    image: "/images/img-1.jpg",
    url: "/experiences/heritage",
  },
  {
    id: 2,
    title: "Spiritual",
    description:
      "Embark on a journey of enlightenment as you walk through the spiritual sites of India, which promise soulful self-exploration and finding inner peace.",
    image: "/images/img-2.jpg",
    url: "/experiences/spiritual",
  },
  {
    id: 3,
    title: "Nature & Wildlife",
    description:
      "Exploring Indian nature and wildlife is a memorable experience that gives insights into India's rich cultural heritage and biodiversity.",
    image: "/images/img-3.jpg",
    url: "/experiences/nature-wildlife",
  },
  {
    id: 4,
    title: "Tourist Places",
    description:
      "Explore the enchanting attractions of India to uncover the myths and marvellous splendours where you can have glimpses of some of the country's secretive secrets.",
    image: "/images/img-4.jpg",
    url: "/experiences/tourist-places",
  },
];

// ── India Destinations ──
export interface DestinationCard {
  id: number;
  title: string;
  image: string;
  tourCount: number;
  url: string;
}

export const destinationsSectionHeading = {
  title: "India Destinations",
  viewAllUrl: "/tour-packages",
};

export const destinationCards: DestinationCard[] = [
  {
    id: 1,
    title: "North India Tours",
    image: "/images/red-fort.png",
    tourCount: 9,
    url: "/tour-packages",
  },
  {
    id: 2,
    title: "Varanasi Tours",
    image: "/images/img-1.jpg",
    tourCount: 17,
    url: "/tour-packages",
  },
  {
    id: 3,
    title: "Goa Tours",
    image: "/images/img-4.jpg",
    tourCount: 13,
    url: "/tour-packages",
  },
  {
    id: 4,
    title: "Kerala Tours",
    image: "/images/img-2.jpg",
    tourCount: 17,
    url: "/kerala/tour-packages",
  },
  {
    id: 5,
    title: "Rajasthan Tours",
    image: "/images/img-3.jpg",
    tourCount: 17,
    url: "/rajasthan/tour-packages",
  },
  {
    id: 6,
    title: "South India Tours",
    image: "/images/banana-leaf-food-kerala.png",
    tourCount: 9,
    url: "/tamil-nadu/tour-packages",
  },
];

// ── Fairs & Festivals Content ──
export const fairsAndFestivals = {
  heading: "Fairs And Festivals Of India",
  paragraphs: [
    "To truly grasp the spirit of the state, incorporate the colourful celebrations into your Tamil Nadu tour itinerary. The festivals of Tamil Nadu beautifully blend religious reverence, cultural performance, and seasonal celebrations, offering a joyful glimpse into the deep-rooted customs of the common people.",
    "The most significant event is Pongal, the annual harvest festival celebrated over four days in January. This festival, a time of thanksgiving to the Sun God, is synonymous with the traditional, ancient bull-taming sport of Jallikattu. Another major celebration is Puthandu, the Tamil New Year, which falls in mid-April and marks the start of the Tamil Calendar with great fanfare.",
    "For art and spiritual enthusiasts planning Tamil Nadu tours and travels, the state offers unique cultural festivals. The Natyanjali Dance Festival in Chidambaram is a spectacular homage to Lord Nataraja (Lord Shiva), where classical dancers from around the world perform. Karthigai Deepam, the 'Festival of Lights', is celebrated on the alignment of the Karthigai constellation, illuminating the state. Additionally, festivals like Thaipusam and the once-in-10-years Mahamaham Festival in Kumbakonam draw massive congregations, cementing Tamil Nadu's reputation as a land of vibrant and unforgettable festivities.",
  ],
};

// ── India Festivals Slider ──
export interface FestivalCard {
  id: number;
  title: string;
  image: string;
  url: string;
}

export const festivalsSectionHeading = {
  title: "India Festivals",
  viewAllUrl: "/india-festivals",
};

export const festivalCards: FestivalCard[] = [
  {
    id: 1,
    title: "Mahashivratri",
    image: "/images/img-1.jpg",
    url: "/festivals",
  },
  {
    id: 2,
    title: "Chitrai Festival",
    image: "/images/img-2.jpg",
    url: "/festivals",
  },
  {
    id: 3,
    title: "Pongal",
    image: "/images/img-3.jpg",
    url: "/festivals",
  },
  {
    id: 4,
    title: "Aadi Perukku",
    image: "/images/img-4.jpg",
    url: "/festivals",
  },
  {
    id: 5,
    title: "Diwali",
    image: "/images/red-fort.png",
    url: "/festivals",
  },
  {
    id: 6,
    title: "Holi",
    image: "/images/banana-leaf-food-kerala.png",
    url: "/festivals",
  },
];

// ── Explore Popular States ──
export const popularStates: TourSliderItem[] = [
  { slug: "tamil-nadu",    title: "Chennai",       image: "/images/img-1.jpg",                  tours_count: 12 },
  { slug: "uttarakhand",   title: "Uttrakhand",    image: "/images/img-2.jpg",                  tours_count: 9  },
  { slug: "kerala",        title: "Kerala",        image: "/images/banana-leaf-food-kerala.png", tours_count: 17 },
  { slug: "rajasthan",     title: "Rajasthan",     image: "/images/img-3.jpg",                  tours_count: 14 },
  { slug: "goa",           title: "Goa",           image: "/images/img-4.jpg",                  tours_count: 11 },
  { slug: "himachal-pradesh", title: "Himachal Pradesh", image: "/images/red-fort.png",         tours_count: 8  },
];

// ── Popular City Experiences ──
export const popularCities: TourSliderItem[] = [
  {
    slug: "jaipur",
    title: "Jaipur City Tours",
    image: "/images/img-3.jpg",
    tours_count: 12,
    description: "Explore Jaipur's iconic forts, palaces, and vibrant culture.",
    popular_spots: "Amber Fort | Hawa Mahal | City Palace | Jantar Mantar",
  },
  {
    slug: "agra",
    title: "Agra Heritage Tours",
    image: "/images/img-1.jpg",
    tours_count: 9,
    description: "Discover Agra's iconic monuments, rich history, and cultural heritage.",
    popular_spots: "Taj Mahal | Agra Fort | Itmad-ud-Daulah | Mehtab Bagh",
  },
  {
    slug: "delhi",
    title: "Delhi City Tours",
    image: "/images/red-fort.png",
    tours_count: 17,
    description: "Discover Delhi's iconic landmarks and rich cultural heritage.",
    popular_spots: "Red Fort | India Gate | Qutub Minar | Humayun's Tomb",
  },
  {
    slug: "varanasi",
    title: "Varanasi Tours",
    image: "/images/img-2.jpg",
    tours_count: 8,
    description: "Experience the spiritual heart of India on the banks of the Ganges.",
    popular_spots: "Ganga Aarti | Sarnath | Kashi Vishwanath Temple | Manikarnika Ghat",
  },
  {
    slug: "mumbai",
    title: "Mumbai City Tours",
    image: "/images/img-4.jpg",
    tours_count: 6,
    description: "Explore the city of dreams — Bollywood, beaches, and colonial heritage.",
    popular_spots: "Gateway of India | Marine Drive | Elephanta Caves | Colaba Causeway",
  },
];

// ── FAQ ──
export const faqItems: FaqItem[] = [
  {
    id: 1,
    question: "What are the best travel experiences to have in India?",
    answer:
      "India offers diverse experiences including cultural tours, wildlife safaris, luxury train journeys, spiritual retreats, adventure activities, wellness vacations, and culinary experiences. From the backwaters of Kerala to the palaces of Rajasthan, every journey is unique and memorable.",
  },
  {
    id: 2,
    question: "Which experience is best for first-time visitors to India?",
    answer:
      "For first-time visitors, we recommend the Golden Triangle Tour (Delhi, Agra, Jaipur) as it covers iconic landmarks, rich history, and cultural depth in a compact itinerary. Kerala Backwaters and Rajasthan Heritage Tours are also excellent introductions to India's diversity.",
  },
  {
    id: 3,
    question: "What is the best time to experience different regions of India?",
    answer:
      "October to March is ideal for most regions including Rajasthan, Goa, and South India. For the Himalayan regions and North India, April to June works well. Kerala is best visited between September and March, avoiding the monsoon months of June to August.",
  },
  {
    id: 4,
    question: "Can I customize my India travel experience based on my interests?",
    answer:
      "Absolutely. At Indian Panorama, every itinerary is fully customisable. Whether you are passionate about wildlife, heritage, spirituality, cuisine, or adventure, our expert planners design bespoke journeys tailored to your interests, budget, and travel dates.",
  },
  {
    id: 5,
    question: "What are the most popular themed holidays in India?",
    answer:
      "Popular themed holidays include Heritage & Culture Tours, Wildlife & Nature Safaris, Spiritual Pilgrimages, Ayurveda & Wellness Retreats, Adventure & Trekking Tours, Culinary Journeys, Festival Tours, and Honeymoon Packages. Each theme offers a distinct and immersive travel experience.",
  },
];

// ── Popular Packages ──
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

export const popularPackages: PackageItem[] = [
  {
    id: 1,
    title: "South India Explorer",
    image: "/images/img-1.jpg",
    rating: 5,
    reviewCount: 48,
    nights: 16,
    days: 17,
    destinations: "Chennai · Odutn · Madurai",
    url: "/tamil-nadu/tour-packages",
  },
  {
    id: 2,
    title: "History of Maharashtra",
    image: "/images/img-2.jpg",
    rating: 4,
    reviewCount: 32,
    nights: 9,
    days: 10,
    destinations: "Maharashtra · Panchgani · Mumbai",
    url: "/tour-packages",
  },
  {
    id: 3,
    title: "West Bengal",
    image: "/images/img-3.jpg",
    rating: 4,
    reviewCount: 21,
    nights: 8,
    days: 9,
    destinations: "Kolkata",
    url: "/tour-packages",
  },
  {
    id: 4,
    title: "Taj Mahal",
    image: "/images/img-4.jpg",
    rating: 5,
    reviewCount: 67,
    nights: 13,
    days: 14,
    destinations: "Jaisalmer · Jodhpur · Udaipur · Agra · Delhi",
    url: "/tour-packages/four-corners-of-rajasthan",
  },
  {
    id: 5,
    title: "Kerala Backwaters",
    image: "/images/banana-leaf-food-kerala.png",
    rating: 5,
    reviewCount: 54,
    nights: 6,
    days: 7,
    destinations: "Kochi · Alleppey · Kumarakom",
    url: "/tour-packages/kerala-backwaters-6n7d",
  },
  {
    id: 6,
    title: "Rajasthan Heritage",
    image: "/images/red-fort.png",
    rating: 5,
    reviewCount: 89,
    nights: 11,
    days: 12,
    destinations: "Jaipur · Jodhpur · Udaipur · Jaisalmer",
    url: "/rajasthan/tour-packages",
  },
  {
    id: 7,
    title: "Goa Beach Escape",
    image: "/images/img-1.jpg",
    rating: 4,
    reviewCount: 43,
    nights: 5,
    days: 6,
    destinations: "North Goa · South Goa",
    url: "/tour-packages",
  },
  {
    id: 8,
    title: "North India Discovery",
    image: "/images/img-2.jpg",
    rating: 5,
    reviewCount: 61,
    nights: 9,
    days: 10,
    destinations: "Delhi · Agra · Jaipur · Varanasi",
    url: "/tour-packages",
  },
];
