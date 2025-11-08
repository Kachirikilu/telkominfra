<div class="mb-6 border p-6 rounded-xl bg-gray-50 shadow-md">
    <h4 class="text-xl font-extrabold mb-4 text-indigo-700 border-b pb-2">
        <i class="fas fa-comments mr-2"></i> Hubungkan Komentar Pengguna
    </h4>

    {{-- 🔍 Input pencarian realtime --}}
    <div class="mb-3">
        <label for="searchKomentar" class="block text-sm font-medium text-gray-700">
            Cari Komentar Belum Terhubung (Nama Tempat/Komentar):
        </label>
        <input type="text"
               id="searchKomentar"
               wire:model.live="search"
               placeholder="Ketik nama tempat atau isi komentar..."
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border focus:ring-indigo-500 focus:border-indigo-500">
    </div>

    {{-- 🔹 Hasil pencarian komentar --}}
    <div class="space-y-2 max-h-60 overflow-y-auto border rounded-md p-3 bg-white">
        @if (strlen(trim($search)) < 2)
            <p class="text-gray-500 text-sm italic">
                Ketik minimal 2 huruf untuk menampilkan komentar...
            </p>
        @elseif ($komentars->isEmpty())
            <p class="text-gray-500 text-sm italic">
                Tidak ada komentar belum terhubung ditemukan.
            </p>
        @else
            @foreach ($komentars as $item)
                <div class="flex items-start space-x-2 border-b last:border-b-0 p-2 hover:bg-indigo-50 transition">
                    <input type="checkbox" wire:model="selectedKeluhan" value="{{ $item->id }}"
                        class="mt-1.5 h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">

                    <div class="flex-1">
                        <p class="font-semibold text-sm text-indigo-800">{{ $item->nama_pengguna }}</p>
                        <p class="text-gray-600 text-xs italic">{{ $item->nama_tempat }}</p>
                        <p class="text-gray-800 text-sm">"{{ $item->komentar }}"</p>
                    </div>
                </div>
            @endforeach

            {{-- 🔸 Pagination --}}
            <div class="pt-2 border-t mt-2">
                {{ $komentars->links() }}
            </div>
        @endif
    </div>

    {{-- 🔹 Tombol submit --}}
    <button wire:click="assignKomentar" href="#"
        wire:loading.attr="disabled"
        class="mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-semibold text-sm transition disabled:opacity-50">
        <i class="fas fa-save mr-1"></i>
        <span wire:loading.remove>Hubungkan ke Perjalanan Ini</span>
        <span wire:loading><i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...</span>
    </button>

    {{-- 🔔 Event listener --}}
    <script>
        document.addEventListener('livewire:load', () => {
            Livewire.on('alert', (data) => alert(data.message));

            // 🔁 Reload halaman penuh jika dispatch('reloadPage') dipanggil
            Livewire.on('reloadPage', () => window.location.reload());
        });
    </script>
</div>
