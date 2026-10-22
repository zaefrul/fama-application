<?php

namespace App\Http\Controllers\Exporter;

use App\Domain\PartyType;
use App\Domain\QrStatus;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\QrCode;
use App\Services\LotSplitService;
use App\Services\QrImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class LotController extends Controller
{
    public function __construct(private readonly LotSplitService $lots) {}

    public function index(Request $request): View
    {
        $companyId = $request->user()->company_id;
        $lots = QrCode::query()
            ->with(['holder', 'parent.holder', 'rootApplication.produceType'])
            ->where('holder_company_id', $companyId)
            ->orderByDesc('generated_at')
            ->get();
        $incoming = QrCode::query()
            ->with(['parent.holder', 'rootApplication.produceType'])
            ->where('pending_holder_company_id', $companyId)
            ->whereNull('holder_company_id')
            ->where('status', QrStatus::GeneratedInactive)
            ->orderByDesc('generated_at')
            ->get();

        return view('exporter.lots.index', [
            'lots' => $lots,
            'incoming' => $incoming,
        ]);
    }

    public function show(string $id, Request $request, QrImageService $qrImage): View
    {
        $lot = $this->heldLot($id, $request);
        $lot->load([
            'holder',
            'children.holder',
            'children.pendingHolder',
            'parent.holder',
            'rootApplication.produceType',
        ]);

        $rootId = $lot->root_application_id ?: $lot->application_id;
        $root = $rootId ? QrCode::treeForApplication($rootId) : null;
        $source = $lot->holder?->chainParty() ?? PartyType::Exporter;

        return view('exporter.lots.show', [
            'lot' => $lot,
            'lineage' => $root ? collect([$root]) : $lot->lineage(),
            'companies' => Company::query()
                ->where('id', '!=', $lot->holder_company_id)
                ->orderBy('name')
                ->get()
                ->filter(fn (Company $company) => $source->canPassTo($company->chainParty()))
                ->sortBy(fn (Company $company) => sprintf('%d-%s', $company->chainParty()->rank(), $company->name))
                ->values(),
            'publicUrl' => $qrImage->traceUrl($lot->qr_code),
            'unit' => $lot->rootApplication?->quantity_unit ?? $lot->application?->quantity_unit ?? '',
        ]);
    }

    public function split(string $id, Request $request): RedirectResponse
    {
        $lot = $this->heldLot($id, $request);
        $chunks = [];
        foreach ((array) $request->input('chunks', []) as $row) {
            if (! is_array($row)) {
                continue;
            }
            $quantity = (int) ($row['quantity'] ?? 0);
            $recipient = trim((string) ($row['recipient_company_id'] ?? ''));
            if ($quantity === 0 && $recipient === '') {
                continue;
            }
            $chunks[] = [
                'quantity' => $quantity,
                'recipient_company_id' => $recipient,
            ];
        }

        try {
            $this->lots->split($lot->id, $request->user(), $chunks);
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('exporter.lots.show', $lot)->with('status', 'Lot dipecahkan.');
    }

    public function sell(string $id, Request $request): RedirectResponse
    {
        $lot = $this->heldLot($id, $request);

        try {
            if ($request->boolean('entire')) {
                $this->lots->sellEntire($lot->id, $request->user());
            } else {
                $quantities = array_map(
                    fn ($value) => (int) $value,
                    array_values((array) $request->input('quantities', [])),
                );
                $this->lots->sellPortions($lot->id, $request->user(), $quantities);
            }
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('exporter.lots.show', $lot)->with('status', 'Jualan direkodkan.');
    }

    public function accept(string $id, Request $request): RedirectResponse
    {
        $lot = QrCode::query()->findOrFail($id);
        abort_unless($lot->pending_holder_company_id === $request->user()->company_id, 404);

        try {
            $this->lots->acceptIfRecipient($lot, $request->user());
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($lot->fresh()->status !== QrStatus::Active) {
            return back()->with('error', 'Lot ini tidak boleh diterima.');
        }

        return redirect()->route('exporter.lots.show', $lot)->with('status', 'Lot diterima. QR kini Aktif.');
    }

    private function heldLot(string $id, Request $request): QrCode
    {
        $lot = QrCode::query()->findOrFail($id);
        abort_unless($lot->holder_company_id === $request->user()->company_id, 404);

        return $lot;
    }
}
