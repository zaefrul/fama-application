<?php

namespace App\Services;

use App\Domain\ApplicationStatus;
use App\Domain\Ids;
use App\Domain\QrStatus;
use App\Domain\Role;
use App\Domain\Transitions;
use App\Models\AppNotification;
use App\Models\Approval;
use App\Models\AuditLog;
use App\Models\Certificate;
use App\Models\Company;
use App\Models\CompanyProduce;
use App\Models\ExportApplication;
use App\Models\GalleryItem;
use App\Models\ProduceType;
use App\Models\QrAccess;
use App\Models\QrCode;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use RuntimeException;

class JejakService
{
    public static function certificatePreviewPath(string $type, ?string $documentPath = null): string
    {
        $path = (string) $documentPath;
        $isUserUpload = $path !== ''
            && ! str_contains($path, '/placeholders/')
            && ! str_contains($path, '/certificates/sijil-');

        if ($isUserUpload) {
            return $path;
        }

        return match (strtoupper(trim($type))) {
            'MYGAP' => '/certificates/sijil-mygap-demo.svg',
            'HACCP' => '/certificates/sijil-haccp-demo.svg',
            'COC' => '/certificates/sijil-coc-demo.svg',
            'FITOSANITASI', 'PHYTOSANITARY' => '/certificates/sijil-fitosanitasi-demo.jpg',
            'HALAL' => '/certificates/sijil-halal-demo.svg',
            'ISO 22000', 'ISO22000', 'ISO_22000' => '/certificates/sijil-iso22000-demo.svg',
            default => $path !== '' ? $path : '/placeholders/certificate-coc.svg',
        };
    }

    public static function produceImagePath(?string $produceTypeId): ?string
    {
        return match ($produceTypeId) {
            'pt_durian' => '/products/produce-durian-demo-01.jpg',
            'pt_tembikai' => '/products/produce-tembikai-demo-01.jpg',
            'pt_mangga' => '/products/produce-mangga-demo-01.jpg',
            'pt_nangka' => '/products/produce-nangka-demo-01.jpg',
            default => null,
        };
    }

    /**
     * @return array<string, list<array{name: string, amount: string, dailyPercent: string}>>
     */
    public static function nutritionByProduce(): array
    {
        return [
            'pt_durian' => [
                ['name' => 'Kalori', 'amount' => '147 kcal', 'dailyPercent' => '7%'],
                ['name' => 'Lemak jumlah', 'amount' => '5.3 g', 'dailyPercent' => '8%'],
                ['name' => 'Kolesterol', 'amount' => '0 mg', 'dailyPercent' => '0%'],
                ['name' => 'Natrium', 'amount' => '1 mg', 'dailyPercent' => '0%'],
                ['name' => 'Kalium', 'amount' => '436 mg', 'dailyPercent' => '12%'],
                ['name' => 'Karbohidrat', 'amount' => '27.1 g', 'dailyPercent' => '9%'],
                ['name' => 'Serat', 'amount' => '3.8 g', 'dailyPercent' => '15%'],
                ['name' => 'Protein', 'amount' => '1.5 g', 'dailyPercent' => '3%'],
                ['name' => 'Vitamin C', 'amount' => '19.7 mg', 'dailyPercent' => '33%'],
            ],
        ];
    }

    public function writeAudit(User $actor, string $action, string $objectType, string $objectId, ?string $remarks = null): void
    {
        AuditLog::query()->create([
            'id' => Ids::create('audit'),
            'actor_user_id' => $actor->id,
            'actor_role' => $actor->role->value,
            'action' => $action,
            'object_type' => $objectType,
            'object_id' => $objectId,
            'before_json' => null,
            'after_json' => null,
            'remarks' => $remarks,
        ]);
    }

    public function createExporterUser(array $input): User
    {
        return User::query()->create([
            'id' => Ids::create('user'),
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'role' => Role::Exporter,
            'identity_reference' => $input['identity_reference'],
            'status' => 'ACTIVE',
            'company_id' => $input['company_id'],
        ]);
    }

