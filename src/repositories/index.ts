import { createFixtureRepository } from "./fixture-repository";
import { createPrismaRepository } from "./prisma-repository";
import type { Repositories } from "./contracts";

let cached: Repositories | null = null;

export function getRepositories(): Repositories {
  if (!cached) {
    cached =
      process.env.DATA_SOURCE === "prisma" && process.env.DATABASE_URL
        ? createPrismaRepository()
        : createFixtureRepository();
  }
  return cached;
}

export type { Repositories } from "./contracts";
