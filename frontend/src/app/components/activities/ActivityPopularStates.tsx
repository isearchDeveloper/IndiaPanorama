import StatesSwiper from "@/app/components/common/StatesSwiper";
import type { ActivityDestinationItem } from "@/services/activitiesService";

type Props = {
  title: string;
  items: ActivityDestinationItem[];
};

export default function ActivityPopularStates({ title, items }: Props) {
  const states = items
    .filter((d) => d.type === "state")
    .map((s) => ({
      id: s.state_slug,
      name: s.name,
      image: s.image ?? null,
      href: `/${s.state_slug}/activities`,
      toursCount: s.tours_count ?? null,
    }));

  return <StatesSwiper title={title} viewAllHref="/activities" items={states} />;
}
