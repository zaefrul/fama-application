import { addGalleryAction, deleteGalleryAction } from "@/app/actions/exporter";
import { CompanyNav } from "@/components/nav";
import { Button, Card, ErrorText, Field, Input, PageTitle, Select } from "@/components/ui";
import { requireRole } from "@/lib/session";
import { getRepositories } from "@/repositories";

export default async function GalleryPage({
  searchParams,
}: {
  searchParams: Promise<{ error?: string }>;
}) {
  const session = await requireRole("EXPORTER");
  const { error } = await searchParams;
  const items = session.companyId ? await getRepositories().listGallery(session.companyId) : [];

  return (
    <div className="space-y-4">
      <PageTitle title="Galeri" subtitle="Muat naik gambar kebun, lot atau buah (JPG/PNG/WEBP, maksimum 5MB)." />
      <CompanyNav />
      <Card>
        <form action={addGalleryAction} className="grid gap-3 md:grid-cols-2">
          <Field label="Kategori">
            <Select name="category">
              <option value="KEBUN">KEBUN</option>
              <option value="LOT_KEBUN">LOT KEBUN</option>
              <option value="BUAH">BUAH</option>
            </Select>
          </Field>
          <Field label="Keterangan" required>
            <Input name="description" required />
          </Field>
          <Field label="Gambar" required>
            <Input name="image" type="file" accept="image/jpeg,image/png,image/webp" required />
          </Field>
          <div className="flex items-end">
            <Button type="submit">+ Gambar</Button>
          </div>
          <div className="md:col-span-2">
            <ErrorText>{error}</ErrorText>
          </div>
        </form>
      </Card>
      <ul className="space-y-2">
        {items.map((item) => (
          <li key={item.id}>
            <Card className="flex items-center gap-3">
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img src={item.filePath} alt={item.description} className="h-16 w-16 rounded-lg object-cover" />
              <div className="flex-1">
                <p className="font-semibold">{item.description}</p>
                <p className="text-xs text-muted">{new Date(item.uploadedAt).toLocaleDateString("ms-MY")}</p>
              </div>
              <form action={deleteGalleryAction}>
                <input type="hidden" name="id" value={item.id} />
                <Button variant="danger" type="submit">
                  Buang
                </Button>
              </form>
            </Card>
          </li>
        ))}
      </ul>
    </div>
  );
}
