<?php
namespace App\Http\Controllers;

use App\Models\CashLedgerEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashBookController extends Controller
{
    public function index(): View
    {
        $entries = CashLedgerEntry::orderBy('occurred_at', 'desc')->get();

        // Calculate balance
        $cashIn  = CashLedgerEntry::where('type', 'in')->sum('amount');
        $cashOut = CashLedgerEntry::where('type', 'out')->sum('amount');
        $balance = $cashIn - $cashOut;

        return view('cash-book.index', compact('entries', 'cashIn', 'cashOut', 'balance'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type'        => 'required|in:in,out',
            'amount'      => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'occurred_at' => 'required|date',
            'reference'   => 'nullable|string|max:100',
        ]);

        // Normalize currency input
        $validated = array_merge($validated, $this->normalizeCurrency($request, ['amount']));

        CashLedgerEntry::create($validated);

        $typeText = $validated['type'] === 'in' ? 'Pemasukan' : 'Pengeluaran';
        return redirect()->route('cash.index')
            ->with('success', "{$typeText} berhasil dicatat.");
    }

    public function update(Request $request, CashLedgerEntry $cashLedgerEntry): RedirectResponse
    {
        $validated = $request->validate([
            'type'        => 'required|in:in,out',
            'amount'      => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'occurred_at' => 'required|date',
            'reference'   => 'nullable|string|max:100',
        ]);

        // Normalize currency input
        $validated = array_merge($validated, $this->normalizeCurrency($request, ['amount']));

        $cashLedgerEntry->update($validated);

        return redirect()->route('cash.index')
            ->with('success', 'Catatan kas berhasil diperbarui.');
    }

    public function destroy(CashLedgerEntry $cashLedgerEntry): RedirectResponse
    {
        $cashLedgerEntry->delete();

        return redirect()->route('cash.index')
            ->with('success', 'Catatan kas berhasil dihapus.');
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
