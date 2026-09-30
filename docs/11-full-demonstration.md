# Full demonstration script — Sistem Jejak GPL

Use this script to walk a room through the prototype from usahawan login to a public QR scan, then a lot split. Allow **40–50 minutes**. A shorter **20-minute** path is at the end.

The screens are in Malay. Say the story in whatever language the room uses, and click the labels exactly as written below.

## The story in one minute

Sistem Jejak GPL lets an **usahawan** record an export, lets **Pegawai FAMA** review it, and lets the **public** see a verified product page only after FAMA approves.

A QR can exist before approval. Until FAMA approves, the public page says **QR Belum Diaktifkan**. Approval turns that same QR **Aktif** and opens the traceability page.

That first QR is the whole batch. After it is active, the company holding it can break the remaining quantity into smaller lots. Each chunk gets its own QR, already active, linked back to the batch. The public page shows those stops as a checkpoint line under **Jejak pecahan**.

DagangNet and iFAMA in this prototype are **mock lookups**, not the live government systems. Certificate files are uploaded copies. There is no live check with a certificate authority, and there is no hardware label printer.

## Before the session

### Open the app

Use the URL you were given for the demo environment.

If you are running it on this machine:

```bash
php artisan serve
```

Then open `http://localhost:8000`. The home page sends you to **Log Masuk**.

### Two browser windows

| Window | Use |
|---|---|
| Normal window | Log in as usahawan, then later as Pegawai FAMA |
| Private / incognito window | Public QR pages, so login is not lost |

Optional: a phone on the same network, to scan the QR shown on screen. If the phone cannot reach the demo URL, open the public link in the incognito window instead. That is the same page a scan would open.

### Accounts

On the login screen, the role toggle must match the account. The wrong role shows **Peranan yang dipilih tidak sepadan dengan akaun ini.**

| Who | Role toggle | Email | Password |
|---|---|---|---|
| Usahawan Ali bin Abu, ABC Fruits Sdn. Bhd. | **USAHAWAN** | `ali@abcfruits.example` | `Exporter123!` |
| Pegawai FAMA Ali bin Abu Ghani | **FAMA** | `aliabu@fama.gov.my` | `Fama123!` |

A second usahawan exists for the lot split in Part 10: Siti Aminah, MTS Fruits, `siti@mtsfruits.example` / `Exporter123!`. You do not need her before that.

### Records already on screen

ABC Fruits (Ali) already has one example of each status. **Show these. Do not approve, reject, or edit them during the live demo.** The live story uses a new application you create in the room.

| Application | Product | Status | QR | Public link |
|---|---|---|---|---|
| FAMA-2026-000123 | Durian Musang King | Dalam Semakan | GPL-QR-000123 Belum Aktif | `/trace/GPL-QR-000123` |
| FAMA-2026-000015 | Mangga Harumanis | Diluluskan | GPL-QR-000015 Aktif | `/trace/GPL-QR-000015` |
| FAMA-2026-000011 | Mangga Chokanan | Ditolak | GPL-QR-000011 Belum Aktif | `/trace/GPL-QR-000011` |
| FAMA-2026-000124 | Nangka Tekam Yellow | Draf | no QR yet | — |

MTS Fruits also has an approved active QR, **GPL-QR-000109** (Tembikai), at `/trace/GPL-QR-000109`. Use Ali’s Mangga Harumanis if you want the approved example to belong to the logged-in company.

### Do not do this on the demo database

- Do not run `php artisan migrate:fresh`, `migrate:refresh`, `migrate:reset`, or `db:wipe`.
- Do not finish **Daftar** for `H0B00001` or `H0B00002`. Those companies already have accounts.
- Do not finish **Daftar FAMA** for `770101145533`. That officer already has an account.
- Do not click **Sahkan** or **Tolak** on FAMA-2026-000123. That record is the prepared “waiting for review / QR still inactive” example.
- Do not split a prepared QR (**GPL-QR-000015**, **GPL-QR-000109**, **GPL-QR-000123**). Split only the new Pisang from Part 4. A split writes a child QR and reduces the remaining quantity.
- A rejected application cannot be edited and sent again in this prototype. If you want to show **Tolak** live, create a second new application and reject that one only.

