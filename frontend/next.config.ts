import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  env: {
    API_TOKEN: "zaxsc+/-=0dfvgbnhmjklo*/-piutyerwq*%$25631478907539541lokythbfet&*(@kjhkhgfhk546456456)",
  },
  allowedDevOrigins: ["192.168.1.9", "192.168.1.17"],
  images: {
    qualities: [75, 90],
    remotePatterns: [
      { protocol: "https", hostname: "projects.isearchsolution.com" },
      { protocol: "https", hostname: "cdn.indianpanorama.in" },
    ],
  },
};

export default nextConfig;
