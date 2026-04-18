@extends('layouts.operator_app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <form action="{{ route('operator.stok.store') }}" method="POST">
            @csrf

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <span class="text-red-500 text-xl">⚠️</span>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Gagal menyimpan data. Silakan cek masalah berikut:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <span class="text-green-500 text-xl">✅</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Header Invoice -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
                    <h1 class="text-2xl font-bold text-white">📄 Invoice Digital - Verifikasi Hasil OCR</h1>
                    <p class="text-blue-100 text-sm">Nota Pembelian Bahan Baku</p>
                </div>

                <div class="p-6">
                    <!-- Supplier Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <label class="block text-xs font-semibold text-blue-800 uppercase tracking-wide mb-2">Supplier</label>
                            <input type="text" name="supplier" value="{{ session('ocr_result.supplier') ?? '' }}" class="w-full px-3 py-2 border border-blue-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900" placeholder="Nama Toko" required>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <label class="block text-xs font-semibold text-green-800 uppercase tracking-wide mb-2">No Nota</label>
                            <input type="text" name="no_nota" value="{{ session('ocr_result.metadata.no_nota') ?? '' }}" class="w-full px-3 py-2 border border-green-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-gray-900" placeholder="No Nota">
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <label class="block text-xs font-semibold text-purple-800 uppercase tracking-wide mb-2">Tanggal</label>
                            <input type="text" name="tanggal" value="{{ session('ocr_result.metadata.tanggal') ?? '' }}" class="w-full px-3 py-2 border border-purple-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900" placeholder="Tanggal">
                        </div>
                        <div class="bg-orange-50 p-4 rounded-lg">
                            <label class="block text-xs font-semibold text-orange-800 uppercase tracking-wide mb-2">Kontak</label>
                            <input type="text" name="alamat_kontak" value="{{ session('ocr_result.metadata.alamat_kontak') ?? '' }}" class="w-full px-3 py-2 border border-orange-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-gray-900" placeholder="Alamat & Kontak">
                        </div>
                    </div>

                    @if(session('ocr_result.image_url'))
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">📷 Gambar Nota Asli</h3>
                        <img src="{{ asset(session('ocr_result.image_url')) }}" alt="Nota" class="max-w-md h-auto border border-gray-300 rounded-lg shadow-sm">
                    </div>
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-green-600 to-green-800 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold text-white">📦 Daftar Barang</h2>
                            <p class="text-green-100 text-sm">Edit jika perlu sebelum konfirmasi.</p>
                        </div>
                        <button type="button" id="add-row" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-green-600 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                            ➕ Tambah Baris
                        </button>
                    </div>
                </div>

                <div class="p-4">
                    @if(session('ocr_result.items') && count(session('ocr_result.items')) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-[900px] divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-sm font-bold text-gray-700 uppercase tracking-wide" style="width: 40%">Nama Barang</th>
                                        <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 12%">Satuan</th>
                                        <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 14%">Harga</th>
                                        <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 10%">Jml</th>
                                        <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 14%">Subtotal</th>
                                        <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                     @foreach(session('ocr_result.items') as $index => $item)
                                         <tr class="hover:bg-gray-50 transition-colors">
                                             <td class="px-4 py-3">
                                                 <div class="flex flex-col gap-2">
                                                     <textarea name="items[{{ $index }}][nama]" title="{{ $item['nama'] ?? '' }}" rows="1" style="resize: horizontal; height: 40px; overflow: hidden;" class="w-full px-3 py-2 text-base text-black font-medium border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>{{ $item['nama'] ?? '' }}</textarea>
                                                     <label class="inline-flex items-center text-sm text-gray-600">
                                                         <input type="checkbox" name="items[{{ $index }}][is_inventory]" value="1" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                         <span class="ml-2">Hitung Stok & Harga</span>
                                                     </label>
                                                 </div>
                                                 <input type="hidden" name="items[{{ $index }}][id]" value="{{ $index }}">
                                             </td>
                                             <td class="px-4 py-2 text-center">
                                                 <input type="text" name="items[{{ $index }}][satuan]" value="{{ $item['satuan'] ?? '' }}" class="w-full px-2 py-1 text-base text-black font-medium border border-gray-300 rounded-md text-center focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="pcs">
                                             </td>
                                             <td class="px-4 py-2 text-center">
                                                 <input type="number" name="items[{{ $index }}][harga]" value="{{ $item['harga'] ?? 0 }}" class="w-full px-2 py-1 text-base text-black font-medium border {{ ($item['harga'] ?? 0) == 0 ? 'border-red-300 bg-red-50' : 'border-gray-300' }} rounded-md text-center focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" min="0" required>
                                                 @if(($item['harga'] ?? 0) == 0)
                                                     <p class="text-xs text-red-600 mt-1">⚠️ Harga 0 - Perlu dicek</p>
                                                 @endif
                                             </td>
                                             <td class="px-4 py-2 text-center">
                                                 <input type="number" name="items[{{ $index }}][qty]" value="{{ $item['qty'] ?? 1 }}" class="w-full px-2 py-1 text-base text-black font-medium border border-gray-300 rounded-md text-center focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent" min="1" required>
                                             </td>
                                             <td class="px-4 py-2 text-right">
                                                 <span class="font-bold text-black text-base subtotal-{{ $index }}">Rp {{ number_format(($item['harga'] ?? 0) * ($item['qty'] ?? 1), 0, ',', '.') }}</span>
                                             </td>
                                             <td class="px-4 py-2 text-center">
                                                 <button type="button" class="inline-flex items-center px-2 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors delete-row" data-index="{{ $index }}">
                                                     🗑️ Hapus
                                                 </button>
                                             </td>
                                         </tr>
                                     @endforeach

                                    <!-- Empty row for manual addition -->
                                    <tr class="bg-gray-50 border-t-2 border-dashed border-gray-300">
                                        <td class="px-4 py-2">
                                            <textarea disabled name="items[{{ count(session('ocr_result.items')) }}][nama]" rows="2" style="resize: none;" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md bg-gray-100" placeholder="Klik 'Tambah Baris' di atas"></textarea>
                                            <input disabled type="hidden" name="items[{{ count(session('ocr_result.items')) }}][id]" value="{{ count(session('ocr_result.items')) }}">
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <input disabled type="text" name="items[{{ count(session('ocr_result.items')) }}][satuan]" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md text-center bg-gray-100" placeholder="pcs">
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <input disabled type="number" name="items[{{ count(session('ocr_result.items')) }}][harga]" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md text-center bg-gray-100" min="0" placeholder="0">
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <input disabled type="number" name="items[{{ count(session('ocr_result.items')) }}][qty]" value="1" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md text-center bg-gray-100" min="1" placeholder="1">
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            <span class="font-semibold text-gray-900 text-sm subtotal-{{ count(session('ocr_result.items')) }}">Rp 0</span>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <button type="button" class="inline-flex items-center px-2 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors delete-row" data-index="{{ count(session('ocr_result.items')) }}">
                                                🗑️ Hapus
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <span class="text-4xl">📭</span>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Item Terdeteksi</h3>
                            <p class="text-gray-500">Coba upload gambar nota yang lebih jelas atau dengan pencahayaan yang baik.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Total Section -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-purple-600 to-purple-800 px-6 py-4">
                    <h3 class="text-lg font-bold text-white">💰 Ringkasan Pembayaran</h3>
                </div>
                <div class="p-6">
                    <div class="flex justify-end">
                        <div class="w-full max-w-sm">
                            <div class="bg-gray-50 p-4 rounded-lg border-2 border-dashed border-gray-300">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-600">Total Bayar:</span>
                                    <span class="text-lg font-bold text-gray-900" id="total_bayar_display">Rp {{ number_format(session('ocr_result.metadata.total_bayar') ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <input type="hidden" id="total_bayar" name="total_bayar" value="{{ session('ocr_result.metadata.total_bayar') ?? 0 }}">
                                <p class="text-xs text-gray-500 mt-2">💡 Total otomatis terupdate saat edit harga/qty</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row gap-4 justify-end">
                        <a href="{{ url('/operator/scan') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            🔄 Scan Ulang
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                            ✅ Konfirmasi & Tambah ke Stok
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to calculate subtotal for a specific row
    function calculateSubtotalForRow(index) {
        const hargaInput = document.querySelector('input[name="items[' + index + '][harga]"]');
        const qtyInput = document.querySelector('input[name="items[' + index + '][qty]"]');
        const subtotalSpan = document.querySelector('.subtotal-' + index);

        if (hargaInput && qtyInput && subtotalSpan) {
            const harga = parseInt(hargaInput.value) || 0;
            const qty = parseInt(qtyInput.value) || 0;
            const subtotal = harga * qty;

            subtotalSpan.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
        }
    }

    // Function to calculate total bayar
    function calculateTotalBayar() {
        let total = 0;
        document.querySelectorAll('[class*="subtotal-"]').forEach(function(span) {
            const text = span.textContent.replace('Rp ', '').replace(/\./g, '').replace(/,/g, '.');
            const value = parseInt(text) || 0;
            total += value;
        });
        const totalBayarInput = document.getElementById('total_bayar');
        const totalBayarDisplay = document.getElementById('total_bayar_display');
        if (totalBayarInput) {
            totalBayarInput.value = total;
        }
        if (totalBayarDisplay) {
            totalBayarDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }
    }

    // Add event listeners to all inputs that affect subtotal calculation
    document.querySelectorAll('input[name*="[harga]"], input[name*="[qty]"]').forEach(function(input) {
        input.addEventListener('input', function() {
            // Extract index from input name
            const match = input.name.match(/items\[(\d+)\]/);
            if (match) {
                const index = match[1];
                calculateSubtotalForRow(index);
                calculateTotalBayar();
            }
        });
    });

    // Delete row functionality
    document.querySelectorAll('.delete-row').forEach(function(button) {
        button.addEventListener('click', function() {
            const index = this.getAttribute('data-index');
            const row = this.closest('tr');
            row.remove();
            calculateTotalBayar();
        });
    });

    // Add row functionality
    let rowIndex = {{ count(session('ocr_result.items', [])) + 1 }};
    document.getElementById('add-row').addEventListener('click', function() {
        const tbody = document.querySelector('tbody');
        const emptyRow = tbody.querySelector('tr.bg-gray-50');
        const newRow = emptyRow.cloneNode(true);

        // Update names and ids
        newRow.querySelectorAll('textarea, input').forEach(function(el) {
            el.disabled = false; // MENGAKTIFKAN INPUTAN YANG BARU DICLONE
            el.classList.remove('bg-gray-100'); // MENGHILANGKAN WARNA ABU-ABU

            if (el.name) {
                el.name = el.name.replace(/\[\d+\]/, '[' + rowIndex + ']');
            }
            if (el.type === 'hidden' && el.name.includes('[id]')) {
                el.value = rowIndex;
            }
            if (el.placeholder) {
                el.placeholder = el.placeholder.replace('Klik \'Tambah Baris\' di atas', 'Bahan baru');
            }
            // Clear values
            if (el.type !== 'hidden') {
                el.value = '';
            }
        });

        // Update subtotal class
        const subtotalSpan = newRow.querySelector('[class*="subtotal-"]');
        if (subtotalSpan) {
            subtotalSpan.className = subtotalSpan.className.replace(/subtotal-\d+/, 'subtotal-' + rowIndex);
            subtotalSpan.textContent = 'Rp 0';
        }

        // Update delete button data-index
        const deleteBtn = newRow.querySelector('.delete-row');
        if (deleteBtn) {
            deleteBtn.setAttribute('data-index', rowIndex);
        }

        // Insert before empty row
        tbody.insertBefore(newRow, emptyRow);

        // Add event listeners to new inputs
        newRow.querySelectorAll('input[name*="[harga]"], input[name*="[qty]"]').forEach(function(input) {
            input.addEventListener('input', function() {
                const match = input.name.match(/items\[(\d+)\]/);
                if (match) {
                    const index = match[1];
                    calculateSubtotalForRow(index);
                    calculateTotalBayar();
                }
            });
        });

        // Add delete listener
        const newDeleteBtn = newRow.querySelector('.delete-row');
        if (newDeleteBtn) {
            newDeleteBtn.addEventListener('click', function() {
                const index = this.getAttribute('data-index');
                const row = this.closest('tr');
                row.remove();
                calculateTotalBayar();
            });
        }

        rowIndex++;
    });

    // Initial calculation
    calculateTotalBayar();
});
</script>
@endsection
