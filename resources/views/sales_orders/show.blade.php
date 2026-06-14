<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('ព័ត៌មានលម្អិតនៃការលក់: ') }} {{ $salesOrder->so_number }}
            </h2>
            <a href="{{ route('sales-orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border-b pb-6 mb-6 text-sm">
                    <div>
                        <p class="text-gray-500">អតិថិជន (Customer)</p>
                        <p class="font-bold text-lg text-gray-800">{{ $salesOrder->customer->name }}</p>
                        <p class="text-gray-600">{{ $salesOrder->customer->email }} | {{ $salesOrder->customer->phone }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">កាលបរិច្ឆេទបញ្ជាទិញ</p>
                        <p class="font-bold text-gray-800">{{ $salesOrder->order_date }}</p>
                        <p class="text-gray-500 mt-2">ស្ថានភាពចរន្ត</p>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 uppercase">{{ $salesOrder->status }}</span>
                    </div>
                    <div class="text-right md:text-right">
                        @if($salesOrder->status == 'draft')
                        <form action="{{ route('sales-orders.confirm', $salesOrder->id) }}" method="POST" onsubmit="return confirm('តើអ្នកពិតជាចង់បញ្ជាក់ការលក់នេះមែនទេ? ប្រព័ន្ធនឹងដកស្តុក និងចេញវិក្កយបត្រស្វ័យប្រវត្តិ!')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-bold text-sm text-white uppercase tracking-widest hover:bg-green-700 shadow-md">
                                ✓ បញ្ជាក់ការលក់ (CONFIRM ORDER)
                            </button>
                        </form>
                        @else
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200 inline-block text-left">
                            <p class="text-green-800 font-bold">✓ វិក្កយបត្រត្រូវបានបង្កើតរួចរាល់៖</p>
                            @if($invoice)
                            <p class="text-sm font-medium text-gray-700 mt-1">លេខវិក្កយបត្រ: <span class="text-indigo-600 font-bold">{{ $invoice->invoice_number }}</span></p>
                            <div class="mt-2 flex gap-2">
                                <a href="{{ route('invoices.pdf.stream', $invoice->id) }}" target="_blank" class="text-xs bg-indigo-500 text-white px-2 py-1 rounded hover:bg-indigo-600">មើល PDF</a>
                                <a href="{{ route('invoices.pdf.download', $invoice->id) }}" class="text-xs bg-gray-500 text-white px-2 py-1 rounded hover:bg-gray-600">ទាញយក PDF</a>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                <h3 class="text-lg font-medium text-gray-900 mb-4">មុខទំនិញដែលបានទិញ</h3>
                <table class="min-w-full divide-y divide-gray-200 border mb-6 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">ឈ្មោះផលិតផល</th>
                            <th class="px-4 py-3 text-center">បរិមាណ</th>
                            <th class="px-4 py-3 text-right">តម្លៃឯកតា</th>
                            <th class="px-4 py-3 text-center">បញ្ចុះតម្លៃ</th>
                            <th class="px-4 py-3 text-right">សរុបជួរ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($salesOrder->items as $index => $item)
                        <tr>
                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $item->product->name }}</td>
                            <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right">${{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-4 py-3 text-center">{{ $item->discount_percent }}%</td>
                            <td class="px-4 py-3 text-right font-bold">${{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="flex justify-end text-sm">
                    <div class="w-full md:w-1/3 border p-4 bg-gray-50 rounded-lg space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-500">សរុបបណ្ដោះអាសន្ន (Subtotal):</span>
                            <span class="font-bold">${{ number_format($salesOrder->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">ពន្ធអាករ (Tax 10% VAT):</span>
                            <span class="font-bold text-red-600">+ ${{ number_format($salesOrder->tax_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-2 text-base font-bold text-indigo-700">
                            <span>ទឹកប្រាក់សរុបរួម (Total Amount):</span>
                            <span>${{ number_format($salesOrder->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
