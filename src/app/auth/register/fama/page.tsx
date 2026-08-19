import { FamaRegisterForm } from "./register-form";

export default async function FamaRegisterPage({
  searchParams,
}: {
  searchParams: Promise<{ error?: string }>;
}) {
  const params = await searchParams;
  return <FamaRegisterForm error={params.error} />;
}
