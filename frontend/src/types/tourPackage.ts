export interface TourDestination {
  title: string;
  highlights: string;
}

export interface TourItineraryDay {
  day: number;
  title: string;
  description: string;
}

export interface TourFaq {
  question: string;
  answer: string;
}

export interface RelatedPackage {
  slug: string;
  title: string;
  image: string;
  duration: string;
  location: string;
}

export interface TourPackageDetail {
  slug: string;
  title: string;
  duration: string;
  days: number;
  nights: number;
  shortDescription: string;
  images: string[];
  destinations: TourDestination[];
  itinerary: TourItineraryDay[];
  faqs: TourFaq[];
  relatedPackages: RelatedPackage[];
}
