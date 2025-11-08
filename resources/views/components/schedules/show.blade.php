<div class="flex-1 p-1 sm:p-3 md:p-6 lg:p-8 mt-5 md:mt-0 overflow-y-auto">
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-1 sm:px-3 md:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-3 sm:p-4 lg:p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-semibold mb-4">{{ $jadwalCeramah->judul_ceramah }}</h1>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h2 class="text-lg font-semibold mb-2">Informasi Utama</h2>
                            <p><strong>Nama Ustadz:</strong> {{ $jadwalCeramah->nama_ustadz }}</p>
                            <p><strong>Tanggal Ceramah:</strong> {{ \Carbon\Carbon::parse($jadwalCeramah->tanggal_ceramah)->locale('id')->isoFormat('D MMMM Y') }}</p>
                            <p><strong>Jam Mulai:</strong> {{ \Carbon\Carbon::parse($jadwalCeramah->jam_mulai)->format('H:i') }} WIB</p>
                            @if($jadwalCeramah->jam_selesai)
                                <p><strong>Jam Selesai:</strong> {{ \Carbon\Carbon::parse($jadwalCeramah->jam_selesai)->format('H:i') }} WIB</p>
                            @endif
                            <p><strong>Tempat Ceramah:</strong> {{ $jadwalCeramah->tempat_ceramah }}</p>
                            <p><strong>Kategori:</strong> {{ Str::headline(str_replace('_', ' ', $jadwalCeramah->kategori_ceramah)) }}</p>
                            @if($jadwalCeramah->link_streaming)
                                <p><strong>Link Streaming:</strong> <a href="{{ $jadwalCeramah->link_streaming }}" target="_blank" class="text-blue-500 hover:underline">{{ $jadwalCeramah->link_streaming }}</a></p>
                            @endif
                        </div>

                        <div>
                            <h2 class="text-lg font-semibold mb-2">Gambar</h2>
                            @if($jadwalCeramah->gambar)
                                <img src="{{ asset($jadwalCeramah->gambar) }}" alt="{{ $jadwalCeramah->judul_ceramah }}" class="max-w-full rounded-md shadow-md">
                            @else
                                <p>Tidak ada gambar.</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6">
                        <h2 class="text-lg font-semibold mb-2">Tentang Ceramah</h2>
                        @if($jadwalCeramah->tentang_ceramah)
                            <p class="text-gray-700">{{ $jadwalCeramah->tentang_ceramah }}</p>
                        @else
                            <p>Tidak ada deskripsi tentang ceramah.</p>
                        @endif
                    </div>

                    {{-- <div class="mt-8">
                        <a href="{{ route('admin.schedules.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Kembali ke Daftar Jadwal</a>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>