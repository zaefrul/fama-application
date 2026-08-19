export default function NotFound() {
  return (
    <main className="mx-auto max-w-lg px-4 py-16 text-center">
      <h1 className="text-2xl font-bold">Halaman tidak dijumpai</h1>
      <p className="mt-2 text-sm text-muted">Semak semula pautan atau kembali ke log masuk.</p>
      <a href="/auth/login" className="mt-4 inline-block text-sm font-semibold text-brand">
        Ke log masuk
      </a>
    </main>
  );
}