## Suggested timing

| Part | Minutes | What the room sees |
|---|---:|---|
| 1. Login and usahawan home | 4 | Who the usahawan is |
| 2. Company profile | 4 | DagangNet fields, produce, certificates, gallery |
| 3. Prepared applications | 5 | Draf, semakan, lulus, tolak |
| 4. Create a new application | 6 | Inactive QR is born with the draft |
| 5. Public page before approval | 3 | QR Belum Diaktifkan |
| 6. Submit | 2 | Status becomes Dihantar |
| 7. FAMA dashboard and approval | 7 | Same QR becomes Aktif |
| 8. Public page after approval | 5 | Traceability page, languages, one checkpoint |
| 9. Download QR and audit | 4 | PNG/PDF and the audit trail |
| 10. Split the batch | 6 | Child QR, checkpoint line, FAMA tree |

## Part 1 — Log in as usahawan

1. Open `/auth/login`.
2. Select **USAHAWAN**.
3. Email: `ali@abcfruits.example`
4. Password: `Exporter123!`
5. Click **Log Masuk**.

You land on **Utama**.

Say: “This is the usahawan for ABC Fruits Sdn. Bhd. The tick beside the company name means the account is tied to a DagangNet company record.”

Point at:

- **QR Aktif** and **QR Belum Aktif**
- **Jumlah Permohonan**, **Permohonan Lulus**, **Permohonan Gagal**
- **Tindakan Pantas**: add produce, add a certificate, print a QR
- The recent **Permohonan** list
- The **Kod QR** card
- The **Galeri** photo

Left menu (desktop) or bottom menu (phone):

- **Utama**
- **Permohonan**
- **Kod QR**
- **Pecahan**
- **Sijil**
- **Profil**

**Pecahan** is the lot list. You use it in Part 10. On the home screen, leave it.

## Part 2 — Company profile

1. Click **Kemas Kini Profil**, or **Profil** in the menu.
2. On **Maklumat Syarikat**, show:
   - **No. Pendaftaran** `AB34567` — read only
   - **Nama Syarikat** `ABC Fruits Sdn. Bhd.` — read only
   - Address, phone, email, and website — the usahawan can update these
3. Say: “Name and registration number come from DagangNet and stay read-only. The usahawan maintains the contact fields.”
4. Do not click **Simpan** unless you intentionally changed a field you are happy to keep.
5. Use the tabs **Syarikat**, **Keluaran**, **Sijil**, **Galeri**.

**Keluaran** already lists Durian, Nangka, and Manggis. Say the usahawan maintains the produce the company exports. Do not click **Buang**.

**Sijil** already has MyGAP, HACCP, HALAL, Fitosanitasi, ISO 22000, and CoC `STB181019EJ100436`. Open one file with **Buka fail**, then close it. Say: “These are uploaded copies for the prototype. V1 does not call a live certificate authority.”

**Galeri** shows kebun, lot kebun, and buah photos. These support the company profile. The picture printed on a public QR is chosen later, on the application.

## Part 3 — Show the four prepared statuses

1. Open **Permohonan**.
2. Click each row below, speak the status, then go back. Do not submit, approve, or edit them.

**FAMA-2026-000124 · Nangka Tekam Yellow — Draf**

- Still editable.
- No QR until it is saved again from this screen.

**FAMA-2026-000123 · Durian Musang King — Dalam Semakan**

- Already submitted.
- QR **GPL-QR-000123** is **Belum Aktif**.
- Say: “FAMA can see this. The public cannot see the product page yet.”
- Leave it. You will open its public link in Part 5 only as a prepared example. The live approval uses the new application from Part 4.

