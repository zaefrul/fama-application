import { ExporterRegisterForm } from "./register-form";

export default async function ExporterRegisterPage({
  searchParams,
}: {
  searchParams: Promise<{ error?: string }>;
}) {
  const params = await searchParams;
  return <ExporterRegisterForm error={params.error} />;
}