    public function createFamaUser(array $input): User
    {
        return User::query()->create([
            'id' => Ids::create('user'),
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'role' => Role::FamaOfficer,
            'identity_reference' => $input['identity_reference'],
            'status' => 'ACTIVE',
            'company_id' => null,
        ]);
    }

    public function createCompany(array $input, User $actor): Company
    {
        $registrationNo = trim($input['registration_no'] ?? '');
        $name = trim($input['name'] ?? '');
        if ($registrationNo === '' || $name === '') {
            throw new RuntimeException('Nama dan no. pendaftaran diperlukan');
        }

        $externalAccountNo = $this->famaAccountNo($registrationNo);
        $duplicate = Company::query()
            ->where('registration_no', $registrationNo)
            ->orWhere('external_account_no', $externalAccountNo)
            ->exists();
        if ($duplicate) {
            throw new RuntimeException('No. pendaftaran atau akaun luaran telah wujud');
        }

        $company = Company::query()->create([
            'id' => Ids::create('co'),
            'registration_no' => $registrationNo,
            'external_account_no' => $externalAccountNo,
            'name' => $name,
            'email' => $input['email'] ?? '',
            'phone' => $input['phone'] ?? '',
            'address' => $input['address'] ?? '',
            'state' => $input['state'] ?? '',
            'district' => $input['district'] ?? '',
            'postcode' => $input['postcode'] ?? '',
            'website' => $input['website'] ?? '',
            'logo_path' => null,
            'external_source' => 'FAMA',
            'external_status' => 'Aktif',
        ]);

        $this->writeAudit($actor, 'COMPANY_CREATED', 'Company', $company->id);

        return $company;
    }

    public function updateCompany(string $id, array $patch): Company
    {
        $company = Company::query()->findOrFail($id);
        $company->fill([
            'address' => $patch['address'] ?? $company->address,
            'state' => $patch['state'] ?? $company->state,
            'district' => $patch['district'] ?? $company->district,
            'postcode' => $patch['postcode'] ?? $company->postcode,
            'phone' => $patch['phone'] ?? $company->phone,
            'email' => $patch['email'] ?? $company->email,
            'website' => $patch['website'] ?? $company->website,
            'logo_path' => $patch['logo_path'] ?? $company->logo_path,
        ]);
        $company->save();

        return $company;
    }

    public function updateManagedCompany(string $id, array $patch, User $actor): Company
    {
        $current = Company::query()->find($id);
        if (! $current) {
            throw new RuntimeException('Syarikat tidak dijumpai');
        }

        $isFama = $current->external_source === 'FAMA';
        $nextRegistration = $isFama && array_key_exists('registration_no', $patch)
            ? trim((string) $patch['registration_no'])
            : $current->registration_no;
        $nextName = $isFama && array_key_exists('name', $patch)
            ? trim((string) $patch['name'])
            : $current->name;
        if ($nextRegistration === '' || $nextName === '') {
            throw new RuntimeException('Nama dan no. pendaftaran diperlukan');
        }

        $nextAccount = $isFama ? $this->famaAccountNo($nextRegistration) : $current->external_account_no;
        if ($isFama && ($nextRegistration !== $current->registration_no || $nextAccount !== $current->external_account_no)) {
            $duplicate = Company::query()
                ->where('id', '!=', $id)
                ->where(function ($query) use ($nextRegistration, $nextAccount) {
                    $query->where('registration_no', $nextRegistration)
                        ->orWhere('external_account_no', $nextAccount);
                })
                ->exists();
            if ($duplicate) {
                throw new RuntimeException('No. pendaftaran atau akaun luaran telah wujud');
            }
        }

        $current->fill([
            'address' => $patch['address'] ?? $current->address,
            'state' => $patch['state'] ?? $current->state,
            'district' => $patch['district'] ?? $current->district,
            'postcode' => $patch['postcode'] ?? $current->postcode,
            'phone' => $patch['phone'] ?? $current->phone,
            'email' => $patch['email'] ?? $current->email,
            'website' => $patch['website'] ?? $current->website,
            'logo_path' => $patch['logo_path'] ?? $current->logo_path,
        ]);
        if ($isFama) {
            $current->name = $nextName;
            $current->registration_no = $nextRegistration;
            $current->external_account_no = $nextAccount;
        }
        $current->save();
        $this->writeAudit($actor, 'COMPANY_UPDATED', 'Company', $id);

        return $current;
    }

