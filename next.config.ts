import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  typedRoutes: false,
  serverExternalPackages: ["@prisma/client", "prisma", ".prisma/client"],
  webpack: (config, { isServer }) => {
    if (isServer) {
      const externals = Array.isArray(config.externals) ? config.externals : [];
      config.externals = [...externals, "@prisma/client", ".prisma/client"];
    }
    return config;
  },
  eslint: {
    ignoreDuringBuilds: true,
  },
  experimental: {
    cpus: 1,
    workerThreads: false,
    serverActions: {
      bodySizeLimit: "8mb",
      allowedOrigins: ["jejakgpl.metadatasystem.my", "localhost:3000"],
    },
  },
};

export default nextConfig;
