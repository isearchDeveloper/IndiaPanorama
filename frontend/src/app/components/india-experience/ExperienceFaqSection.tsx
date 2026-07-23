import FaqSection from "@/app/components/common/FaqSection";
type ApiFaqItem = { question: string; answer: string };
import type { FaqItem } from "@/app/components/common/FaqSection";

interface Props {
  title: string;
  subTitle: string;
  list: ApiFaqItem[];
}

export default function ExperienceFaqSection({ title, subTitle, list }: Props) {
  const items: FaqItem[] = list.map((f, i) => ({
    id: i + 1,
    question: f.question,
    answer: f.answer,
  }));

  if (!items.length) return null;

  return (
    <FaqSection
      heading={title}
      subtext={subTitle}
      items={items}
    />
  );
}
