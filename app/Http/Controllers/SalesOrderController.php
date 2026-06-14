<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\Invoice;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class SalesOrderController extends Controller
{

    // ១. បង្ហាញបញ្ជីការលក់ទាំងអស់ (Index)
    public function index()
    {
        // ទាញយកទិន្នន័យ Sales Order ទាំងអស់ជាមួយព័ត៌មានអតិថិជន
        $salesOrders = SalesOrder::with('customer')->latest()->paginate(10);

        return view('sales_orders.index', compact('salesOrders'));
    }

    // ២. បង្ហាញព័ត៌មានលម្អិតនៃការលក់មួយ (Show)
    public function show(SalesOrder $salesOrder)
    {
        // ហៅទិន្នន័យទំនិញលម្អិត (Items) និងផលិតផល (Product) មកជាមួយ
        $salesOrder->load('customer', 'items.product');

        // ស្វែងរក Invoice ដែលទាក់ទងនឹង SO នេះ (បើមាន)
        $invoice = Invoice::where('sales_order_id', $salesOrder->id)->first();

        return view('sales_orders.show', compact('salesOrder', 'invoice'));
    }
    // បង្ហាញទំព័រ Form បង្កើតការលក់ និងបោះអថេរទៅឱ្យ View
    public function create()
    {
        $customers = Customer::where('status', 'active')->get();
        $products = Product::all();

        return view('sales_orders.create', compact('customers', 'products'));
    }

    // រក្សាទុកទិន្នន័យលក់ (Status: Draft)
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        return DB::transaction(function () use ($request) {
            $soNumber = 'SO-' . date('Y') . '-' . str_pad(
                SalesOrder::whereYear('created_at', date('Y'))->count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );

            $subtotal = 0;
            $items = [];

            foreach ($request->items as $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];
                $discountPercent = $item['discount_percent'] ?? 0;
                $discount = $lineTotal * ($discountPercent / 100);
                $lineSubtotal = $lineTotal - $discount;

                $items[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_percent' => $discountPercent,
                    'subtotal' => $lineSubtotal,
                ];

                $subtotal += $lineSubtotal;
            }

            $taxRate = 10; // 10% VAT
            $taxAmount = $subtotal * ($taxRate / 100);
            $totalAmount = $subtotal + $taxAmount;

            $so = SalesOrder::create([
                'so_number' => $soNumber,
                'customer_id' => $request->customer_id,
                'order_date' => $request->order_date,
                'status' => 'draft',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'created_by' => Auth::id(),
            ]);

            $so->items()->createMany($items);

            return redirect()->route('dashboard')->with('success', 'Sales Order ត្រូវបានបង្កើតជា Draft រួចរាល់!');
        });
    }

    // បញ្ជាក់ការលក់ (Confirm SO) -> ពិនិត្យស្តុក -> ដកស្តុក -> បង្កើត Invoice
    public function confirm(SalesOrder $salesOrder)
    {
        foreach ($salesOrder->items as $item) {
            if ($item->product->stock_quantity < $item->quantity) {
                return back()->with('error', "ស្តុកមិនគ្រប់គ្រាន់សម្រាប់ផលិតផល {$item->product->name} ឡើយ!");
            }
        }

        DB::transaction(function () use ($salesOrder) {
            foreach ($salesOrder->items as $item) {
                $item->product->decrement('stock_quantity', $item->quantity);

                InventoryTransaction::create([
                    'product_id' => $item->product_id,
                    'type' => 'stock_out',
                    'quantity' => $item->quantity,
                    'reference_type' => 'SalesOrder',
                    'reference_id' => $salesOrder->id,
                    'note' => 'Sold via SO: ' . $salesOrder->so_number,
                ]);
            }

            $salesOrder->update(['status' => 'confirmed']);
            $this->createInvoiceFromSO($salesOrder);
        });

        return back()->with('success', 'បានបញ្ជាក់ការលក់ និងចេញវិក្កយបត្ររួចរាល់!');
    }

    private function createInvoiceFromSO(SalesOrder $salesOrder)
    {
        $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad(
            Invoice::whereYear('created_at', date('Y'))->count() + 1,
            4,
            '0',
            STR_PAD_LEFT
        );

        Invoice::create([
            'invoice_number' => $invoiceNumber,
            'sales_order_id' => $salesOrder->id,
            'customer_id' => $salesOrder->customer_id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'total_amount' => $salesOrder->total_amount,
            'paid_amount' => 0,
            'outstanding_balance' => $salesOrder->total_amount,
            'payment_status' => 'unpaid',
        ]);
    }

    // ១. បង្ហាញទំព័រ Form កែសម្រួល (Edit)
    public function edit(SalesOrder $salesOrder)
    {
        // អនុញ្ញាតឱ្យកែបាន តែស្ថានភាព draft ប៉ុណ្ណោះ
        if ($salesOrder->status !== 'draft') {
            return redirect()->route('sales-orders.index')->with('error', 'មិនអាចកែសម្រួលបានទេ ព្រោះការលក់នេះត្រូវបានបញ្ជាក់រួចហើយ!');
        }

        $customers = Customer::where('status', 'active')->get();
        $products = Product::all();
        $salesOrder->load('items'); // ទាញយកមុខទំនិញចាស់ៗមកជាមួយ

        return view('sales_orders.edit', compact('salesOrder', 'customers', 'products'));
    }

    // ២. រក្សាទុកការធ្វើបច្ចុប្បន្នភាព (Update)
    public function update(Request $request, SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'draft') {
            return redirect()->route('sales-orders.index')->with('error', 'មិនអាចកែសម្រួលបានទេ!');
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        return DB::transaction(function () use ($request, $salesOrder) {
            $subtotal = 0;
            $items = [];

            foreach ($request->items as $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];
                $discount = $lineTotal * (($item['discount_percent'] ?? 0) / 100);
                $lineSubtotal = $lineTotal - $discount;

                $items[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'subtotal' => $lineSubtotal,
                ];
                $subtotal += $lineSubtotal;
            }

            $taxAmount = $subtotal * (10 / 100); // 10% VAT [cite: 315, 316]
            $totalAmount = $subtotal + $taxAmount;

            // Update ព័ត៌មានមេ
            $salesOrder->update([
                'customer_id' => $request->customer_id,
                'order_date' => $request->order_date,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
            ]);

            // លុបមុខទំនិញចាស់ៗចោល រួចបញ្ចូលថ្មី
            $salesOrder->items()->delete();
            $salesOrder->items()->createMany($items);

            return redirect()->route('sales-orders.index')->with('success', 'ការលក់ត្រូវបានកែសម្រួលដោយជោគជ័យ!');
        });
    }

    // ៣. មុខងារលុប (Destroy - បែប Soft Delete)
    public function destroy(SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'draft') {
            return back()->with('error', 'មិនអាចលុបបានទេ ព្រោះការលក់នេះត្រូវបានបញ្ជាក់រួចហើយ!');
        }

        // លុបបែប Soft Delete (មិនបាត់ពី Database ទេ តែលែងបង្ហាញលើប្រព័ន្ធ) [cite: 273, 686]
        $salesOrder->delete();

        return back()->with('success', 'បានលុបការលក់ដោយជោគជ័យ!');
    }
}
