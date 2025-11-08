{{-- 1. STYLING: Pindahkan CSS Leaflet ke stack CSS --}}
@props(['visualData'])

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    {{-- CATATAN: Pastikan Tailwind CSS sudah dimuat di layout utama Anda --}}
@endpush

{{-- 2. KONTEN HTML --}}
<div class="bg-gray-50 font-sans pt-2 pb-1 px-4">

    <div style="padding: 20px;">
        <h2>Unggah Data Perjalanan (GPX & NMF)</h2>
        
        <form
        action="{{ route('perjalanan.store') }}"
        method="POST" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom: 15px;">
                <label for="nama_pengguna">Nama Pengguna:</label>
                <input type="text" id="nama_pengguna" name="nama_pengguna" required 
                       style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="gpx_file">File GPX (.gpx):</label>
                <input type="file" id="gpx_file" name="gpx_file" accept=".gpx" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="nmf_file">File Nemo (.nmf atau .txt log):</label>
                <input type="file" id="nmf_file" name="nmf_file" accept=".nmf,.txt" required>
            </div>
            
            @if ($errors->any())
                <div style="color: red; margin-bottom: 15px;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <button type="submit" 
                    style="padding: 10px 15px; background-color: blue; color: white; border: none; cursor: pointer;">
                Simpan Data
            </button>
        </form>
    </div>

    <h3 class="text-center text-xl font-semibold mt-5 mb-5">
        Visualisasi Kualitas RSRQ di Jalur Drive Test
    </h3>
    
    <div id="map" class="h-[600px] mx-auto my-5 border border-gray-300 shadow-lg"></div>

</div>


{{-- 3. SCRIPTS: Pindahkan JS Leaflet dan Logika Peta ke stack JS --}}
@push('scripts')
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        // Koordinat Pusat (Jakarta) dan Zoom Level
        var initialCoords = [-2.9105859, 104.8536157
];  
        var map = L.map('map').setView(initialCoords, 14);

        // Tambahkan Tile Layer (Peta Dasar)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

// ----------------------------------------------------------------------
// A. DATA SIMULASI TOWER (eNodeB)
// ----------------------------------------------------------------------
        
        var towerData = [
            [-6.200000, 106.816666, "eNB-001 | Pusat"],  
            [-6.195000, 106.825000, "eNB-002 | Timur Laut"],
            [-6.205000, 106.808000, "eNB-003 | Barat Daya"],
            [-6.190000, 106.810000, "eNB-004 | Utara"],
            [-6.210000, 106.820000, "eNB-005 | Selatan"],
            [-6.200000, 106.800000, "eNB-006 | Jauh Barat"],
        ];

        var towerIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34]
        });

        towerData.forEach(function(tower) {
            L.marker([tower[0], tower[1]], {icon: towerIcon})
                .addTo(map)
                .bindPopup("<b>" + tower[2] + "</b><br>Frekuensi: 1800 MHz");
        });

// ----------------------------------------------------------------------
// B. FUNGSI PENENTU WARNA RSRQ
// ----------------------------------------------------------------------
        
        function getColorByRSRQ(rsrq) {
            if (rsrq >= -10) {
                return '#2ECC71'; // Hijau (Sangat Baik)
            } else if (rsrq >= -15) {
                return '#F4D03F'; // Kuning (Baik/Sedang)
            } else {
                return '#E74C3C'; // Merah (Buruk)
            }
        }

// ... [Bagian A & B JavaScript Anda]

// ----------------------------------------------------------------------
// C. DATA DRIVE TEST (DINAMIS DARI DATABASE)
// ----------------------------------------------------------------------

// Inisialisasi array data dari PHP
var measurementPoints = []; 

// Menggunakan Blade untuk loop data dari Controller
@foreach($visualData as $data)
    measurementPoints.push([
        // Format: [latitude, longitude, rsrp, rsrq]
        {{ $data->latitude }}, 
        {{ $data->longitude }}, 
        {{ $data->rsrp ?? 'null' }}, // Jika RSRP null, gunakan null
        {{ $data->rsrq ?? 'null' }}, // Jika RSRQ null, gunakan null
        "PCI: {{ $data->pci ?? '-' }} | SINR: {{ $data->sinr ?? '-' }}" // Tambahan info popup
    ]);
@endforeach

// ----------------------------------------------------------------------
// D. LOGIKA SEGMENTED POLYLINE (Diperbarui untuk indeks baru)
// ----------------------------------------------------------------------

for (var i = 1; i < measurementPoints.length; i++) {
    var startPoint = measurementPoints[i - 1]; 
    var endPoint = measurementPoints[i];     
    
    // RSRQ sekarang berada di indeks 3
    var rsrq = endPoint[3]; 
    var segmentColor = getColorByRSRQ(rsrq);
    
    var segment = L.polyline([
        [startPoint[0], startPoint[1]], 
        [endPoint[0], endPoint[1]]      
    ], {
        color: segmentColor,
        weight: 6,
        opacity: 0.9
    }).addTo(map);

    segment.bindPopup("Segmen Drive Test<br>RSRQ di Titik Akhir: *" + rsrq + " dB*");
}

// ...

// ----------------------------------------------------------------------
// E. MARKER PENGUKURAN (Titik Data) (Diperbarui untuk indeks baru)
// ----------------------------------------------------------------------

var measurementIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
    iconSize: [15, 25], 
    iconAnchor: [7, 25],
    popupAnchor: [1, -15]
});

measurementPoints.forEach(function(point) {
    // RSRP adalah indeks 2, RSRQ adalah indeks 3, Info Tambahan adalah indeks 4
    var rsrp = point[2];
    var rsrq = point[3];
    var infoTambahan = point[4];

    L.marker([point[0], point[1]], {icon: measurementIcon})
        .addTo(map)
        .bindPopup("<b>Titik Data</b><br>RSRP: " + rsrp + " dBm<br>RSRQ: " + rsrq + " dB<br>" + infoTambahan);
});

// ----------------------------------------------------------------------
// F. TAMBAH LEGENDA (Menggunakan Kelas Tailwind)
// ----------------------------------------------------------------------
        
        var legend = L.control({position: 'bottomright'});

        legend.onAdd = function (map) {
            // Menggunakan kelas Tailwind: p-2, text-sm, bg-white, bg-opacity-80, shadow-md, rounded-md
            var div = L.DomUtil.create('div', 'info p-2 text-sm bg-white bg-opacity-80 shadow-md rounded-md'),
                rsrq_colors = ['#E74C3C', '#F4D03F', '#2ECC71'];
            var rsrq_labels = ["< -15 dB (Buruk)", "-15 s/d -10 dB (Sedang)", "> -10 dB (Baik)"];
            
            div.innerHTML += '<b class="font-bold">Kualitas RSRQ (dB)</b><br>';

            for (var i = 0; i < rsrq_colors.length; i++) {
                // Styling ikon legenda diterapkan inline di JS
                div.innerHTML +=
                    '<i style="background:' + rsrq_colors[i] + '; width: 18px; height: 18px; float: left; margin-right: 8px; opacity: 0.7; border-radius: 3px;"></i> ' +
                    rsrq_labels[i] + '<br>';
            }

            return div;
        };

        legend.addTo(map);

    </script>
@endpush