**FAMA-2026-000015 · Mangga Harumanis — Diluluskan**

- QR **GPL-QR-000015** is **Aktif**.
- This is the “already finished” example.

**FAMA-2026-000011 · Mangga Chokanan — Ditolak**

- Decision text: **Ditolak · Maklumat gred tidak konsisten dengan sijil CoC.**
- QR **GPL-QR-000011** stays **Belum Aktif**.
- Say: “Rejection does not activate the QR. The reason is stored with the decision.”

## Part 4 — Create a new application in the room

1. On **Permohonan**, click **+ Tambah**.
2. The page says a inactive QR is generated when the draft is saved.
3. Fill **Maklumat Keluaran**:

| Field | Type this |
|---|---|
| Jenis Keluaran Pertanian | Pisang |
| Varieti | Cavendish |
| Gred | A |
| Saiz | M |
| Bilangan Eksport / Berat (kg) | 500 |
| Destinasi | China |
| No Sijil CoC | STB181019EJ100436 |
| Gambar paparan QR | optional; skip if you have no image ready |

4. Fill **Maklumat Eksport**:

| Field | Type this |
|---|---|
| Tarikh Eksport | a date about two weeks ahead |
| Nama Ladang | Ladang Pisang Raub |
| No. Lot | LOT-P01 |
| Lokasi ladang | Raub, Pahang |
| Latitud | 3.793 |
| Longitud | 101.857 |
| Pengimport | South China Fresh Co. |
| Alamat Pengimport | No. 18, Tianhe, Guangzhou, China |

5. Click **Seterusnya**. **Simpan** does the same thing on this screen: it stores the draft and generates the QR.
6. You land on the new application, status **Draf**, with a **Kod QR** card status **Belum Aktif**.
7. Write down the new QR ID (it looks like `GPL-QR-000125` or the next free number). You need it for the public page.

Say: “The QR exists now, but it is not a public certificate of approval. FAMA has not reviewed it.”

## Part 5 — Public page while the QR is inactive

1. In the **incognito** window, open:

```text
/trace/GPL-QR-000123
```

2. The page title is **QR Belum Diaktifkan**. Only the QR ID is shown. No product, farm, or importer.
3. Then open the **new** QR from Part 4 the same way. It shows the same inactive state.
4. Optional contrast, still in incognito:

```text
/trace/GPL-QR-000015
```

That one is already **Aktif** (Mangga Harumanis). Scroll only long enough to say “this is what approval unlocks; we will do it live in a moment.”

5. Optional invalid code:

```text
/trace/GPL-QR-999999
```

The page says **Kod QR tidak sah.**

## Part 6 — Usahawan submits

1. Return to the normal window, still on the new Pisang draft.
2. Click **Hantar**.
3. The form locks. Status becomes **Dihantar**.
4. The QR stays **Belum Aktif**.

Say: “Submission sends it to FAMA. It does not activate the QR.”

## Part 7 — Pegawai FAMA reviews and approves

1. Click **Log Keluar** at the bottom of the left menu.
2. On **Log Masuk**, select **FAMA**.
3. Email: `aliabu@fama.gov.my`
4. Password: `Fama123!`
5. Click **Log Masuk**.

You land on **Utama FAMA**.

Say: “This is the monitoring view, not the usahawan view.”

Point at, briefly:

- **Syarikat Aktif**, **Usahawan**, **Permohonan QR**
- **QR Aktif** and **QR Belum Aktif**
- **Menunggu Pengesahan**
- **10 buah paling kerap** and **Destinasi eksport**
- **Pemantauan Harian Bilangan QR Yang Dijana**
- **Imbasan Halaman Awam QR** — each open of a real public QR page counts as one scan; visitors are not named

Left menu:

- **Utama**
- **Pengurusan QR**
- **Kelulusan QR**
- **Maklumat Syarikat**

On a phone, the header menu opens **Menu Utama** with the same links plus **Log Keluar**.

### Approve the new application

