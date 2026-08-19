import Image from "next/image";
import { APP_LOGO } from "@/components/brand";

export default function AuthLayout({ children }: { children: React.ReactNode }) {
  return (
    <div className="flex min-h-dvh items-center justify-center bg-[linear-gradient(180deg,#e8f3ee_0%,#f3f5f4_42%)] px-4 py-8">
      <div className="w-full max-w-md">
        <div className="mb-6 flex flex-col items-center text-center">
          <Image
            src={APP_LOGO}
            alt="Sistem Jejak GPL"
            width={280}
            height={200}
            className="h-16 w-auto max-w-[70vw] object-contain sm:h-24"
            priority
          />
          <p className="mt-2 text-xs text-muted">Lembaga Pemasaran Pertanian Persekutuan</p>
        </div>
        {children}
      </div>
    </div>
  );
}
