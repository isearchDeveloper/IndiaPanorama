import IconCards from "@/app/components/common/IconCards";

type BestTimeCard = {
  id: number;
  season: string;
  months: string;
  description: string;
  icon: string;
};

type Props = {
  heading: string;
  items: BestTimeCard[];
};

export default function BestTimeCards({ heading, items }: Props) {
  const mapped = items.map((item) => ({
    id: item.id,
    title: item.season || item.months,
    description: item.description,
  }));

  return <IconCards heading={heading} items={mapped} />;
}