1. Open **Kelulusan QR**.
2. Click the Pisang application you just submitted (status **Dihantar**).
3. Opening it moves **Dihantar** to **Dalam Semakan** by itself.
4. Read the summary aloud: product, company, farm, importer, CoC number. The QR preview is still inactive.
5. Click **Sahkan**.

You do not need to type a note for approval. The system stores **Diluluskan**.

6. The badge becomes **Diluluskan**. The QR becomes **Aktif**. The decision card shows **Diluluskan · Diluluskan**.

Say: “Approval and QR activation are one step. The officer does not activate the QR separately.”

### If someone asks to see a rejection live

Do this only on a **second** new application, never on FAMA-2026-000123 or on the Pisang you just approved.

1. Create and submit another draft as the usahawan.
2. As FAMA, open it.
3. Type a note in **Catatan**, for example `Kuantiti tidak sepadan dengan invois.`
4. Click **Tolak**.
5. **Catatan** is required. Empty reject shows **Catatan penolakan diperlukan.**
6. Status becomes **Ditolak**. The QR stays **Belum Aktif**.

Otherwise, show the prepared rejection **FAMA-2026-000011** and move on.

## Part 8 — Public page after approval

1. In the incognito window, open the new QR again, or refresh it.
2. The inactive warning is gone. The page is **Profil Keluaran Pertanian** / **Jejak Keluaran Pertanian**.
3. On a desktop-width window you see the wide profile. On a phone you see the pamphlet layout. If you are on a laptop, narrow the window or use the phone to show both.
4. Point at:
   - product and variety (Pisang · Cavendish)
   - grade and origin Malaysia
   - company, farm, lot, destination
   - scan count
   - product details, export details
   - **Jejak pecahan**: one checkpoint, this batch, labelled **Kod ini**. There is no line yet, because the lot has not been split
   - certificates (MyGAP, HACCP, CoC, and the others on ABC Fruits)
   - nutrition, when the produce has it
   - agency strip
5. Click **BM**, **中文**, and **EN** at the top right. The same record switches language.
6. Say: “This is the page a buyer or consumer gets from the label. Private account data is not on it.”

## Part 9 — Download the QR and show the audit trail

1. Log out of FAMA and log in again as Ali (**USAHAWAN**).
2. Open **Kod QR**.
3. Open the new Pisang QR. Status is **Aktif**.
4. Click **Muat Turun QR**.
5. Leave **5 cm** and **PNG**, or switch format to **PDF**.
6. Click **Muat Turun QR** and let the file download.

Say: “The file is the same public URL. Size is for the label artwork in this prototype, not a call to a printer.”

Audit trail (type the address; it is not in the side menu):

- Usahawan: `/exporter/audit`
- Pegawai FAMA: `/fama/audit`

Actions you should be able to find after this demo include `QR_GENERATED`, `APPLICATION_SUBMITTED`, and `APPLICATION_APPROVED`. Older seed rows include the Durian submission and the MTS approval.

## Part 10 — Split the batch

The approved Pisang QR is one batch, held by ABC Fruits. This part breaks 100 kg off that batch and gives it to MTS Fruits. The rest stays with Ali.

Say: “The first QR is the whole shipment. A new code is created only for the chunk we record. We do not file a new application for each box.”

### Ali splits 100 kg to MTS

1. Still logged in as Ali (**USAHAWAN**).
2. Open **Pecahan**.
3. Open the new Pisang row. It says **Lot asal**, quantity **500 kg**, baki **500**.
4. Under **Jejak pecahan**, one green checkpoint says **Pengeksport · Kod ini** and ABC Fruits.
5. Under **Pecahkan kepada syarikat**:
   - **Kuantiti**: `100`
   - **Penerima**: **Pengeksport · MTS Fruits Sdn. Bhd.**
6. Click **Simpan pecahan**.
7. The page says **Lot dipecahkan.** Baki is **400**. A second checkpoint appears for MTS Fruits, **100 kg**, with **Muat turun** and **Jejak awam**.

