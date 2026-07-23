import type { Metadata } from "next";
import { Outfit, Inter } from "next/font/google";
import Script from "next/script";
import "./globals.css";
import Header from "@/app/components/layout/Header";
import Footer from "@/app/components/layout/Footer";
import PartnerSlider from "@/app/components/common/PartnerSlider";
import PartnerSliderGate from "@/app/components/common/PartnerSliderGate";
import GoogleReviews from "@/app/components/common/GoogleReviews";

const outfit = Outfit({
  subsets: ["latin"],
  variable: "--font-outfit",
  display: "swap",
  weight: ["400", "500", "600", "700"],
});

const inter = Inter({
  subsets: ["latin"],
  variable: "--font-inter",
  display: "swap",
  weight: ["400", "500", "600", "700"],
});

// ── Default / fallback metadata ──
// Har page pe generateMetadata() se override ho sakta hai
export const metadata: Metadata = {
  title: {
    default: "Indian Panorama - India Tours & Travel",
    template: "%s | Indian Panorama",
  },
  description: "Explore India with Indian Panorama - Premium India tour packages",
  icons: {
    icon: "/favicon.webp",
    apple: "/favicon.webp",
  },
  robots: {
    index: true,
    follow: true,
  },
};

const GTM_ID = process.env.GTM_ID; // Server-only — browser mein expose nahi hoga

export default function RootLayout({
  children,
}: Readonly<{ children: React.ReactNode }>) {
  return (
    <html lang="en" className={`${outfit.variable} ${inter.variable}`}>
      <head>
        {/* ── Google Tag Manager — head script ── */}
        {GTM_ID && (
          <Script
            id="gtm-script"
            strategy="afterInteractive"
            dangerouslySetInnerHTML={{
              __html: `(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','${GTM_ID}');`,
            }}
          />
        )}
      </head>
      <body>
        {/* ── Google Tag Manager — noscript fallback (body top) ── */}
        {GTM_ID && (
          <noscript>
            <iframe
              src={`https://www.googletagmanager.com/ns.html?id=${GTM_ID}`}
              height="0"
              width="0"
              style={{ display: "none", visibility: "hidden" }}
            />
          </noscript>
        )}

        <Header />
        <main>{children}</main>
        {/* Google Reviews — har page pe PartnerSlider ke upar (company pages + 404 pe auto-hide) */}
        <GoogleReviews />
        <PartnerSliderGate>
          <PartnerSlider />
        </PartnerSliderGate>
        <Footer />
      </body>
    </html>
  );
}
