<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    // ១. បង្កើត Purchase Order ថ្មី
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        // ប្រើ Transaction ដើម្បីការពារកំហុស
        return DB::transaction(function () use ($validated) {
            // បង្កើតលេខ PO ស្វ័យប្រវត្តិ (ឧទាហរណ៍៖ PO-2026-0001)
            $poNumber = 'PO-' . date('Y') . '-' . str_pad(
                PurchaseOrder::whereYear('created_at', date('Y'))->count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );

            $po = PurchaseOrder::create([
                'po_number' => $poNumber,
                'supplier_id' => $validated['supplier_id'],
                'order_date' => $validated['order_date'],
                'status' => 'draft',
                'created_by' => \Illuminate\Support\Facades\Auth::id(), // យក ID អ្នកដែលកំពុង Login
            ]);

            $total = 0;
            foreach ($validated['items'] as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $po->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
                $total += $subtotal;
            }

            $po->update(['total_amount' => $total]); // Update តម្លៃសរុបចុងក្រោយ

            return redirect()->route('purchase-orders.index')
                ->with('success', 'Purchase Order ត្រូវបានបង្កើតដោយជោគជ័យ!');
        });
    }

    // ២. ប្តូរ Status ទៅ "Received" និងកើនស្តុកស្វ័យប្រវត្តិ
    public function markAsReceived(PurchaseOrder $purchaseOrder)
    {
        // ពិនិត្យថាត្រូវមាន status ជា 'sent' ជាមុនសិន
        if ($purchaseOrder->status !== 'sent') {
            return back()->with('error', 'PO ត្រូវតែមាន Status "Sent" ជាមុនសិន!');
        }

        DB::transaction(function () use ($purchaseOrder) {
            foreach ($purchaseOrder->items as $item) {
                // ១. បង្កើនចំនួនក្នុងស្តុក (Inventory Sync)
                $item->product->increment('stock_quantity', $item->quantity);

                // ២. កត់ត្រាចូលក្នុងប្រវត្តិចលនាស្តុក
                InventoryTransaction::create([
                    'product_id' => $item->product_id,
                    'type' => 'stock_in',
                    'quantity' => $item->quantity,
                    'reference_type' => 'Purchase Order',
                    'reference_id' => $purchaseOrder->id,
                    'note' => 'Received from PO: ' . $purchaseOrder->po_number,
                ]);
            }

            $purchaseOrder->update(['status' => 'received']); //
        });

        return back()->with('success', 'ទំនិញត្រូវបានបញ្ចូលស្តុកជោគជ័យ!');
    }
}
