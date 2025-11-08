@props(['perjalananDetail', 'mapsData']) {{-- Ganti visualData dan centerCoords menjadi mapsData --}}

{{-- 1. STYLING: Pindahkan CSS Leaflet ke stack CSS dan Tambahkan Perbaikan CSS --}}
@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <style>
        .leaflet-control.info {
            z-index: 1000;
        }

        .info i {
            display: inline-block !important;
        }
    </style>
@endpush

{{-- 2. KONTEN HTML --}}

<div class="bg-gray-90 font-sans pt-2 pb-1 px-4">

    {{-- Form Unggah Data --}}
    <div class="mb-6 border p-4 rounded-lg bg-gray-50">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-md font-bold mb-3">Unggah Data Perjalanan (NMF) Baru</h4>
            <form action="{{ route('perjalanan.destroy', $perjalananDetail->id) }}" method="POST" class="inline-block"
                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data perjalanan ini? Data log yang terkait juga akan terhapus.');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-1.5 px-3 rounded text-xs uppercase transition ease-in-out duration-150">Hapus
                    Semua</button>
            </form>
        </div>
        <form action="{{ route('perjalanan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <input type="hidden" name="id_perjalanan" value="{{ $perjalananDetail->id_perjalanan ?? '' }}">
                <input type="hidden" name="nama_pengguna" value="{{ Auth::user()->name ?? 'User Default' }}">
                <input type="hidden" name="nama_tempat"
                    value="{{ $perjalananDetail->nama_tempat ?? 'Lokasi Default' }}">

                {{-- ID Perjalanan (Readonly) --}}
                <div>
                    <label for="id_perjalanan_display" class="block text-sm font-medium text-gray-700">Nama
                        ID:</label>
                    <input type="text" id="id_perjalanan_display" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border"
                        value="{{ $perjalananDetail->id_perjalanan ?? null }}" readonly>
                    {{-- Catatan: Jika ID ini digunakan untuk POST/STORE, pastikan Anda juga menyertakan input hidden aslinya atau ubah input ini agar sesuai. --}}
                </div>

                {{-- NEW: Status Input (Select) --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status Pengujian:</label>
                    <select id="status" name="status" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="Before">Before</option>
                        {{-- Nilai 'After' diset sebagai default terpilih --}}
                        <option value="After" selected>After</option>
                    </select>
                </div>

                {{-- File NMF Input --}}
                <div>
                    <label for="nmf_file" class="block text-sm font-medium text-gray-700">File Nemo (.nmf atau .txt
                        log):</label>
                    <input type="file" id="nmf_file" name="nmf_file" accept=".nmf,.txt" required
                        class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white p-2">
                </div>
            </div>

            {{-- Handling Errors --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <strong>Error!</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-300 transition ease-in-out duration-150 mt-2">
                Unggah Data Baru
            </button>
        </form>


    </div>

    <div class="mb-6 border p-6 rounded-xl bg-white shadow-lg">
        <h4 class="text-xl font-extrabold mb-4 text-indigo-700 border-b pb-2">
            <i class="fas fa-edit mr-2"></i> Edit Detail Sesi Perjalanan
        </h4>

        <form action="{{ route('perjalanan.update', $perjalananDetail->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">

                <!-- NAMA PENGGUNA -->
                <div>
                    <label for="nama_pengguna" class="block text-sm font-medium text-gray-700">Nama Pengguna:</label>
                    <input type="text" id="nama_pengguna" name="nama_pengguna" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        value="{{ old('nama_pengguna', $perjalananDetail->nama_pengguna ?? (Auth::user()->name ?? 'User Default')) }}">
                </div>

                <!-- NAMA TEMPAT / LOKASI -->
                <div>
                    <label for="nama_tempat" class="block text-sm font-medium text-gray-700">Nama Tempat /
                        Lokasi:</label>
                    <input type="text" id="nama_tempat" name="nama_tempat" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        value="{{ old('nama_tempat', $perjalananDetail->nama_tempat ?? null) }}">
                </div>

                <!-- ID PERJALANAN (Display Only - Read from $perjalananDetail) -->
                <div>
                    <label for="display_id_perjalanan" class="block text-sm font-medium text-gray-700">ID
                        Perjalanan:</label>
                    <input type="text" id="display_id_perjalanan"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border bg-gray-200 cursor-not-allowed"
                        value="{{ $perjalananDetail->id ?? 'ID Not Found' }}" readonly>
                    <!-- Nilai ini hanya untuk tampilan, bukan untuk dikirim (karena ID sudah di URL) -->
                </div>
            </div>

            {{-- Handling Validation Errors --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm"
                    role="alert">
                    <strong class="font-semibold">Perhatian!</strong> Ada kesalahan pada input:
                    <ul class="list-disc ml-5 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-orange-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-500 active:bg-orange-500 focus:outline-none focus:border-orange-900 focus:ring focus:ring-orange-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                Simpan Perubahan Detail
            </button>
        </form>
    </div>


    {{-- LOOP untuk setiap log data dan buat MAP container UNIK --}}
    <div class="mt-8">
        <h3 class="text-2xl font-extrabold text-gray-800 mb-6 border-b pb-2">Visualisasi Data Log</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-4">
            <div class="grid grid-cols-1 gap-4">
                 @forelse (collect($mapsData)->where('status', 'Before') as $mapItem)
                    <x-telkominfra.map.item-card :mapItem="$mapItem" />
                @empty
                    <div class="lg:col-span-3">
                        <p class="text-center text-gray-500 py-10 border rounded-lg bg-gray-50">
                            Tidak ada data sinyal 'Before' yang ditemukan untuk perjalanan ini.
                        </p>
                    </div>
                @endforelse
            </div>
            <div class="grid grid-cols-1 gap-4">
                 @forelse (collect($mapsData)->where('status', 'After') as $mapItem)
                    <x-telkominfra.map.item-card :mapItem="$mapItem" />
                @empty
                    <div class="lg:col-span-3">
                        <p class="text-center text-gray-500 py-10 border rounded-lg bg-gray-50">
                            Tidak ada data sinyal 'After' yang ditemukan untuk perjalanan ini.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

{{-- 3. SCRIPTS: Pindahkan JS Leaflet dan Logika Peta ke stack JS --}}
@push('scripts')
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        // Data maps yang sekarang berisi array dari semua log
        var allMapsData = @json($mapsData ?? []);

        // Pastikan DOM sudah dimuat sepenuhnya sebelum menjalankan inisialisasi peta.
        document.addEventListener('DOMContentLoaded', function() {

            // ----------------------------------------------------------------------
            // B. FUNGSI PENENTU WARNA RSRQ (Tetap sama)
            // ----------------------------------------------------------------------
            function getColorByRSRQ(rsrq) {
                if (rsrq >= -70) {
                    return '#2ECC71'; // Hijau (Sangat Baik)
                } else if (rsrq >= -90) {
                    return '#F4D03F'; // Kuning (Baik/Sedang)
                } else {
                    return '#E74C3C'; // Merah (Buruk)
                }
            }

            // ----------------------------------------------------------------------
            // C & D & E & F. LOOPING DAN INISIALISASI PETA UNTUK SETIAP LOG
            // ----------------------------------------------------------------------
            allMapsData.forEach(function(mapItem) {

                // Ambil ID unik dan data
                var mapId = 'map-' + mapItem.id;
                var centerCoords = mapItem.centerCoords;
                var visualData = mapItem.visualData;

                // 1. Inisialisasi Peta Baru
                // Periksa: Pastikan elemen peta sudah tersedia
                var mapElement = document.getElementById(mapId);
                if (!mapElement) {
                    // console.error("Elemen peta tidak ditemukan: " + mapId);
                    return; // Lewati jika elemen tidak ada di DOM
                }

                // Inisialisasi Peta
                // Menggunakan titik tengah yang didapat dari Controller
                var map = L.map(mapId).setView(centerCoords, 18);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                // --- Proses Data Visual (Polyline & Marker) hanya jika ada data ---
                if (visualData.length > 0) {
                    // 2. Format Ulang Data Titik Pengukuran
                    var measurementPoints = visualData.map(function(data) {
                        return [
                            data.latitude,
                            data.longitude,
                            data.rsrp,
                            data.rsrq,
                            "PCI: " + (data.pci ?? 'N/A') + " | SINR: " + (data.sinr ?? 'N/A')
                        ];
                    });

                    // 3. LOGIKA SEGMENTED POLYLINE 
                    for (var i = 1; i < measurementPoints.length; i++) {
                        var startPoint = measurementPoints[i - 1];
                        var endPoint = measurementPoints[i];

                        var rsrq = endPoint[3];
                        var latitude = endPoint[0];
                        var longitude = endPoint[1];
                        var segmentColor = getColorByRSRQ(rsrq);

                        var segment = L.polyline([
                            [startPoint[0], startPoint[1]],
                            [endPoint[0], endPoint[1]]
                        ], {
                            color: segmentColor,
                            weight: 6,
                            opacity: 0.9
                        }).addTo(map);

                        segment.bindPopup(
                            "Segmen Drive Test<br>" +
                            "RSRQ di Titik Akhir: <b>" + rsrq + " dB</b><br>" +
                            "Koordinat: " + latitude.toFixed(6) + ", " + longitude.toFixed(6)
                        );
                    }

                    // 4. MARKER PENGUKURAN (Titik Data)
                    var measurementIcon = L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
                        iconSize: [0, 0], // Marker size zeroed out because we mainly show polyline
                        iconAnchor: [7, 25],
                        popupAnchor: [1, -90]
                    });

                    measurementPoints.forEach(function(point) {
                        var rsrp = point[2];
                        var rsrq = point[3];
                        var infoTambahan = point[4];

                        L.marker([point[0], point[1]], {
                                icon: measurementIcon
                            })
                            .addTo(map)
                            .bindPopup(
                                "<b>Titik Data</b><br>" +
                                "RSRP: " + rsrp + " dBm<br>" +
                                "RSRQ: " + rsrq + " dB<br>" +
                                infoTambahan + "<br>" +
                                "Koordinat: " + point[0].toFixed(6) + ", " + point[1].toFixed(6)
                            );
                    });

                    // OPTIONAL: Sesuaikan batas peta (fit bounds)
                    var latLngs = measurementPoints.map(p => [p[0], p[1]]);
                    map.fitBounds(L.polyline(latLngs).getBounds());
                }


                // 5. TAMBAH LEGENDA 
                var legend = L.control({
                    position: 'bottomright'
                });
                legend.onAdd = function(map) {
                    var div = L.DomUtil.create('div',
                            'info p-2 text-sm bg-white bg-opacity-90 shadow-md rounded-md'),
                        rsrq_colors = ['#E74C3C', '#F4D03F', '#2ECC71'];
                    var rsrq_labels = ["< -90 dB (Buruk)", "-90 s/d -70 dB (Sedang)",
                        "> -70 dB (Baik)"
                    ];

                    div.innerHTML += '<b class="font-bold">Kualitas RSRQ (dB)</b><br>';

                    for (var i = 0; i < rsrq_colors.length; i++) {
                        div.innerHTML +=
                            '<i style="background:' + rsrq_colors[i] +
                            '; width: 18px; height: 18px; float: left; margin-right: 8px; opacity: 0.7; border-radius: 3px;"></i> ' +
                            rsrq_labels[i] + '<br>';
                    }

                    return div;
                };
                legend.addTo(map);

            }); // END of allMapsData.forEach()
        }); // END of DOMContentLoaded
    </script>
@endpush
