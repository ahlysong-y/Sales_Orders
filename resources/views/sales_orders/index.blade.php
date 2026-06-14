<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('បញ្ជីការលក់ទាំងអស់ (Sales Orders)') }}
            </h2>
            <a href="{{ route('sales-orders.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                + បង្កើតការលក់ថ្មី
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">លេខ SO</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">អតិថិជន</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">កាលបរិច្ឆេទ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ទឹកប្រាក់សរុប</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ស្ថានភាព</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">សកម្មភាព</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($salesOrders as $so)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-indigo-600">{{ $so->so_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $so->customer->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $so->order_date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-bold">${{ number_format($so->total_amount, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($so->status == 'draft')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                                    @elseif($so->status == 'confirmed')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Confirmed</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm flex items-center gap-2">
                                    <a href="{{ route('sales-orders.show', $so->id) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">View</a>

                                    @if($so->status == 'draft')
                                    <a href="{{ route('sales-orders.edit', $so->id) }}" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Edit</a>

                                    <form action="{{ route('sales-orders.destroy', $so->id) }}" method="POST" id="delete-form-{{ $so->id }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $so->id }})" class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Delete
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">មិនទាន់មានទិន្នន័យលក់នៅឡើយទេ។</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $salesOrders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'តើអ្នកពិតជាចង់លុបមែនទេ?'
            , text: "ទិន្នន័យលក់នេះនឹងត្រូវលុបបណ្ដោះអាសន្ន!"
            , icon: 'warning'
            , showCancelButton: true
            , confirmButtonColor: '#d33'
            , cancelButtonColor: '#3085d6'
            , confirmButtonText: 'ព្រមលុប'
            , cancelButtonText: 'បោះបង់'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }

</script>

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
        , title: 'មានបញ្ហា!'
        , text: "{{ session('error') }}"
    , });

</script>
@endif
