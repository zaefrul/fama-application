import { getCompanyRegistry } from "@/integrations";
import { NextResponse } from "next/server";

export async function GET(
  _request: Request,
  { params }: { params: Promise<{ identifier: string }> },
) {
  const { identifier } = await params;
  const company = await getCompanyRegistry().findCompany(identifier);
  if (!company) {
    return NextResponse.json({ error: "Tiada rekod dijumpai" }, { status: 404 });
  }
  return NextResponse.json(company);
}