    public function findOrCreateProduceType(string $name): ProduceType
    {
        $name = trim(preg_replace('/\s+/u', ' ', $name) ?? '');
        if ($name === '') {
            throw new RuntimeException('Jenis Keluaran Pertanian diperlukan');
        }
        if (mb_strlen($name) > 80) {
            throw new RuntimeException('Jenis Keluaran Pertanian terlalu panjang');
        }

        $existing = ProduceType::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();
        if ($existing) {
            return $existing;
        }

        return ProduceType::query()->create([
            'id' => Ids::create('pt'),
            'name' => $name,
        ]);
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function withResolvedProduceType(array $input, bool $allowEmpty = false): array
    {
        $newName = trim((string) ($input['new_produce_name'] ?? ''));
        $produceTypeId = trim((string) ($input['produce_type_id'] ?? ''));
        unset($input['new_produce_name']);

        if ($newName !== '') {
            $input['produce_type_id'] = $this->findOrCreateProduceType($newName)->id;

            return $input;
        }

        if ($produceTypeId !== '') {
            if (! ProduceType::query()->where('id', $produceTypeId)->exists()) {
                throw new RuntimeException('Jenis Keluaran Pertanian tidak sah');
            }
            $input['produce_type_id'] = $produceTypeId;

            return $input;
        }

        if ($allowEmpty) {
            unset($input['produce_type_id']);

            return $input;
        }

        throw new RuntimeException('Sila pilih atau tambah Jenis Keluaran Pertanian');
    }

    public function addCompanyProduce(string $companyId, string $produceTypeId, ?string $variety = null): CompanyProduce
    {
        $existing = CompanyProduce::query()
            ->where('company_id', $companyId)
            ->where('produce_type_id', $produceTypeId)
            ->first();
        if ($existing) {
            return $existing;
        }

        return CompanyProduce::query()->create([
            'id' => Ids::create('cp'),
            'company_id' => $companyId,
            'produce_type_id' => $produceTypeId,
            'variety' => $variety,
            'active' => true,
        ]);
    }

    public function addCertificate(array $input): Certificate
    {
        return Certificate::query()->create([
            'id' => Ids::create('cert'),
            'company_id' => $input['company_id'],
            'type' => $input['type'],
            'certificate_no' => $input['certificate_no'],
            'document_path' => $input['document_path'],
            'issue_date' => $input['issue_date'] ?: now()->toDateString(),
            'expiry_date' => $input['expiry_date'] ?: null,
            'status' => $input['status'] ?? 'ACTIVE',
        ]);
    }

    public function addGalleryItem(array $input): GalleryItem
    {
        return GalleryItem::query()->create([
            'id' => Ids::create('gal'),
            'company_id' => $input['company_id'],
            'category' => $input['category'],
            'description' => $input['description'],
            'file_path' => $input['file_path'],
            'uploaded_by' => $input['uploaded_by'],
            'uploaded_at' => $input['uploaded_at'] ?? now(),
        ]);
    }

    public function createApplication(array $input): ExportApplication
    {
        $input = $this->withResolvedProduceType($input);
        $existing = ExportApplication::query()->pluck('application_no')->all();

        $application = ExportApplication::query()->create([
            'id' => Ids::create('app'),
            'application_no' => Ids::nextApplicationNo($existing),
            'company_id' => $input['company_id'],
            'produce_type_id' => $input['produce_type_id'],
            'variety' => $input['variety'],
            'grade' => $input['grade'],
            'size' => $input['size'],
            'quantity' => (int) $input['quantity'],
            'quantity_unit' => $input['quantity_unit'] ?? 'kg',
            'destination_country' => $input['destination_country'],
            'coc_certificate_id' => $input['coc_certificate_id'] ?: null,
            'coc_number' => $input['coc_number'] ?? '',
            'export_date' => $input['export_date'] ?: null,
            'lot_no' => $input['lot_no'] ?? null,
            'farm_location' => $input['farm_location'] ?? null,
            'farm_lat' => $input['farm_lat'] ?? null,
            'farm_lng' => $input['farm_lng'] ?? null,
            'display_image_path' => $input['display_image_path'] ?? null,
            'farm_name' => $input['farm_name'],
            'importer_name' => $input['importer_name'],
            'importer_address' => $input['importer_address'],
            'status' => ApplicationStatus::Draft,
        ]);
        $this->addCompanyProduce($application->company_id, $application->produce_type_id);

        return $application;
    }

    public function updateApplication(string $id, array $patch): ExportApplication
    {
        $current = ExportApplication::query()->find($id);
        if (! $current || $current->status !== ApplicationStatus::Draft) {
            throw new RuntimeException('Hanya draf boleh dikemaskini');
        }

        $patch = $this->withResolvedProduceType($patch, true);
        $this->fillApplication($current, $patch);
        $current->save();
        $this->addCompanyProduce($current->company_id, $current->produce_type_id);

        return $current;
    }

    public function updateManagedApplication(string $id, array $patch, User $actor): ExportApplication
    {
        $current = ExportApplication::query()->find($id);
        if (! $current) {
            throw new RuntimeException('Permohonan tidak dijumpai');
        }
        if ($current->status !== ApplicationStatus::Approved) {
            throw new RuntimeException('Hanya permohonan diluluskan boleh dikemaskini oleh FAMA');
        }

        $patch = $this->withResolvedProduceType($patch, true);
        $this->fillApplication($current, $patch);
        $current->save();
        $this->addCompanyProduce($current->company_id, $current->produce_type_id);
        $this->writeAudit($actor, 'APPLICATION_UPDATED', 'ExportApplication', $id);

        return $current;
    }

    public function generateQr(string $applicationId, User $actor): QrCode
    {
        $existing = QrCode::query()->where('application_id', $applicationId)->first();
        if ($existing) {
            return $existing;
        }

        $code = Ids::nextQrCode(QrCode::query()->pluck('qr_code')->all());
        $row = QrCode::query()->create([
            'id' => Ids::create('qr'),
            'qr_code' => $code,
            'application_id' => $applicationId,
            'public_slug' => $code,
            'status' => QrStatus::GeneratedInactive,
            'generated_at' => now(),
        ]);
        $this->writeAudit($actor, 'QR_GENERATED', 'QRCode', $row->id);

        return $row;
    }

    public function submitApplication(string $id, User $actor): ExportApplication
    {
        $current = ExportApplication::query()->find($id);
        if (! $current) {
            throw new RuntimeException('Permohonan tidak dijumpai');
        }
        Transitions::assertApplicationTransition($current->status->value, ApplicationStatus::Submitted->value);
        $current->status = ApplicationStatus::Submitted;
        $current->submitted_at = now();
        $current->save();
        $this->writeAudit($actor, 'APPLICATION_SUBMITTED', 'ExportApplication', $id);

        return $current;
    }

    public function startReview(string $id, User $actor): ExportApplication
    {
        $current = ExportApplication::query()->find($id);
        if (! $current) {
            throw new RuntimeException('Permohonan tidak dijumpai');
        }
        if ($current->status === ApplicationStatus::UnderReview) {
            return $current;
        }
        Transitions::assertApplicationTransition($current->status->value, ApplicationStatus::UnderReview->value);
        $current->status = ApplicationStatus::UnderReview;
        $current->save();
        $this->writeAudit($actor, 'APPLICATION_UNDER_REVIEW', 'ExportApplication', $id);

        return $current;
    }

    public function approveApplication(string $id, User $actor, string $remarks): ExportApplication
    {
        $current = ExportApplication::query()->find($id);
        if (! $current) {
            throw new RuntimeException('Permohonan tidak dijumpai');
        }
        $status = $current->status->value;
        if ($status === ApplicationStatus::Submitted->value) {
            $status = ApplicationStatus::UnderReview->value;
        }
        Transitions::assertApplicationTransition($status, ApplicationStatus::Approved->value);
        $current->status = ApplicationStatus::Approved;
        $current->reviewed_at = now();
        $current->save();

        Approval::query()->create([
            'id' => Ids::create('appr'),
            'application_id' => $id,
            'officer_user_id' => $actor->id,
            'decision' => 'APPROVED',
            'remarks' => $remarks,
            'decided_at' => now(),
        ]);

        $qr = QrCode::query()->where('application_id', $id)->first();
        if ($qr) {
            Transitions::assertQrTransition($qr->status->value, QrStatus::Active->value);
            $qr->status = QrStatus::Active;
            $qr->activated_at = now();
            $qr->save();
        }

        $this->writeAudit($actor, 'APPLICATION_APPROVED', 'ExportApplication', $id, $remarks);

        return $current;
    }

    public function rejectApplication(string $id, User $actor, string $remarks): ExportApplication
    {
        if (trim($remarks) === '') {
            throw new RuntimeException('Catatan penolakan diperlukan');
        }
        $current = ExportApplication::query()->find($id);
        if (! $current) {
            throw new RuntimeException('Permohonan tidak dijumpai');
        }
        $status = $current->status->value;
        if ($status === ApplicationStatus::Submitted->value) {
            $status = ApplicationStatus::UnderReview->value;
        }
        Transitions::assertApplicationTransition($status, ApplicationStatus::Rejected->value);
        $current->status = ApplicationStatus::Rejected;
        $current->reviewed_at = now();
        $current->save();

        Approval::query()->create([
            'id' => Ids::create('appr'),
            'application_id' => $id,
            'officer_user_id' => $actor->id,
            'decision' => 'REJECTED',
            'remarks' => $remarks,
            'decided_at' => now(),
        ]);
        $this->writeAudit($actor, 'APPLICATION_REJECTED', 'ExportApplication', $id, $remarks);

        return $current;
    }

    /**
     * @return array{application: ExportApplication, qr: QrCode}
     */
    public function createAndActivateQr(string $companyId, array $input, User $actor): array
    {
        $application = $this->createApplication([...$input, 'company_id' => $companyId]);
        $generated = $this->generateQr($application->id, $actor);
        $this->submitApplication($application->id, $actor);
        $this->startReview($application->id, $actor);
        $approved = $this->approveApplication($application->id, $actor, 'Dicipta dan diaktifkan oleh FAMA');
        $qr = QrCode::query()->find($generated->id);
        if (! $qr) {
            throw new RuntimeException('QR tidak dijana');
        }

        return ['application' => $approved, 'qr' => $qr];
    }

    public function listQrCodes(?string $companyId = null): Collection
    {
        $query = QrCode::query()->with('application.produceType', 'application.company')->withCount('accesses');
        if ($companyId) {
            $query->whereHas('application', fn ($q) => $q->where('company_id', $companyId));
        }

        return $query->orderByDesc('generated_at')->get();
    }

    public function getQrByCode(string $qrCode): ?QrCode
    {
        return QrCode::query()
            ->where('qr_code', $qrCode)
            ->orWhere('public_slug', $qrCode)
            ->first();
    }

    public function recordQrAccess(QrCode $qr): void
    {
        QrAccess::query()->create([
            'id' => Ids::create('acc'),
            'qr_id' => $qr->id,
            'qr_code' => $qr->qr_code,
            'accessed_at' => now(),
        ]);
    }

    public function listAudit(?string $companyId = null): Collection
    {
        $query = AuditLog::query()->orderByDesc('created_at');
        if (! $companyId) {
            return $query->get();
        }

        $ids = ExportApplication::query()->where('company_id', $companyId)->pluck('id');

        return $query
            ->where(function ($q) use ($ids, $companyId) {
                $q->whereIn('object_id', $ids)->orWhere('object_id', $companyId);
            })
            ->get();
    }

    /**
     * @return array{qrActive: int, qrInactive: int, totalApplications: int, approved: int, rejected: int}
     */
    public function dashboardExporter(string $companyId): array
    {
        $apps = ExportApplication::query()->where('company_id', $companyId)->get();
        $qrs = QrCode::query()->whereIn('application_id', $apps->pluck('id'))->get();

        return [
            'qrActive' => $qrs->where('status', QrStatus::Active)->count(),
            'qrInactive' => $qrs->where('status', QrStatus::GeneratedInactive)->count(),
            'totalApplications' => $apps->count(),
            'approved' => $apps->where('status', ApplicationStatus::Approved)->count(),
            'rejected' => $apps->where('status', ApplicationStatus::Rejected)->count(),
        ];
    }

    /**
     * @return array{
     *     activeCompanies: int,
     *     exporters: int,
     *     qrRequests: int,
     *     qrActive: int,
     *     qrInactive: int,
     *     approved: int,
     *     pending: int,
     *     rejected: int,
     *     uniqueFruits: int,
     *     uniqueDestinations: int,
     *     certificates: int,
     *     famaCompanies: int,
     *     dailyQr: list<array{day: string, label: string, active: int, inactive: int, activePercent: int, inactivePercent: int}>,
     *     topFruits: list<array{label: string, count: int, percent: int, color: int}>,
     *     topDestinations: list<array{label: string, count: int, percent: int, color: int}>,
     *     companiesByState: list<array{label: string, count: int, percent: int, color: int}>,
     *     statusMix: list<array{label: string, count: int, percent: int, tone: string}>,
     *     accessTotal: int,
     *     accessSevenDays: int,
     *     accessWeek: int,
     *     accessLastWeek: int,
     *     dailyAccess: list<array{day: string, label: string, count: int, percent: int}>,
     *     topQrAccess: list<array{qrCode: string, produce: string, company: string, count: int, percent: int}>
     * }
     */
    public function dashboardFama(): array
    {
        $qrs = QrCode::query()->get();
        $apps = ExportApplication::query()->get();
        $statusTotal = max(1, $apps->count());

        $statusMix = [];
        foreach (ApplicationStatus::cases() as $status) {
            $count = $apps->where('status', $status)->count();
            $statusMix[] = [
                'label' => $status->label(),
                'count' => $count,
                'percent' => (int) round(($count / $statusTotal) * 100),
                'tone' => $status->tone(),
            ];
        }

        return [
            'activeCompanies' => Company::query()->where('external_status', 'Aktif')->count(),
            'exporters' => User::query()->where('role', Role::Exporter)->count(),
            'qrRequests' => $apps->count(),
            'qrActive' => $qrs->where('status', QrStatus::Active)->count(),
            'qrInactive' => $qrs->where('status', QrStatus::GeneratedInactive)->count(),
            'approved' => $apps->where('status', ApplicationStatus::Approved)->count(),
            'pending' => $apps->filter(fn (ExportApplication $app) => in_array($app->status, [ApplicationStatus::Submitted, ApplicationStatus::UnderReview], true))->count(),
            'rejected' => $apps->where('status', ApplicationStatus::Rejected)->count(),
            'uniqueFruits' => (int) CompanyProduce::query()->distinct()->count('produce_type_id'),
            'uniqueDestinations' => $apps->pluck('destination_country')->filter()->unique()->count(),
            'certificates' => Certificate::query()->count(),
            'famaCompanies' => Company::query()->where('external_source', 'FAMA')->count(),
            'dailyQr' => $this->dailyQrGenerated(),
            'topFruits' => $this->rankNamedCounts($this->produceCounts($apps), 10),
            'topDestinations' => $this->rankNamedCounts(
                $apps->pluck('destination_country')->filter()->countBy()->all(),
                10,
            ),
            'companiesByState' => $this->rankNamedCounts(
                Company::query()->whereNotNull('state')->pluck('state')->filter()->countBy()->all(),
                10,
            ),
            'statusMix' => $statusMix,
            ...$this->qrAccessStats(),
        ];
    }

    /**
     * Prototype assumption (ADR-016): one public HTML view of a known QR = one imbasan.
     * Daily buckets use Asia/Kuala_Lumpur. Weeks start on Monday.
     *
     * @return array{
     *     accessTotal: int,
     *     accessSevenDays: int,
     *     accessWeek: int,
     *     accessLastWeek: int,
     *     dailyAccess: list<array{day: string, label: string, count: int, percent: int}>,
     *     topQrAccess: list<array{qrCode: string, produce: string, company: string, count: int, percent: int}>
     * }
     */
    private function qrAccessStats(): array
    {
        $zone = 'Asia/Kuala_Lumpur';
        $today = CarbonImmutable::now($zone)->startOfDay();
        $weekStart = $today->startOfWeek(CarbonImmutable::MONDAY);
        $lastWeekStart = $weekStart->subWeek();
        $sevenDaysAgo = $today->subDays(6);

        $accessTotal = QrAccess::query()->count();
        $accessWeek = QrAccess::query()->where('accessed_at', '>=', $weekStart->utc())->count();
        $accessLastWeek = QrAccess::query()
            ->where('accessed_at', '>=', $lastWeekStart->utc())
            ->where('accessed_at', '<', $weekStart->utc())
            ->count();

        $recent = QrAccess::query()
            ->where('accessed_at', '>=', $sevenDaysAgo->utc())
            ->get(['qr_id', 'qr_code', 'accessed_at']);

        $byDay = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $sevenDaysAgo->addDays($i);
            $byDay[$day->toDateString()] = 0;
        }
        foreach ($recent as $access) {
            $key = $access->accessed_at->timezone($zone)->toDateString();
            if (array_key_exists($key, $byDay)) {
                $byDay[$key]++;
            }
        }
        $maxDay = max([1, ...array_values($byDay)]);
        $dailyAccess = [];
        foreach ($byDay as $date => $count) {
            $day = CarbonImmutable::parse($date, $zone)->locale('ms');
            $dailyAccess[] = [
                'day' => $date,
                'label' => strtoupper($day->minDayName),
                'count' => $count,
                'percent' => (int) round(($count / $maxDay) * 100),
            ];
        }

        $topCounts = $recent->countBy('qr_id')->sortDesc()->take(3);
        $topQr = QrCode::query()
            ->with('application.produceType', 'application.company')
            ->whereIn('id', $topCounts->keys())
            ->get()
            ->keyBy('id');
        $maxTop = max([1, ...$topCounts->values()->all()]);
        $topQrAccess = [];
        foreach ($topCounts as $qrId => $count) {
            $qr = $topQr->get($qrId);
            $topQrAccess[] = [
                'qrCode' => $qr?->qr_code ?? (string) $qrId,
                'produce' => $qr?->application?->produceType?->name ?? '—',
                'company' => $qr?->application?->company?->name ?? '—',
                'count' => $count,
                'percent' => (int) round(($count / $maxTop) * 100),
            ];
        }

        return [
            'accessTotal' => $accessTotal,
            'accessSevenDays' => array_sum(array_column($dailyAccess, 'count')),
            'accessWeek' => $accessWeek,
            'accessLastWeek' => $accessLastWeek,
            'dailyAccess' => $dailyAccess,
            'topQrAccess' => $topQrAccess,
        ];
    }

