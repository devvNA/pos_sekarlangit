<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::query()->with('supplier');
        $search = $request->input('q');
        if ($search) {
            $query->where(static function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('inventory.index', [
            'products' => $products,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        $suppliers = Supplier::orderBy('name')->get();

        return view('inventory.create', [
            'suppliers' => $suppliers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'required|string|max:13|unique:products,barcode',
            'unit' => 'required|string|max:50',
            'price_buy' => 'required|numeric|min:0',
            'price_sell' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'active' => 'nullable|boolean',
        ]);

        $data['active'] = (bool) ($data['active'] ?? false);

        Product::create($data);

        return redirect()->route('inventory.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        $suppliers = Supplier::orderBy('name')->get();

        return view('inventory.edit', [
            'product' => $product,
            'suppliers' => $suppliers,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'required|string|max:13|unique:products,barcode,'.$product->id,
            'unit' => 'required|string|max:50',
            'price_buy' => 'required|numeric|min:0',
            'price_sell' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'active' => 'nullable|boolean',
        ]);

        $data['active'] = (bool) ($data['active'] ?? false);
        $product->update($data);

        return redirect()->route('inventory.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        try {
            $product->delete();

            return redirect()->route('inventory.index')->with('success', 'Produk berhasil dihapus.');
        } catch (QueryException $exception) {
            return redirect()->route('inventory.index')
                ->with('error', 'Produk tidak bisa dihapus karena sudah dipakai dalam transaksi.');
        }
    }
}
