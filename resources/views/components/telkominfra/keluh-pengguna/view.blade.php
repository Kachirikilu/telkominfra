    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f9;
        }

        /* Gaya dasar untuk Pagination */
        .pagination-container .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
        }
    </style>

    <div class="container mx-auto p-2 sm:p-6">

        {{-- ============================== STATISTIK RINGKASAN ============================== --}}
        <h2 class="text-2xl font-extrabold text-gray-900 mb-4 border-b pb-2">Dashboard Keluhan</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

            {{-- Total Keseluruhan --}}
            <div
                class="bg-indigo-100 p-6 rounded-xl shadow-lg border-l-4 border-indigo-600 flex justify-between items-start transition duration-300 hover:shadow-xl hover:scale-[1.01]">
                <div class="space-y-1">
                    <p class="text-sm font-medium text-indigo-600 uppercase tracking-wider">Total Keseluruhan</p>
                    <p class="text-4xl font-extrabold text-indigo-900">{{ number_format($totalKeluhan) }}</p>
                </div>
                <i class="fas fa-list text-5xl text-indigo-400 opacity-30 mt-1 ml-4"></i>
            </div>

            {{-- Sudah Selesai --}}
            <div
                class="bg-green-100 p-6 rounded-xl shadow-lg border-l-4 border-green-600 flex justify-between items-start transition duration-300 hover:shadow-xl hover:scale-[1.01]">
                <div class="space-y-1">
                    <p class="text-sm font-medium text-green-600 uppercase tracking-wider">Selesai (Maintenance
                        Berhasil)</p>
                    <p class="text-4xl font-extrabold text-green-900">{{ number_format($keluhanSelesai) }}</p>
                </div>
                <i class="fas fa-check-circle text-5xl text-green-400 opacity-30 mt-1 ml-4"></i>
            </div>

            {{-- Sedang Diproses (BARU) --}}
            <div
                class="bg-blue-100 p-6 rounded-xl shadow-lg border-l-4 border-blue-600 flex justify-between items-start transition duration-300 hover:shadow-xl hover:scale-[1.01]">
                <div class="space-y-1">
                    <p class="text-sm font-medium text-blue-600 uppercase tracking-wider">Sedang Diproses (Dikerjakan)
                    </p>
                    <p class="text-4xl font-extrabold text-blue-900">{{ number_format($keluhanDiproses) }}</p>
                </div>
                <i class="fas fa-tools text-5xl text-blue-400 opacity-30 mt-1 ml-4"></i>
            </div>

            {{-- Belum Selesai --}}
            <div
                class="bg-yellow-100 p-6 rounded-xl shadow-lg border-l-4 border-yellow-600 flex justify-between items-start transition duration-300 hover:shadow-xl hover:scale-[1.01]">
                <div class="space-y-1">
                    <p class="text-sm font-medium text-yellow-600 uppercase tracking-wider">Belum Selesai (Perlu Tindak
                        Lanjut)</p>
                    <p class="text-4xl font-extrabold text-yellow-900">{{ number_format($keluhanBelumSelesai) }}</p>
                </div>
                <i class="fas fa-exclamation-triangle text-5xl text-yellow-400 opacity-30 mt-1 ml-4"></i>
            </div>
        </div>

    </div>

