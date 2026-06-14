<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
        }

        /*  */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        /*  */
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }

        /* [cite: 475] */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        /* [cite: 475] */
        th {
            background: #2c3e50;
            color: white;
            padding: 8px;
            text-align: left;
        }

        /* [cite: 476] */
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        /* [cite: 476] */
        .total-row {
            font-weight: bold;
            background: #f8f9fa;
        }

        /* [cite: 477] */
        .badge-paid {
            color: green;
            font-weight: bold;
        }

        /* [cite: 477] */
        .badge-unpaid {
            color: red;
            font-weight: bold;
        }

        /* [cite: 478] */

    </style>
</head>
<body>
    <div class="header">
        <div class="invoice-title">វិក្កយបត្រ / INVOICE</div>
        <p>លេខ: {{ $invoice->invoice_number }} | កាលបរិច្ឆេទ: {{ $invoice->invoice_date }}</p>
    </div>
    <table>
        <tr>
            <td><strong>អតិថិជន:</strong> {{ $invoice->customer->name }}</td>
            <td><strong>ថ្ងៃផុតកំណត់:</strong> {{ $invoice->due_date }}</td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>ផលិតផល</th>
                <th>បរិមាណ</th>
                <th>តម្លៃឯកតា</th>
                <th>សរុប</th>
            </tr>
        </thead>
        <tbody> @foreach($invoice->salesOrder->items as $i => $item) <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->unit_price, 2) }}</td>
                <td>${{ number_format($item->subtotal, 2) }}</td>
            </tr> @endforeach </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" align="right">សរុបរួម / Total:</td>
                <td>${{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td colspan="4" align="right">បានទូទាត់ / Paid:</td>
                <td class="badge-paid">${{ number_format($invoice->paid_amount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="4" align="right">នៅសល់ / Outstanding:</td>
                <td class="badge-unpaid">${{ number_format($invoice->outstanding_balance, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
