<x-filament::page>
    <div style="color: black" id="invoice" class="p-6 bg-white rounded-lg shadow-md max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold mb-6 text-center text-blue-600">Invoice #{{ $invoice->id }}</h1>
        
        <div class="mb-6">
            <div class="flex justify-between">
                <p><strong>Name:</strong> {{ $invoice->name }}</p>
                <p><strong>Phone:</strong> {{ $invoice->phone }}</p>
            </div>
            <p><strong>Date:</strong> {{ $invoice->created_at->format('d/m/Y') }}</p>
            <p><strong>Notes:</strong> {!! $invoice->notes !!}</p> <!-- Displaying notes with HTML rendering -->
        </div>

        <h2 style="text-align: center" class="text-3xl font-semibold mb-4 text-gray-800">invoice items</h2>
        <table class="min-w-full bg-gray-100 border border-gray-300 rounded-lg">
            <thead>
                <tr class="bg-gray-300 text-gray-700">
                    <th class="py-3 px-4 text-left">Product</th>
                    <th class="py-3 px-4 text-left">Quantity</th>
                    <th class="py-3 px-4 text-right">Price</th>
                    <th class="py-3 px-4 text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @php $invoice_items = \App\Models\InvoiceItem::where('invoice_id', $invoice->id)->get(); @endphp
                @forelse ($invoice_items as $item)
                    <tr class="hover:bg-gray-200 border-b border-gray-300">
                        <td class="py-3 px-4">{{ $item->product->name }}</td>
                        <td class="py-3 px-4">{{ $item->quantity }}</td>
                        <td class="py-3 px-4 text-right">{{ number_format($item->price, 2) }}</td>
                        <td class="py-3 px-4 text-right">{{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-3 text-center">No items found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <h3 class="text-2xl font-semibold mt-6 text-right">Total: {{ number_format($invoice->total, 2) }}</h3>

        <button style="color:white;background-color:blue" onclick="window.print()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Print Invoice</button>
    </div>

    <style>
        body {
            font-family: 'Arial', sans-serif; /* Set a readable font */
        }


        @media print {
            body * {
                visibility: hidden; /* Hide all elements */
            }

            #invoice, #invoice * {
                visibility: visible; /* Show the invoice div and its contents */
            }

            #invoice {
                position: absolute; /* Position the invoice absolutely */
                left: 0; /* Align to the left */
                top: 0; /* Align to the top */
                background-color: white; /* Ensure background is white for printing */
                margin: 0; /* Remove margin */
                padding: 20px; /* Add some padding for printed invoice */
            }

            button {
                display: none; /* Hide the print button during printing */
            }
        }
    </style>
</x-filament::page>
