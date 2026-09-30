<?php

namespace App\Http\Controllers\Exporter;

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

        return view('exporter.lots.index', [
            'lots' => $lots,
        ]);
    }

    public function show(string $id, Request $request, QrImageService $qrImage): View
    {
        $lot = $this->heldLot($id, $request);
        $lot->load([
            'holder',
            'children.holder',
            'parent.holder',
            'rootApplication.produceType',
        ]);

        return view('exporter.lots.show', [
            'lot' => $lot,
            'lineage' => $lot->lineage(),
            'companies' => Company::query()->orderBy('name')->get(),
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

    private function heldLot(string $id, Request $request): QrCode
    {
        $lot = QrCode::query()->findOrFail($id);
        abort_unless($lot->holder_company_id === $request->user()->company_id, 404);

        return $lot;
    }
}
