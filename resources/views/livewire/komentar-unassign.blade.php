<div class="mb-6 border p-6 rounded-xl bg-indigo-50 shadow-md" wire:poll.500ms>
    <h4 class="text-xl font-extrabold mb-4 text-indigo-700 border-b pb-2">
        <i class="fas fa-link mr-2"></i>
        Komentar Terhubung ke Perjalanan Ini ({{ $komentarTerhubung->total() }})
    </h4>

    @if($komentarTerhubung->isNotEmpty())
        <div class="space-y-3 max-h-64 overflow-y-auto pr-2">
            @foreach($komentarTerhubung as $komentar)
                <div class="p-3 bg-white rounded-lg shadow-sm border border-indigo-100 flex items-start justify-between">
                    <div class="flex-1 mr-3">
                        <p class="font-semibold text-sm text-indigo-800">{{ $komentar->nama_pengguna }}</p>
                        <p class="text-gray-600 text-xs italic mb-1">{{ $komentar->nama_tempat }}</p>
                        <p class="text-gray-800 text-sm">"{{ $komentar->komentar }}"</p>
                    </div>
                    <button wire:click="unassignKomentar({{ $komentar->id }})"
                            class="text-xs font-semibold px-2 py-1 rounded-full bg-red-100 text-red-700 hover:bg-red-200 transition duration-150">
                        <i class="fas fa-times"></i> Hapus
                    </button>
                </div>
            @endforeach
        </div>

        <!-- 🔹 Pagination -->
        <div class="mt-4">
            {{ $komentarTerhubung->links() }}
        </div>
    @else
        <p class="text-gray-500 text-sm italic">
            Belum ada komentar pengguna yang dihubungkan ke perjalanan ini.
        </p>
    @endif

    
</div>