Both companies show **Pengeksport** on this database. Ali and Siti were registered before chain roles were stored. A new registration can choose **Pembekal**, **Pengeksport**, **Pemasar**, **Peruncit**, or **Lain-lain** with a name such as Pengumpul. There is no required order of those roles.

Do not click **Jual semua baki**. That marks this whole QR sold and blocks further splits. **Jual pek** is the other sale: it creates a consumer QR for the quantity you type, with no login on that code.

### Siti sees only the chunk she holds

1. Log out. Log in as Siti (**USAHAWAN**): `siti@mtsfruits.example` / `Exporter123!`.
2. Open **Pecahan**.
3. Open the new row. It says **Dari Pengeksport · ABC Fruits Sdn. Bhd.**, baki **100 / 100 kg**.
4. **Jejak pecahan** shows two stops: ABC Fruits (the batch), then MTS Fruits marked **Kod ini**.

Say: “Siti can split only this 100 kg. She cannot split Ali’s remaining 400 kg.”

### Public checkpoint line

1. In the incognito window, open **Jejak awam** from Siti’s screen, or type the child QR (the new code, not the original Pisang QR).
2. **Jejak pecahan** is a line of two stops:
   - gold ring: ABC Fruits, 500 kg (the batch)
   - green dot **Kod ini**: MTS Fruits, 100 kg (this label)
3. Refresh the original Pisang QR. It still shows one checkpoint, ABC Fruits as **Kod ini**. The public page does not list who received the chunks. That list is on **Pecahan** for the holder, and on **Pecahan lot** for FAMA.

### FAMA sees the tree

1. Log in as Pegawai FAMA.
2. Open **Kelulusan QR** and the Pisang application you approved.
3. **Pecahan lot** uses the same line: the batch, then the 100 kg chunk, each with **baki**.

Say: “FAMA can see where the batch went. The officer does not split it for the holder.”

The audit action for the split is `LOT_SPLIT`, on `/fama/audit` or `/exporter/audit`.

## Optional — show registration without creating an account

Do this only if the room asks how someone joins. Stop before the password step.

### Usahawan lookup (mock DagangNet)

1. Log out.
2. On **Log Masuk**, click **Daftar**.
3. **Nombor Akaun**: `H0B00001`
4. Click **Seterusnya**.
5. Step 2 shows ABC Fruits Sdn. Bhd., `abcfruits@gmail.com`, status **Aktif**.
6. Stop. Do not continue to name and password. That company already has Ali’s login. If you do continue, step 3 asks **Peranan dalam rantaian**. That stores the company’s layer for lot splits. Leave it unless the room asks.
7. Go back and try `H0B99999`. The page says **Tiada rekod dijumpai**.
8. To show several DagangNet accounts on one new login, add `H0B00003`, click **Tambah**, add `H0B00004`, then click **Seterusnya**. Step 2 lists both companies. Finishing the password step writes a usahawan on the shared database. The login email is the first company’s email (`demo.utara@example.com` if Utara was added first). After login, **Syarikat aktif** switches the company. Skip this unless the room asks.

### Pegawai FAMA lookup (mock iFAMA)

1. From login, click **Daftar FAMA**.
2. **Nombor Kad Pengenalan**: `770101145533`
3. Click **Seterusnya**.
4. Step 2 shows Ali bin Abu Ghani, `aliabu@fama.gov.my`, jawatan **Pengarah Kanan**.
5. Stop. Do not set a password.
6. A second mock officer exists, Noraini binti Hassan, IC `850909105544`. Do not finish her registration on the shared demo database.
7. Try IC `000000000000` to show **Tiada rekod dijumpai**.

## Optional — FAMA registers a vendor and activates a QR immediately

This path is for an operations question (“what if FAMA captures the vendor at an event?”). It **writes a new company and an active QR** into the shared database. Skip it unless that question comes up.

