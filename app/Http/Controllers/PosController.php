<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Receivable;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    private function normalizeCurrency(?string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', $value ?? '');

        return $digits === '' ? null : $digits;
    }

    public function index(): View
    {
        $cart     = session('pos_cart', []);
        $total    = collect($cart)->sum('subtotal');
        $products = Product::where('active', true)
            ->select('id', 'name', 'barcode', 'price_sell', 'stock', 'unit')
            ->orderBy('name')
            ->get();

        return view('pos.index', [
            'cart'     => $cart,
            'total'    => $total,
            'products' => $products,
        ]);
    }

    public function addItem(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'barcode'    => 'nullable|string',
            'product_id' => 'nullable|integer|exists:products,id',
            'qty'        => 'nullable|integer|min:1',
        ]);

        $product = null;

        // Cari produk berdasarkan barcode jika ada
        if (! empty($data['barcode'])) {
            $product = Product::where('barcode', $data['barcode'])->first();
        }

        // Jika tidak ditemukan barcode atau barcode kosong, cari berdasarkan product_id
        if (! $product && ! empty($data['product_id'])) {
            $product = Product::find($data['product_id']);
        }

        if (! $product) {
            return back()->withErrors([
                'barcode' => 'Produk tidak ditemukan.',
            ])->withInput();
        }

        $qty = (int) ($data['qty'] ?? 1);

        // Validasi stok
        if ($qty > $product->stock) {
            return back()->withErrors([
                'barcode' => "Stok tidak mencukupi. Stok tersedia: {$product->stock}",
            ])->withInput();
        }

        $cart      = session('pos_cart', []);
        $productId = $product->id;
        $price     = (float) $product->price_sell;

        if (isset($cart[$productId])) {
            $newQty = $cart[$productId]['qty'] + $qty;

            // Validasi total qty di cart tidak melebihi stok
            if ($newQty > $product->stock) {
                return back()->withErrors([
                    'barcode' => "Stok tidak mencukupi. Di keranjang: {$cart[$productId]['qty']}, Stok tersedia: {$product->stock}",
                ])->withInput();
            }

            $cart[$productId]['qty']      = $newQty;
            $cart[$productId]['subtotal'] = $cart[$productId]['qty'] * $cart[$productId]['price'];
        } else {
            $cart[$productId] = [
                'id'       => $productId,
                'name'     => $product->name,
                'price'    => $price,
                'qty'      => $qty,
                'subtotal' => $price * $qty,
            ];
        }

        session(['pos_cart' => $cart]);

        return back();
    }

    public function removeItem(int $productId): RedirectResponse
    {
        $cart = session('pos_cart', []);
        unset($cart[$productId]);
        session(['pos_cart' => $cart]);

        return back();
    }

    public function clearCart(): RedirectResponse
    {
        session()->forget('pos_cart');

        return back();
    }

    public function checkout(Request $request): RedirectResponse
    {
        $cart = session('pos_cart', []);
        if (empty($cart)) {
            return back()->withErrors([
                'checkout' => 'Keranjang masih kosong.',
            ]);
        }

        $request->merge([
            'paid' => $this->normalizeCurrency($request->input('paid')),
        ]);

        $data = $request->validate([
            'payment_method' => 'required|string',
            'paid'           => 'nullable|numeric|min:0',
            'customer_name'  => 'nullable|string|max:255',
            'note'           => 'nullable|string|max:500',
        ]);

        $total           = collect($cart)->sum('subtotal');
        $paid            = (float) ($data['paid'] ?? 0);
        $remaining       = $total - $paid;
        $paymentMethod   = $data['payment_method'];
        $needsReceivable = $remaining > 0 || $paymentMethod === 'kasbon';

        if ($needsReceivable && empty($data['customer_name'])) {
            return back()->withErrors([
                'customer_name' => 'Nama pelanggan wajib diisi untuk piutang.',
            ])->withInput();
        }

        $receiptNo = 'SL-' . now()->format('YmdHis') . '-' . str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT);
        $saleId    = null;

        DB::transaction(function () use ($cart, $data, $total, $paid, $remaining, $paymentMethod, $needsReceivable, $receiptNo, &$saleId) {
            $sale = Sale::create([
                'receipt_no'     => $receiptNo,
                'sold_at'        => now(),
                'payment_method' => $paymentMethod,
                'total'          => $total,
                'paid'           => $paid,
                'change'         => $paid > $total ? $paid - $total : 0,
                'note'           => $data['note'] ?? null,
            ]);
            $saleId = $sale->id;

            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                if (! $product) {
                    continue;
                }

                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $product->id,
                    'qty'        => $item['qty'],
                    'price'      => $item['price'],
                    'cost'       => $product->price_buy,
                    'subtotal'   => $item['subtotal'],
                ]);

                $product->decrement('stock', $item['qty']);
            }

            if ($needsReceivable) {
                Receivable::create([
                    'sale_id'       => $sale->id,
                    'customer_name' => $data['customer_name'],
                    'total'         => $total,
                    'remaining'     => $remaining > 0 ? $remaining : $total,
                    'status'        => 'belum_lunas',
                ]);
            }
        });

        session()->forget('pos_cart');
        if ($saleId) {
            return redirect()->route('pos.receipt', $saleId)->with('success', 'Transaksi berhasil disimpan.');
        }

        return back()->with('success', 'Transaksi berhasil disimpan.');
    }

    public function receipt(Sale $sale): View
    {
        $sale->load(['items.product']);

        return view('pos.receipt', [
            'sale' => $sale,
        ]);
    }
}
