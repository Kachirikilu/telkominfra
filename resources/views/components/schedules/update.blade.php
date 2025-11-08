<div class="bg-white flex-1 overflow-auto shadow-md rounded-md px-3 sm:px-6 md:px-30 lg:px-20 xl:px-50 py-6">
    <h2 class="text-xl font-semibold mb-4 border-b border-gray-200 pb-2">Edit Jadwal Ceramah</h2>
    <form action="{{ route('admin.schedules.update', $jadwalCeramah->id) }}" method="POST" class="space-y-4" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div>
            <label for="judul_ceramah" class="block text-gray-700 text-sm font-bold mb-2">Judul Ceramah</label>
            <input type="text" id="judul_ceramah" name="judul_ceramah" value="{{ $jadwalCeramah->judul_ceramah ?? old('judul_ceramah') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('judul_ceramah') border-red-500 @enderror" placeholder="Misalnya: Keutamaan Bulan Ramadhan">
            @error('judul_ceramah')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="nama_ustadz" class="block text-gray-700 text-sm font-bold mb-2">Nama Ustadz</label>
            <input type="text" id="nama_ustadz" name="nama_ustadz" value="{{ $jadwalCeramah->nama_ustadz ?? old('nama_ustadz') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nama_ustadz') border-red-500 @enderror" placeholder="Misalnya: Ustadz Abdul Somad">
            @error('nama_ustadz')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="gambar" class="block text-gray-700 text-sm font-bold mb-2">Gambar (Opsional)</label>
            <input type="file" id="gambar" name="gambar" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('gambar') border-red-500 @enderror">
            @if($jadwalCeramah->gambar && $jadwalCeramah->gambar !== 'images/No Image.png')
                <img src="{{ asset($jadwalCeramah->gambar) }}" alt="{{ $jadwalCeramah->judul_ceramah }}" class="mt-2 max-h-40" id="existing-gambar">
            @endif
            <div class="mt-2">
                <img id="preview-gambar" src="#" alt="Pratinjau Gambar Baru" class="hidden max-h-40">
            </div>
            @error('gambar')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        
            <script>
                const gambarInputUpdate = document.getElementById('gambar');
                const previewGambarUpdate = document.getElementById('preview-gambar');
                const existingGambar = document.getElementById('existing-gambar');
        
                gambarInputUpdate.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewGambarUpdate.src = e.target.result;
                            previewGambarUpdate.classList.remove('hidden');
                            // Sembunyikan gambar yang sudah ada saat gambar baru dipilih
                            if (existingGambar) {
                                existingGambar.classList.add('hidden');
                            }
                        }
                        reader.readAsDataURL(file);
                    } else {
                        previewGambarUpdate.src = '#';
                        previewGambarUpdate.classList.add('hidden');
                        // Tampilkan kembali gambar yang sudah ada jika tidak ada file baru yang dipilih
                        if (existingGambar) {
                            existingGambar.classList.remove('hidden');
                        }
                    }
                });
            </script>
        </div>

        <div>
            <label for="tanggal_ceramah" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Ceramah</label>
            <input type="date" id="tanggal_ceramah" name="tanggal_ceramah" value="{{ $jadwalCeramah->tanggal_ceramah ? $jadwalCeramah->tanggal_ceramah->format('Y-m-d') : old('tanggal_ceramah') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('tanggal_ceramah') border-red-500 @enderror">
            @error('tanggal_ceramah')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="jam_mulai" class="block text-gray-700 text-sm font-bold mb-2">Jam Mulai</label>
                <input type="time" id="jam_mulai" name="jam_mulai" value="{{ $jadwalCeramah->jam_mulai ? $jadwalCeramah->jam_mulai->format('H:i') : old('jam_mulai') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('jam_mulai') border-red-500 @enderror">
                @error('jam_mulai')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="jam_selesai" class="block text-gray-700 text-sm font-bold mb-2">Jam Selesai (Opsional)</label>
                <input type="time" id="jam_selesai" name="jam_selesai" value="{{ $jadwalCeramah->jam_selesai ? $jadwalCeramah->jam_selesai->format('H:i') : old('jam_selesai') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('jam_selesai') border-red-500 @enderror">
                @error('jam_selesai')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="tempat_ceramah" class="block text-gray-700 text-sm font-bold mb-2">Tempat Ceramah</label>
            <input type="text" id="tempat_ceramah" name="tempat_ceramah" value="{{ $jadwalCeramah->tempat_ceramah ?? old('tempat_ceramah') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('tempat_ceramah') border-red-500 @enderror" placeholder="Misalnya: Ruang Utama Masjid">
            @error('tempat_ceramah')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="tentang_ceramah" class="block text-gray-700 text-sm font-bold mb-2">Tentang Ceramah (Deskripsi Singkat)</label>
            <textarea id="tentang_ceramah" name="tentang_ceramah" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('tentang_ceramah') border-red-500 @enderror" placeholder="Berikan deskripsi singkat mengenai isi ceramah.">{{ $jadwalCeramah->tentang_ceramah ?? old('tentang_ceramah') }}</textarea>
            @error('tentang_ceramah')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="kategori_ceramah" class="block text-gray-700 text-sm font-bold mb-2">Kategori Ceramah</label>
            <select id="kategori_ceramah" name="kategori_ceramah" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('kategori_ceramah') border-red-500 @enderror">
                <option value="">Pilih Kategori</option>
                <option value="Tafsir" {{ $jadwalCeramah->kategori_ceramah == 'Tafsir' ? 'selected' : (old('kategori_ceramah') == 'Tafsir' ? 'selected' : '') }}>Tafsir</option>
                <option value="Fadhilah Ramadhan" {{ $jadwalCeramah->kategori_ceramah == 'Fadhilah Ramadhan' ? 'selected' : (old('kategori_ceramah') == 'Fadhilah Ramadhan' ? 'selected' : '') }}>Fadhilah Ramadhan</option>
                <option value="Akidah" {{ $jadwalCeramah->kategori_ceramah == 'Akidah' ? 'selected' : (old('kategori_ceramah') == 'Akidah' ? 'selected' : '') }}>Akidah</option>
                <option value="Fiqih" {{ $jadwalCeramah->kategori_ceramah == 'Fiqih' ? 'selected' : (old('kategori_ceramah') == 'Fiqih' ? 'selected' : '') }}>Fiqih</option>
                <option value="Akhlak" {{ $jadwalCeramah->kategori_ceramah == 'Akhlak' ? 'selected' : (old('kategori_ceramah') == 'Akhlak' ? 'selected' : '') }}>Akhlak</option>
                <option value="Kajian Subuh" {{ $jadwalCeramah->kategori_ceramah == 'Kajian Subuh' ? 'selected' : (old('kategori_ceramah') == 'Kajian Subuh' ? 'selected' : '') }}>Kajian Subuh</option>
                <option value="Kajian Kitab" {{ $jadwalCeramah->kategori_ceramah == 'Kajian Kitab' ? 'selected' : (old('kategori_ceramah') == 'Kajian Kitab' ? 'selected' : '') }}>Kajian Kitab</option>
                <option value="Sirah Nabawiyah" {{ $jadwalCeramah->kategori_ceramah == 'Sirah Nabawiyah' ? 'selected' : (old('kategori_ceramah') == 'Sirah Nabawiyah' ? 'selected' : '') }}>Sirah Nabawiyah</option>
                <option value="Lain-lain" {{ $jadwalCeramah->kategori_ceramah == 'Lain-lain' ? 'selected' : (old('kategori_ceramah') == 'Lain-lain' ? 'selected' : '') }}>Lain-lain</option>
            </select>
            @error('kategori_ceramah')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="link_streaming" class="block text-gray-700 text-sm font-bold mb-2">Link Streaming (Opsional)</label>
            <input type="url" id="link_streaming" name="link_streaming" value="{{ $jadwalCeramah->link_streaming ?? old('link_streaming') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('link_streaming') border-red-500 @enderror" placeholder="Jika ada live streaming, masukkan linknya di sini.">
            @error('link_streaming')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Update Jadwal</button>
        </div>
    </form>
</div>