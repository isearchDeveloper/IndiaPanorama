import PopularPackages from "@/app/components/common/PopularPackages";
import PopularPackagesGate from "@/app/components/common/PopularPackagesGate";

export default function PagesLayout({ children }: { children: React.ReactNode }) {
  return (
    <>
      {children}
      <PopularPackagesGate>
        <PopularPackages />
      </PopularPackagesGate>
    </>
  );
}
