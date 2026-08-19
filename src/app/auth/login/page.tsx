import { Card } from "@/components/ui";
import { LoginForm } from "./login-form";

export default async function LoginPage({
  searchParams,
}: {
  searchParams: Promise<{ error?: string; role?: string }>;
}) {
  const params = await searchParams;
  const error =
    params.error === "invalid"
      ? "Emel atau kata laluan tidak sah."
      : params.error === "role"
        ? "Peranan yang dipilih tidak sepadan dengan akaun ini."
        : null;

  return (
    <Card className="px-5 py-6">
      <h1 className="mb-5 text-center text-lg font-bold">Log Masuk</h1>
      <LoginForm error={error} defaultRole={params.role} />
    </Card>
  );
}
