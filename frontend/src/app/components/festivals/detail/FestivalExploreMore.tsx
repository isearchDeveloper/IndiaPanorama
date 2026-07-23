import IndiaPopularPackages from "@/app/components/india/IndiaPopularPackages";
import type { FestivalDetailExploreItem } from "@/services/festivalsService";

interface Props {
  title: string;
  items: FestivalDetailExploreItem[];
}

export default function FestivalExploreMore({ title, items }: Props) {
  if (!items.length) return null;

  const packages = items.map((item, i) => ({
    id: i,
    title: `${item.state_name} Festivals`,
    image: item.image,
    image_alt: item.image_alt,
    duration_days: item.duration_days,
    duration_nights: item.duration_nights,
    slug: `${item.state_slug}/festivals`,
    url: `/${item.state_slug}/festivals`,
  }));

  return <IndiaPopularPackages heading={title} packages={packages} />;
}
