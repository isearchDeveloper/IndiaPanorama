import FaqSection from "@/app/components/common/FaqSection";
import { faqItems } from "./indiaPageData";

export default function IndiaFaqSection() {
  return (
    <FaqSection
      heading="FAQ's"
      subtext="Find answers to the most common questions about our India tour packages, travel planning, bookings, accommodations, and customized holiday experiences."
      items={faqItems}
      sideImage={{ src: "/images/faq-side-image.webp", alt: "India Taj Mahal" }}
    />
  );
}
