@props(['perjalananDetail', 'visualData', 'centerCoords'])

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

    <div style="padding: 20px;">
        <h2>Unggah Data Perjalanan (NMF)</h2>
        
        <form
        action="{{ route('perjalanan.store') }}"
        method="POST" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom: 15px;">
                <label for="id_perjalanan">ID Perjalanan:</label>
                <input value="{{ $perjalananDetail->id_perjalanan ?? null }}" type="text" id="id_perjalanan" name="id_perjalanan" 
                        style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="nama_pengguna">Nama Pengguna:</label>
                <input value="{{ Auth::user()->name  }}" type="text" id="nama_pengguna" name="nama_pengguna" required 
                        style="width: 100%; padding: 8px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label for="nama_tempat">Nama Tempat:</label>
                <input value="{{ $perjalananDetail->nama_tempat ?? null }}" type="text" id="nama_tempat" name="nama_tempat" required 
                        style="width: 100%; padding: 8px;">
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
        // Memastikan variabel centerCoords di-set
        var centerCoords = @json($centerCoords ?? [-6.200000, 106.816666]); 
        var map = L.map('map').setView(centerCoords, 18);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        

// ----------------------------------------------------------------------
// A. DATA SIMULASI TOWER (eNodeB) - Di-comment, tidak ada perubahan
// ----------------------------------------------------------------------
        
// ----------------------------------------------------------------------
// B. FUNGSI PENENTU WARNA RSRQ
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
// C. DATA DRIVE TEST (DINAMIS DARI DATABASE)
// ----------------------------------------------------------------------

// Inisialisasi array data dari PHP
var measurementPoints = []; 

@foreach($visualData as $data)
    measurementPoints.push([
        {{ json_encode($data['latitude']) }}, 
        {{ json_encode($data['longitude']) }}, 
        {{ json_encode($data['rsrp'] ?? null) }}, 
        {{ json_encode($data['rsrq'] ?? null) }}, 
        {!! json_encode("PCI: {$data['pci']} | SINR: {$data['sinr']}") !!}
    ]);
@endforeach


// ----------------------------------------------------------------------
// D. LOGIKA SEGMENTED POLYLINE 
// ----------------------------------------------------------------------

for (var i = 1; i < measurementPoints.length; i++) {
    var startPoint = measurementPoints[i - 1]; 
    var endPoint = measurementPoints[i]; 
    
    // RSRQ sekarang berada di indeks 3
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

// ----------------------------------------------------------------------
// E. MARKER PENGUKURAN (Titik Data)
// ----------------------------------------------------------------------

var measurementIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
    iconSize: [0, 0], 
    iconAnchor: [7, 25],
    popupAnchor: [1, -90]
});

measurementPoints.forEach(function(point) {
    // RSRP adalah indeks 2, RSRQ adalah indeks 3, Info Tambahan adalah indeks 4
    var rsrp = point[2];
    var rsrq = point[3];
    var infoTambahan = point[4];

    L.marker([point[0], point[1]], {icon: measurementIcon})
    .addTo(map)
    .bindPopup(
        "<b>Titik Data</b><br>" +
        "RSRP: " + rsrp + " dBm<br>" +
        "RSRQ: " + rsrq + " dB<br>" +
        infoTambahan + "<br>" +
        "Koordinat: " + point[0].toFixed(6) + ", " + point[1].toFixed(6)
    );

});

// ----------------------------------------------------------------------
// F. TAMBAH LEGENDA (Menggunakan Kelas Tailwind)
// ----------------------------------------------------------------------
        
        var legend = L.control({position: 'bottomright'});

        legend.onAdd = function (map) {
            // Menggunakan kelas Tailwind: p-2, text-sm, bg-white, bg-opacity-90, shadow-md, rounded-md
            // Kelas 'info' digunakan di CSS kustom di atas
            var div = L.DomUtil.create('div', 'info p-2 text-sm bg-white bg-opacity-90 shadow-md rounded-md'),
                rsrq_colors = ['#E74C3C', '#F4D03F', '#2ECC71'];
            var rsrq_labels = ["< -90 dB (Buruk)", "-90 s/d -70 dB (Sedang)", "> -70 dB (Baik)"];
            
            div.innerHTML += '<b class="font-bold">Kualitas RSRQ (dB)</b><br>';

            for (var i = 0; i < rsrq_colors.length; i++) {
                // Styling ikon legenda diterapkan inline di JS (tag <i>)
                div.innerHTML +=
                    '<i style="background:' + rsrq_colors[i] + '; width: 18px; height: 18px; float: left; margin-right: 8px; opacity: 0.7; border-radius: 3px;"></i> ' +
                    rsrq_labels[i] + '<br>';
            }

            return div;
        };

        legend.addTo(map);

    </script>
@endpush