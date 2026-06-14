<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('កែសម្រួលការលក់ (Edit Sales Order): ') }} {{ $salesOrder->so_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('sales-orders.update', $salesOrder->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">អតិថិជន (Customer) *</label>
                            <select name="customer_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $salesOrder->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">កាលបរិច្ឆេទ (Order Date) *</label>
                            <input type="date" name="order_date" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ $salesOrder->order_date }}" required>
                        </div>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">បញ្ជីមុខទំនិញ (Order Items)</h3>

                    <div class="overflow-x-auto mb-6 border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200" id="items-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase" style="width: 40%;">ឈ្មោះផលិតផល</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase" style="width: 15%;">បរិមាណ</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase" style="width: 20%;">តម្លៃឯកតា ($)</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase" style="width: 15%;">បញ្ចុះតម្លៃ (%)</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase" style="width: 10%;">សកម្មភាព</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($salesOrder->items as $index => $soItem)
                                <tr>
                                    <td class="px-4 py-2">
                                        <select name="items[{{ $index }}][product_id]" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                            @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ $soItem->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" name="items[{{ $index }}][quantity]" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="0.01" step="0.01" value="{{ $soItem->quantity }}" required>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" name="items[{{ $index }}][unit_price]" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="0" step="0.01" value="{{ $soItem->unit_price }}" required>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" name="items[{{ $index }}][discount_percent]" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="0" max="100" value="{{ $soItem->discount_percent }}">
                                    </td>
                                    <td class="px-4 py-2">
                                        <button type="button" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 remove-row w-full text-center">Delete</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mb-6">
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 shadow-sm" id="add-item-btn">
                            + បន្ថែមមុខទំនិញ
                        </button>
                    </div>

                    <div class="flex justify-between items-center gap-4 border-t pt-4 mt-6">
                        <a href="{{ route('sales-orders.index') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition shadow-sm">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition shadow-sm">
                            Save Changes
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        let itemIndex = {
            {
                $salesOrder - > items - > count()
            }
        };

        document.getElementById('add-item-btn').addEventListener('click', function() {
            let tableBody = document.querySelector('#items-table tbody');
            let newRow = document.createElement('tr');

            newRow.innerHTML = `
                <td class="px-4 py-2">
                    <select name="items[${itemIndex}][product_id]" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        <option value="">-- ជ្រើសរើសផលិតផល --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} (ស្តុកនៅសល់: {{ $product->stock_quantity }})</option>
                        @endforeach
                    </select>
                </td>
                <td class="px-4 py-2">
                    <input type="number" name="items[${itemIndex}][quantity]" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="0.01" step="0.01" value="1" required>
                </td>
                <td class="px-4 py-2">
                    <input type="number" name="items[${itemIndex}][unit_price]" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="0" step="0.01" placeholder="0.00" required>
                </td>
                <td class="px-4 py-2">
                    <input type="number" name="items[${itemIndex}][discount_percent]" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" min="0" max="100" value="0">
                </td>
                <td class="px-4 py-2">
                    <button type="button" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 remove-row w-full text-center">Delete</button>
                </td>
            `;

            tableBody.appendChild(newRow);
            itemIndex++;
        });

        document.querySelector('#items-table').addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('remove-row')) {
                let rowsCount = document.querySelectorAll('#items-table tbody tr').length;
                if (rowsCount > 1) {
                    e.target.closest('tr').remove();
                } else {
                    alert('ត្រូវតែមានមុខទំនិញយ៉ាងហោចណាស់មួយ!');
                }
            }
        });

    </script>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success'
        , title: 'ជោគជ័យ!'
        , text: "{{ session('success') }}"
        , timer: 3000
        , showConfirmButton: false
    });

</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error'
        , title: 'មិនអាចអនុវត្តបាន!'
        , text: "{{ session('error') }}"
    , });

</script>
@endif
