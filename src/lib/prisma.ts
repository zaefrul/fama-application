import { PrismaClient } from "@prisma/client";

const globalForPrisma = globalThis as unknown as { prisma?: PrismaClient };

export function getPrisma() {
  if (!process.env.DATABASE_URL) return null;
  if (!globalForPrisma.prisma) {
    try {
      globalForPrisma.prisma = new PrismaClient({
        log: ["error"],
      });
    } catch (error) {
      throw new Error(
        `Prisma client failed to start. On Plesk run npm run db:generate (not build). ${
          error instanceof Error ? error.message : String(error)
        }`,
      );
    }
  }
  return globalForPrisma.prisma;
}
