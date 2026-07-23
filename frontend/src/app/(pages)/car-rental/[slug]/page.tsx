import type { Metadata } from "next";
import { notFound } from "next/navigation";
import CarRentalCityLayout from "@/app/components/car-rental/layouts/CarRentalCityLayout";
import CarRentalRouteLayout from "@/app/components/car-rental/layouts/CarRentalRouteLayout";
// import CarRentalPackageLayout from "@/app/components/car-rental/layouts/CarRentalPackageLayout"; // package pages hidden
import CarRentalVehicleLayout from "@/app/components/car-rental/layouts/CarRentalVehicleLayout";
import { fetchCarRentalDetails } from "@/services/carRental";

type Props = { params: Promise<{ slug: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const result = await fetchCarRentalDetails(slug);
  const d = result?.data;
  const title = d?.meta?.meta_title ?? d?.banner?.title ?? "Car Rental | Indian Panorama";

  return {
    title,
    description: d?.meta?.meta_description ?? undefined,
    alternates: { canonical: `https://www.indianpanorama.in/car-rental/${slug}` },
    robots: { index: true, follow: true },
    openGraph: {
      title,
      description: d?.meta?.meta_description ?? undefined,
      url: `https://www.indianpanorama.in/car-rental/${slug}`,
      siteName: "Indian Panorama",
      type: "website",
    },
  };
}

export default async function CarRentalDetailPage({ params }: Props) {
  const { slug } = await params;
  const result = await fetchCarRentalDetails(slug);

  if (!result) notFound();

  const { type, data } = result;

  switch (type) {
    case "city":    return <CarRentalCityLayout data={data} />;
    case "route":   return <CarRentalRouteLayout data={data} />;
    // package pages hidden along with the CarPackageLinks section —
    // must not open even if backend sends valid package data.
    // wapas laane ke liye: notFound() hatao aur ye line + import uncomment karo
    // case "package": return <CarRentalPackageLayout data={data} />;
    case "package": return notFound();
    case "fleet":
    case "vehicle": return <CarRentalVehicleLayout data={data} />;
    default:        return notFound();
  }
}
