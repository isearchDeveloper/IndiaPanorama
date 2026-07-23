import type { CityAttractionItem } from "@/services/activitiesService";
import AttractionCards from "@/app/components/tourist-attractions/AttractionCards";

interface Props {
  title: string;
  stateSlug: string;
  citySlug: string;
  items: CityAttractionItem[];
}

export default function CityTopAttractions({ title, stateSlug, citySlug, items }: Props) {
  if (!items.length) return null;

  const mapped = items.map((item, i) => ({
    id: i + 1,
    name: item.name,
    image: item.image ?? "",
    description: "",
    href: `/${stateSlug}/${citySlug}/${item.slug}`,
  }));

  return <AttractionCards heading={title} items={mapped} columns={3} />;
}
