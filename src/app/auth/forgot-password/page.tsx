import Link from "next/link";
import { Button, Card } from "@/components/ui";

export default function ForgotPasswordPage() {
  return (
    <Card className="space-y-4">
      <h1 className="text-lg font-bold">Lupa Kata Laluan</h1>
      <p className="text-sm text-muted">
        Tetapan semula kata laluan melalui e-mel belum disambungkan untuk prototaip V1.
        Sila gunakan akaun seed atau daftar semula melalui carian mock.
      </p>
      <Button>
        <Link href="/auth/login">Kembali ke log masuk</Link>
      </Button>
    </Card>
  );
}
