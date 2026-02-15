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
    public function index(): View
    {
        $cart = session('pos_cart', []);
        $total = collect($cart)->sum('subtotal');

        return view('pos.index', [
            'cart' => $cart,
            'total' => $total,
        ]);
    }

    public function addItem(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'barcode' => 'required|string',
        ]);

        $product = Product::where('barcode', $data['barcode'])->first();
        if (!$product) {
            return back()->withErrors([
                'barcode' => 'Produk tidak ditemukan.',
            ])->withInput();
        }

        $cart = session('pos_cart', []);
        $productId = $product->id;
        $price = (float) $product->price_sell;

        if (isset($cart[$productId])) {
            $cart[$productId]['qty'] += 1;
            $cart[$productId]['subtotal'] = $cart[$productId]['qty'] * $cart[$productId]['price'];
        } else {
            $cart[$productId] = [
                'id' => $productId,
                'name' => $product->name,
                'price' => $price,
                'qty' => 1,
                'subtotal' => $price,
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

        $data = $request->validate([
            'payment_method' => 'required|string',
            'paid' => 'nullable|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:500',
        ]);

        $total = collect($cart)->sum('subtotal');
        $paid = (float) ($data['paid'] ?? 0);
        $remaining = $total - $paid;
        $paymentMethod = $data['payment_method'];
        $needsReceivable = $remaining > 0 || $paymentMethod === 'kasbon';

        if ($needsReceivable && empty($data['customer_name'])) {
            return back()->withErrors([
                'customer_name' => 'Nama pelanggan wajib diisi untuk piutang.',
            ])->withInput();
        }

        $receiptNo = 'SL-'.now()->format('YmdHis').'-'.str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT);
        $saleId = null;

        DB::transaction(function () use ($cart, $data, $total, $paid, $remaining, $paymentMethod, $needsReceivable, $receiptNo, &$saleId) {
            $sale = Sale::create([
                'receipt_no' => $receiptNo,
                'sold_at' => now(),
                'payment_method' => $paymentMethod,
                'total' => $total,
                'paid' => $paid,
                'change' => $paid > $total ? $paid - $total : 0,
                'note' => $data['note'] ?? null,
            ]);
            $saleId = $sale->id;

            foreach ($cart as $item) {
                $product = Product::find($item['id']);
                if (!$product) {
                    continue;
                }

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'cost' => $product->price_buy,
                    'subtotal' => $item['subtotal'],
                ]);

                $product->decrement('stock', $item['qty']);
            }

            if ($needsReceivable) {
                Receivable::create([
                    'sale_id' => $sale->id,
                    'customer_name' => $data['customer_name'],
                    'total' => $total,
                    'remaining' => $remaining > 0 ? $remaining : $total,
                    'status' => 'belum_lunas',
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