    /**
     * Last 7 calendar days (Asia/Kuala_Lumpur) of QR generated_at, split by current status.
     *
     * @return list<array{day: string, label: string, active: int, inactive: int, activePercent: int, inactivePercent: int}>
     */
    private function dailyQrGenerated(): array
    {
        $zone = 'Asia/Kuala_Lumpur';
        $today = CarbonImmutable::now($zone)->startOfDay();
        $sevenDaysAgo = $today->subDays(6);
        $recent = QrCode::query()
            ->where('generated_at', '>=', $sevenDaysAgo->utc())
            ->get(['status', 'generated_at']);

        $byDay = [];
        for ($i = 0; $i < 7; $i++) {
            $byDay[$sevenDaysAgo->addDays($i)->toDateString()] = ['active' => 0, 'inactive' => 0];
        }
        foreach ($recent as $qr) {
            $key = $qr->generated_at?->timezone($zone)->toDateString();
            if (! $key || ! array_key_exists($key, $byDay)) {
                continue;
            }
            if ($qr->status === QrStatus::Active) {
                $byDay[$key]['active']++;
            } else {
                $byDay[$key]['inactive']++;
            }
        }

        $max = max([1, ...array_map(fn (array $row) => $row['active'] + $row['inactive'], $byDay)]);
        $dailyQr = [];
        foreach ($byDay as $date => $row) {
            $day = CarbonImmutable::parse($date, $zone)->locale('ms');
            $dailyQr[] = [
                'day' => $date,
                'label' => strtoupper($day->minDayName),
                'active' => $row['active'],
                'inactive' => $row['inactive'],
                'activePercent' => (int) round(($row['active'] / $max) * 100),
                'inactivePercent' => (int) round(($row['inactive'] / $max) * 100),
            ];
        }

        return $dailyQr;
    }

