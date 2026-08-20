import { NextResponse } from "next/server";
import { getPrisma } from "@/lib/prisma";

export const dynamic = "force-dynamic";

export async function GET() {
  const result: Record<string, unknown> = {
    ok: false,
    node: process.version,
    dataSource: process.env.DATA_SOURCE ?? null,
    hasDatabaseUrl: Boolean(process.env.DATABASE_URL),
    hasAuthSecret: Boolean(process.env.AUTH_SECRET),
    authUrl: process.env.AUTH_URL ?? null,
  };

  try {
    const prisma = getPrisma();
    if (!prisma) {
      result.error = "DATABASE_URL is missing";
      return NextResponse.json(result, { status: 500 });
    }
    await prisma.$queryRaw`SELECT 1`;
    const users = await prisma.user.count();
    result.ok = true;
    result.userCount = users;
    return NextResponse.json(result);
  } catch (error) {
    result.error = error instanceof Error ? error.message : String(error);
    result.stack = error instanceof Error ? error.stack : null;
    return NextResponse.json(result, { status: 500 });
  }
}
