<?php

namespace App\Http\Controllers;

use App\Models\Invoice; // បន្ថែមចំណុចនេះ
use App\Models\Payment; // បន្ថែមចំណុចនេះ
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    // ១. កត់ត្រាការបង់ប្រាក់ (Partial ឬ Full Payment)
    public function recordPayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->outstanding_balance, // [cite: 441]
            'payment_date' => 'required|date', // [cite: 442]
            'method' => 'required|in:cash,bank_transfer,cheque,online', // [cite: 443]
            'reference' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $invoice) {
            // បង្កើតប្រវត្តិនៃការបង់ប្រាក់
            Payment::create([
                'invoice_id' => $invoice->id, // [cite: 446]
                'amount' => $request->amount, // [cite: 447]
                'payment_date' => $request->payment_date, // [cite: 448]
                'method' => $request->method, // [cite: 449]
                'reference' => $request->reference, // [cite: 454]
            ]);

            // គណនាលុយដែលបានបង់ និងលុយនៅសល់ (Outstanding Balance)
            $newPaidAmount = $invoice->paid_amount + $request->amount; // [cite: 458]
            $outstanding = $invoice->total_amount - $newPaidAmount; // [cite: 459]

            // ធ្វើបច្ចុប្បន្នភាព Status នៃវិក្កយបត្រ
            $invoice->update([
                'paid_amount' => $newPaidAmount, // [cite: 461]
                'outstanding_balance' => $outstanding, // [cite: 462]
                'payment_status' => $outstanding <= 0 ? 'paid' : 'partial', // [cite: 463]
            ]);
        });

        return back()->with('success', 'ការទូទាត់ប្រាក់ត្រូវបានកត់ត្រាដោយជោគជ័យ!'); // [cite: 466]
    }

    // ២. ទាញយកឯកសារ Invoice ជា PDF (Download)
    public function downloadPdf(Invoice $invoice)
    {
        // ប្រើ Eager Loading ដើម្បីចៀសវាងបញ្ហា N+1 Query
        $invoice->load(['customer', 'salesOrder.items.product', 'payments']); // [cite: 418, 424, 425, 426]

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice')) // [cite: 427]
            ->setPaper('a4', 'portrait'); // [cite: 427]

        return $pdf->download("Invoice-{$invoice->invoice_number}.pdf"); // [cite: 428]
    }

    // ៣. មើលឯកសារ Invoice លើ Browser (Stream)
    public function streamPdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'salesOrder.items.product']); // [cite: 432]

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice')) // [cite: 433]
            ->setPaper('a4', 'portrait'); // [cite: 433]

        return $pdf->stream("Invoice-{$invoice->invoice_number}.pdf"); // [cite: 434]
    }
}