    /**
     * @param  Collection<int, ExportApplication>  $apps
     * @return array<string, int>
     */
    private function produceCounts(Collection $apps): array
    {
        $counts = $apps->pluck('produce_type_id')->filter()->countBy();
        $names = ProduceType::query()
            ->whereIn('id', $counts->keys())
            ->pluck('name', 'id');

        $named = [];
        foreach ($counts as $id => $count) {
            $named[(string) ($names[$id] ?? $id)] = (int) $count;
        }

        return $named;
    }

    /**
     * @param  array<string, int>  $counts
     * @return list<array{label: string, count: int, percent: int, color: int}>
     */
    private function rankNamedCounts(array $counts, int $limit): array
    {
        arsort($counts);
        $counts = array_slice($counts, 0, $limit, true);
        $max = max([1, ...array_values($counts)]);
        $items = [];
        $color = 1;
        foreach ($counts as $label => $count) {
            $items[] = [
                'label' => (string) $label,
                'count' => (int) $count,
                'percent' => (int) round(($count / $max) * 100),
                'color' => $color,
            ];
            $color = $color === 10 ? 1 : $color + 1;
        }

        return $items;
    }

    public function unreadNotificationCount(string $userId): int
    {
        return AppNotification::query()->where('user_id', $userId)->where('read', false)->count();
    }

