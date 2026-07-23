import type { Metadata } from "next";
import CarRentalHomeLayout from "@/app/components/car-rental/layouts/CarRentalHomeLayout";
import { fetchCarRentalData } from "@/services/carRental";

export async function generateMetadata(): Promise<Metadata> {
  const api = await fetchCarRentalData();
  return {
    title: api?.meta?.meta_title ?? "Car Rental in India | Indian Panorama",
    description: api?.meta?.meta_description ?? undefined,
    alternates: { canonical: "https://www.indianpanorama.in/car-rental" },
    robots: { index: true, follow: true },
    openGraph: {
      title: api?.meta?.meta_title ?? "Car Rental in India | Indian Panorama",
      description: api?.meta?.meta_description ?? undefined,
      url: "https://www.indianpanorama.in/car-rental",
      siteName: "Indian Panorama",
      type: "website",
    },
  };
}

export default async function CarRentalPage() {
  const api = await fetchCarRentalData();
  return <CarRentalHomeLayout data={api} />;
}
