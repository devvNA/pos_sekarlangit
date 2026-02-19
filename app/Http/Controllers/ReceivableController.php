<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Receivable;
use App\Models\ReceivablePayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReceivableController extends Controller
{
    public function index(): View
    {
        $receivables = Receivable::with(['customer', 'payments'])
            ->latest()
            ->get();

        $customers = Customer::orderBy('name')->get();

        return view('receivables.index', compact('receivables', 'customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id'   => 'nullable|exists:customers,id',
            'customer_name' => 'required|string|max:255',
            'total'         => 'required|numeric|min:0',
            'due_date'      => 'nullable|date',
        ]);

        // Normalize currency input
        $validated = array_merge($validated, $this->normalizeCurrency($request, ['total']));

        // Set remaining sama dengan total saat create
        $validated['remaining'] = $validated['total'];
        $validated['status']    = 'belum_lunas';

        Receivable::create($validated);

        return redirect()->route('receivables.index')
            ->with('success', 'Piutang berhasil ditambahkan.');
    }

    public function update(Request $request, Receivable $receivable): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id'   => 'nullable|exists:customers,id',
            'customer_name' => 'required|string|max:255',
            'total'         => 'required|numeric|min:0',
            'due_date'      => 'nullable|date',
        ]);

        // Normalize currency input
        $validated = array_merge($validated, $this->normalizeCurrency($request, ['total']));

        // Hitung ulang remaining berdasarkan total baru dan payments yang ada
        $totalPaid              = $receivable->payments()->sum('amount');
        $validated['remaining'] = $validated['total'] - $totalPaid;

        // Update status
        if ($validated['remaining'] <= 0) {
            $validated['status']    = 'lunas';
            $validated['remaining'] = 0;
        } else {
            $validated['status'] = 'belum_lunas';
        }

        $receivable->update($validated);

        return redirect()->route('receivables.index')
            ->with('success', 'Piutang berhasil diperbarui.');
    }

    public function destroy(Receivable $receivable): RedirectResponse
    {
        $paymentCount = $receivable->payments()->count();

        if ($paymentCount > 0) {
            return redirect()->route('receivables.index')
                ->with('error', "Tidak dapat menghapus piutang karena sudah ada {$paymentCount} pembayaran tercatat.");
        }

        $receivable->delete();

        return redirect()->route('receivables.index')
            ->with('success', 'Piutang berhasil dihapus.');
    }

    public function addPayment(Request $request, Receivable $receivable): RedirectResponse
    {
        $validated = $request->validate([
            'amount'  => 'required|numeric|min:0',
            'paid_at' => 'required|date',
            'note'    => 'nullable|string|max:500',
        ]);

        // Normalize currency input
        $validated = array_merge($validated, $this->normalizeCurrency($request, ['amount']));

        // Validasi: amount tidak boleh lebih besar dari remaining
        if ($validated['amount'] > $receivable->remaining) {
            return redirect()->route('receivables.index')
                ->with('error', 'Jumlah pembayaran tidak boleh lebih besar dari sisa piutang.');
        }

        // Create payment record
        ReceivablePayment::create([
            'receivable_id' => $receivable->id,
            'paid_at'       => $validated['paid_at'],
            'amount'        => $validated['amount'],
            'note'          => $validated['note'],
        ]);

        // Update receivable remaining dan status
        $newRemaining = $receivable->remaining - $validated['amount'];
        $newStatus    = $newRemaining <= 0 ? 'lunas' : 'belum_lunas';

        $receivable->update([
            'remaining' => max(0, $newRemaining),
            'status'    => $newStatus,
        ]);

        return redirect()->route('receivables.index')
            ->with('success', 'Pembayaran berhasil dicatat.');
    }

    /**
     * Helper function to normalize currency input (remove formatting)
     */
    private function normalizeCurrency(Request $request, array $fields): array
    {
        $normalized = [];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $value = $request->input($field);
                // Remove "Rp", ".", ",", and spaces
                $cleaned            = preg_replace('/[^\d]/', '', $value);
                $normalized[$field] = $cleaned ?: 0;
            }
        }
        return $normalized;
    }
}