    public function produceName(?string $produceTypeId): string
    {
        if (! $produceTypeId) {
            return '—';
        }

        return ProduceType::query()->find($produceTypeId)?->name ?? '—';
    }

    private function famaAccountNo(string $registrationNo): string
    {
        return 'FAMA-'.trim($registrationNo);
    }

    private function fillApplication(ExportApplication $application, array $patch): void
    {
        $application->fill([
            'produce_type_id' => $patch['produce_type_id'] ?? $application->produce_type_id,
            'variety' => $patch['variety'] ?? $application->variety,
            'grade' => $patch['grade'] ?? $application->grade,
            'size' => $patch['size'] ?? $application->size,
            'quantity' => array_key_exists('quantity', $patch) ? (int) $patch['quantity'] : $application->quantity,
            'quantity_unit' => $patch['quantity_unit'] ?? $application->quantity_unit,
            'destination_country' => $patch['destination_country'] ?? $application->destination_country,
            'coc_certificate_id' => array_key_exists('coc_certificate_id', $patch) ? ($patch['coc_certificate_id'] ?: null) : $application->coc_certificate_id,
            'coc_number' => $patch['coc_number'] ?? $application->coc_number,
            'export_date' => array_key_exists('export_date', $patch) ? ($patch['export_date'] ?: null) : $application->export_date,
            'lot_no' => array_key_exists('lot_no', $patch) ? ($patch['lot_no'] ?: null) : $application->lot_no,
            'farm_location' => array_key_exists('farm_location', $patch) ? ($patch['farm_location'] ?: null) : $application->farm_location,
            'farm_lat' => array_key_exists('farm_lat', $patch) ? $patch['farm_lat'] : $application->farm_lat,
            'farm_lng' => array_key_exists('farm_lng', $patch) ? $patch['farm_lng'] : $application->farm_lng,
            'display_image_path' => $patch['display_image_path'] ?? $application->display_image_path,
            'farm_name' => $patch['farm_name'] ?? $application->farm_name,
            'importer_name' => $patch['importer_name'] ?? $application->importer_name,
            'importer_address' => $patch['importer_address'] ?? $application->importer_address,
        ]);
    }
}