1. Log in as Pegawai FAMA.
2. Open **Maklumat Syarikat**.
3. Search `ABC` or `AB34567` and open ABC Fruits if you only want to show the company file. Do not change DagangNet name or registration number.
4. For the live ops path, click **Daftar Vendor**.
5. The subtitle says no usahawan account is created.
6. Save a clearly fake vendor, for example registration `DEMO-MAHA-01`, name `Vendor Demo MAHA`.
7. Open that vendor and click **Cipta QR**.
8. The subtitle says the QR is activated immediately.
9. Fill a short export record and click **Cipta dan Aktifkan QR**.
10. Open the new public URL. It is already **Aktif**, because this path approves in one action.

Say: “This is an operations shortcut. The normal path is still usahawan submission, then FAMA approval.”

## 20-minute version

If the room is short on time, do only this:

1. Log in as Ali. Show **Utama** for 1 minute.
2. Open **Permohonan** and show Durian (Dalam Semakan), Mangga Harumanis (Diluluskan), and Mangga Chokanan (Ditolak). Do not change them.
3. Incognito: `/trace/GPL-QR-000123` (**QR Belum Diaktifkan**), then `/trace/GPL-QR-000015` (active Mangga page). Switch **EN** once.
4. Log in as FAMA. Show **Utama FAMA**, then open Durian under **Kelulusan QR** and stop on the summary. Do not click **Sahkan**.
5. As Ali, open **Kod QR**, open **GPL-QR-000015**, and download a PNG.

Skip registration, the lot split, and vendor capture. If someone asks where a box goes after approval, open **Pecahan** on Ali’s approved Mangga (**GPL-QR-000015**) and show the single checkpoint. Do not click **Simpan pecahan**.

## If something goes wrong

| What you see | What to do |
|---|---|
| Emel atau kata laluan tidak sah. | Check the email and password in the table above. |
| Peranan yang dipilih tidak sepadan dengan akaun ini. | Ali is **USAHAWAN**. The FAMA officer is **FAMA**. |
| Tiada rekod dijumpai on registration | Use `H0B00001` or IC `770101145533`. Any other value is the “not found” case, except Noraini’s IC `850909105544`. |
| Sahkan / Tolak buttons are missing | The application is already **Diluluskan** or **Ditolak**. Open the Pisang draft you submitted, or the prepared Durian only if you have decided to consume that record. |
| Catatan penolakan diperlukan. | Type a reason, then click **Tolak** again. |
| Public page has no product after you clicked Sahkan | Refresh the incognito tab. Confirm the URL is the new QR, not GPL-QR-000123. |
| You approved FAMA-2026-000123 by mistake | Tell the room that record is now the live approved example. Use GPL-QR-000011 for the inactive story, and create a new draft if you still need **Dalam Semakan**. Do not wipe the database. |
| Jumlah pecahan melebihi baki kuantiti | The chunks add up to more than the remaining kg. Lower the quantity and click **Simpan pecahan** again. |
| Hanya pemegang lot semasa boleh memecahkan atau menjual lot ini | You are logged in as the other company. Open **Pecahan** as the holder of that row. |
| Lot belum aktif | That QR is not approved yet. Split the Pisang only after **Sahkan**. |

## Rehearse tonight

- [ ] Log in as Ali and as the FAMA officer, then log out.
- [ ] Open `/trace/GPL-QR-000015`, `/trace/GPL-QR-000123`, and `/trace/GPL-QR-999999` in a private window.
- [ ] Create one Pisang application, submit it, approve it, refresh the public page, and download the PNG. Then split 100 kg to MTS Fruits, open the child public page, and open **Pecahan lot** as FAMA. Tell the audience tomorrow’s Pisang is a second run, or delete nothing and just use a different variety such as `Berangan`.
- [ ] Confirm the projector can show the left menu. If the window is narrow, use the bottom menu (usahawan) or the header menu (FAMA).
- [ ] Keep this file open on a second screen.
