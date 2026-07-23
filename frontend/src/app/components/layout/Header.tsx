// Server Component — "use client" NAHI lagaya
// Yahan data fetch hoga server pe, browser pe nahi

import { getHeaderMenu } from "@/services/headerService";
import HeaderClient from "./HeaderClient";

export default async function Header() {
  // Server pe API call — token safe, result cached 1 hour
  const navItems = await getHeaderMenu();

  // navItems ko client component ko prop ki tarah de do
  return <HeaderClient navItems={navItems} />;
}
