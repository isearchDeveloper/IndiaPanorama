import StatesSwiper from "@/app/components/common/StatesSwiper";

type Item = {
  id: number;
  name: string;
  image: string;
  href: string;
  toursCount?: number;
};

type Props = {
  title: string;
  items: Item[];
};

export default function TAPopularStates({ title, items }: Props) {
  const mapped = items.map((item) => ({
    id: item.id,
    name: item.name,
    image: item.image ?? null,
    href: item.href,
    toursCount: item.toursCount ?? null,
  }));

  return <StatesSwiper title={title} viewAllHref="/tourist-attractions" items={mapped} />;
}
