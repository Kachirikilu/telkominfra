<?php

namespace App\Http\Controllers\Telkominfra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Perjalanan;
use App\Models\PerjalananData;
use App\Models\PengukuranSinyal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File; 
use App\Http\Controllers\Telkominfra\FileTelkominfraController;

class ViewTelkominfraController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Perjalanan::query();

        if ($search) {
            $parsedDate = null;
            try {
                $parsedDate = Carbon::parse($search);
            } catch (\Exception $e) {
                $parsedDate = null;
            }

            $query->where(function ($q) use ($search, $parsedDate) {
                $q->where('id', 'like', '%' . $search . '%')
                  ->where('id_perjalanan', 'like', '%' . $search . '%')
                  ->orWhere('nama_pengguna', 'like', '%' . $search . '%')
                  ->orWhere('nama_tempat', 'like', '%' . $search . '%');
                if ($parsedDate) {
                    $q->orWhereBetween('created_at', [
                        $parsedDate->copy()->startOfDay(), 
                        $parsedDate->copy()->endOfDay(),
                    ]);
                }
            });
        }
        $perjalanans = $query->paginate(10);
        $perjalanans->appends(['search' => $search]);

        if ($request->wantsJson() || $request->is('api/*')) {
            $payload = [
                'perjalanans' => $perjalanans,
                'search' => $search,
            ];
            $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
            return response($json, 200)
                ->header('Content-Type', 'application/json')
                ->header('Content-Length', strlen($json));
        }
        return view('telkominfra', [
            'perjalanans' => $perjalanans, 
            'search' => $search,
        ]);
    }


    public function show(Request $request, string $id)
    {
        $perjalanan = Perjalanan::where('id', $id)->firstOrFail();
        $perjalananDatas = PerjalananData::where('perjalanan_id', $perjalanan->id)->get(); 
        
        $mapsData = []; 
        $mergedData = [
            'Before' => [],
            'After' => [],
        ];

        $avgAccumulators = [
            'Before' => [
                'rsrp_sum' => null, 'rsrp_count' => null,
                'rssi_sum' => null, 'rssi_count' => null,
                'rsrq_sum' => null, 'rsrq_count' => null,
                'sinr_sum' => null, 'sinr_count' => null,
            ],
            'After'  => [
                'rsrp_sum' => null, 'rsrp_count' => null,
                'rssi_sum' => null, 'rssi_count' => null,
                'rsrq_sum' => null, 'rsrq_count' => null,
                'sinr_sum' => null, 'sinr_count' => null,
            ],
        ];

        $individualMaps = [];

        // === LOOP UNTUK SETIAP FILE ===
        foreach ($perjalananDatas as $dataItem) {
            $visualData = [];
            $centerCoords = [-2.9105859, 104.8536157];

            try {
                $nmfPath = public_path('uploads/perjalanan/' . $dataItem->file_nmf);
                $parser = new FileTelkominfraController();
                $dataSinyal = $parser->parseNmfSinyal($nmfPath, $dataItem->id); 

                foreach ($dataSinyal as $sinyal) {
                    if ($sinyal['latitude'] === null || $sinyal['longitude'] === null) {
                        continue; 
                    }

                    $status = $dataItem->status;

                    // --- Akumulasi rata-rata ---
                    if (isset($avgAccumulators[$status])) {
                        foreach (['rsrp', 'rssi', 'rsrq', 'sinr'] as $metric) {
                            if (isset($sinyal[$metric])) {
                                $avgAccumulators[$status]["{$metric}_sum"] += (float)$sinyal[$metric];
                                $avgAccumulators[$status]["{$metric}_count"]++;
                            }
                        }
                    }

                    // --- Format waktu ---
                    $rawTimestamp = $sinyal['timestamp_waktu'] ?? null;
                    $formattedTimestamp = null;
                    if ($rawTimestamp) {
                        try {
                            $carbonTime = Carbon::parse($rawTimestamp);
                            $formattedTimestamp = $carbonTime->format('H:i:s d/m/y');
                        } catch (\Exception $e) {
                            $formattedTimestamp = 'Invalid Time';
                        }
                    }

                    $point = [
                        'id'        => $dataItem->id,
                        'latitude'  => (float) $sinyal['latitude'],
                        'longitude' => (float) $sinyal['longitude'],
                        'rsrp'      => $sinyal['rsrp'] ?? null,
                        'rssi'      => $sinyal['rssi'] ?? null,
                        'rsrq'      => $sinyal['rsrq'] ?? null,
                        'sinr'      => $sinyal['sinr'] ?? null,
                        'pci'       => $sinyal['pci'] ?? null,
                        'earfcn'    => $sinyal['earfcn'] ?? null,
                        'band'      => $sinyal['band'] ?? null,
                        'frekuensi' => $sinyal['frekuensi'] ?? null,
                        'bandwidth' => $sinyal['bandwidth'] ?? null,
                        'n_value'   => $sinyal['n_value'] ?? null,
                        'timestamp_waktu' => $formattedTimestamp,
                        'cell_id' => $sinyal['cell_id'] ?? null
                    ];

                    $visualData[] = $point;
                    $mergedData[$status][] = $point;
                }

                if (!empty($visualData)) {
                    $latitudes = array_column($visualData, 'latitude');
                    $longitudes = array_column($visualData, 'longitude');

                    $minLat = min($latitudes);
                    $maxLat = max($latitudes);
                    $minLon = min($longitudes);
                    $maxLon = max($longitudes);

                    $centerCoords = [
                        ($minLat + $maxLat) / 2,
                        ($minLon + $maxLon) / 2,
                    ];
                }

            } catch (\Exception $e) {
                Log::error("Gagal memproses file NMF untuk PerjalananData ID: " . $dataItem->id . ". Error: " . $e->getMessage());
            }

            // Simpan ke data per file
            $individualMaps[] = [
                'id' => $dataItem->id,
                'centerCoords' => $centerCoords,
                'visualData' => $visualData,
                'fileName' => $dataItem->file_nmf,
                'perangkat' => $dataItem->perangkat,
                'status' => $dataItem->status,
            ];
        }

        // === TAMBAHKAN DATA GABUNGAN DULU ===
        $mergedMaps = [];
        foreach (['Before', 'After'] as $status) {
        if (!empty($mergedData[$status])) {
            $latitudes = array_column($mergedData[$status], 'latitude');
            $longitudes = array_column($mergedData[$status], 'longitude');

            $minLat = min($latitudes);
            $maxLat = max($latitudes);
            $minLon = min($longitudes);
            $maxLon = max($longitudes);

            $centerCoords = [
                ($minLat + $maxLat) / 2,
                ($minLon + $maxLon) / 2,
            ];

            $fileName = $status === 'Before' ? 'Sebelum' : 'Sesudah';

            $mergedMaps[] = [
                'id' => "Merged_{$status}",
                'centerCoords' => $centerCoords,
                'visualData' => $mergedData[$status],
                'fileName' => "Gabungan Data {$fileName} Maintenance",
                'perangkat' => 'Semua Perangkat',
                'status' => $status,
            ];
        }
    }

        // === GABUNGKAN: gabungan dulu, lalu per file ===
        $mapsData = array_merge($mergedMaps, $individualMaps);

        // === HITUNG RATA-RATA ===
        $signalAverages = [];
        foreach ($avgAccumulators as $status => $data) {
            $signalAverages[$status] = [
                'rsrp_avg' => $data['rsrp_count'] > 0 ? round($data['rsrp_sum'] / $data['rsrp_count'], 2) : 0,
                'rssi_avg' => $data['rssi_count'] > 0 ? round($data['rssi_sum'] / $data['rssi_count'], 2) : 0,
                'rsrq_avg' => $data['rsrq_count'] > 0 ? round($data['rsrq_sum'] / $data['rsrq_count'], 2) : 0,
                'sinr_avg' => $data['sinr_count'] > 0 ? round($data['sinr_sum'] / $data['sinr_count'], 2) : 0,
            ];
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            $payload = [
                'status' => 'success',
                'perjalanan' => $perjalanan,
                'maps_data' => $mapsData,
                'signal_averages' => $signalAverages,
            ];
            $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
            return response($json, 200)
                ->header('Content-Type', 'application/json')
                ->header('Content-Length', strlen($json));
        }


        return view('telkominfra-show', [
            'perjalananDetail' => $perjalanan,
            'mapsData' => $mapsData, 
            'signalAverages' => $signalAverages,
        ]);
    }
    public function show2(Request $request, string $id)
    {
        $perjalanan = Perjalanan::where('id', $id)->firstOrFail();
        $perjalananDatas = PerjalananData::where('perjalanan_id', $perjalanan->id)->get();

        $mapsData = [];
        $mergedData = [
            'Before' => [],
            'After'  => [],
        ];

        $avgAccumulators = [
            'Before' => [
                'rsrp_sum' => 0, 'rsrp_count' => 0,
                'rssi_sum' => 0, 'rssi_count' => 0,
                'rsrq_sum' => 0, 'rsrq_count' => 0,
                'sinr_sum' => 0, 'sinr_count' => 0,
            ],
            'After'  => [
                'rsrp_sum' => 0, 'rsrp_count' => 0,
                'rssi_sum' => 0, 'rssi_count' => 0,
                'rsrq_sum' => 0, 'rsrq_count' => 0,
                'sinr_sum' => 0, 'sinr_count' => 0,
            ],
        ];

        $individualMaps = [];

        // === LOOP UNTUK SETIAP DATA PERJALANAN ===
        foreach ($perjalananDatas as $dataItem) {
            $visualData = [];
            $centerCoords = [-2.9105859, 104.8536157];

            try {
                // 🔹 Ambil data sinyal dari database, bukan dari file
                // $dataSinyal = PengukuranSinyal::where('perjalanan_id', $dataItem->id)->get();
                $nmfPath = public_path('uploads/perjalanan/' . $dataItem->file_nmf);
                $parser = new FileTelkominfraController();
                $dataSinyal = $parser->parseNmfSinyal($nmfPath, $dataItem->id);

                foreach ($dataSinyal as $sinyal) {
                    if ($sinyal->latitude === null || $sinyal->longitude === null) {
                        continue;
                    }

                    $status = $dataItem->status;

                    // --- Akumulasi rata-rata ---
                    if (isset($avgAccumulators[$status])) {
                        foreach (['rsrp', 'rssi', 'rsrq', 'sinr'] as $metric) {
                            if (!is_null($sinyal->$metric)) {
                                $avgAccumulators[$status]["{$metric}_sum"] += (float)$sinyal->$metric;
                                $avgAccumulators[$status]["{$metric}_count"]++;
                            }
                        }
                    }

                    // --- Format waktu ---
                    $formattedTimestamp = null;
                    if (!empty($sinyal->timestamp_waktu)) {
                        try {
                            $carbonTime = Carbon::parse($sinyal->timestamp_waktu);
                            $formattedTimestamp = $carbonTime->format('H:i:s d/m/y');
                        } catch (\Exception $e) {
                            $formattedTimestamp = 'Invalid Time';
                        }
                    }

                    // --- Susun data titik untuk peta ---
                    $point = [
                        'id'        => $dataItem->id,
                        'latitude'  => (float)$sinyal->latitude,
                        'longitude' => (float)$sinyal->longitude,
                        'rsrp'      => $sinyal->rsrp,
                        'rssi'      => $sinyal->rssi,
                        'rsrq'      => $sinyal->rsrq,
                        'sinr'      => $sinyal->sinr,
                        'pci'       => $sinyal->pci,
                        'earfcn'    => $sinyal->earfcn,
                        'band'      => $sinyal->band,
                        'frekuensi' => $sinyal->frekuensi,
                        'bandwidth' => $sinyal->bandwidth,
                        'n_value'   => $sinyal->n_value,
                        'timestamp_waktu' => $formattedTimestamp,
                        'cell_id'   => $sinyal->cell_id,
                    ];

                    $visualData[] = $point;
                    $mergedData[$status][] = $point;
                }

                // 🔹 Hitung koordinat tengah (untuk fokus peta)
                if (!empty($visualData)) {
                    $latitudes = array_column($visualData, 'latitude');
                    $longitudes = array_column($visualData, 'longitude');

                    $minLat = min($latitudes);
                    $maxLat = max($latitudes);
                    $minLon = min($longitudes);
                    $maxLon = max($longitudes);

                    $centerCoords = [
                        ($minLat + $maxLat) / 2,
                        ($minLon + $maxLon) / 2,
                    ];
                }
            } catch (\Exception $e) {
                Log::error("Gagal mengambil data sinyal dari DB untuk perjalananData ID: {$dataItem->id}. Error: " . $e->getMessage());
            }

            // Simpan per dataset (per file/per status)
            $individualMaps[] = [
                'id' => $dataItem->id,
                'centerCoords' => $centerCoords,
                'visualData' => $visualData,
                'fileName' => $dataItem->file_nmf,
                'perangkat' => $dataItem->perangkat,
                'status' => $dataItem->status,
            ];
        }

        // === TAMBAHKAN DATA GABUNGAN (BEFORE & AFTER) ===
        $mergedMaps = [];
        foreach (['Before', 'After'] as $status) {
            if (!empty($mergedData[$status])) {
                $latitudes = array_column($mergedData[$status], 'latitude');
                $longitudes = array_column($mergedData[$status], 'longitude');

                $minLat = min($latitudes);
                $maxLat = max($latitudes);
                $minLon = min($longitudes);
                $maxLon = max($longitudes);

                $centerCoords = [
                    ($minLat + $maxLat) / 2,
                    ($minLon + $maxLon) / 2,
                ];

                $fileName = $status === 'Before' ? 'Sebelum' : 'Sesudah';

                $mergedMaps[] = [
                    'id' => "Merged_{$status}",
                    'centerCoords' => $centerCoords,
                    'visualData' => $mergedData[$status],
                    'fileName' => "Gabungan Data {$fileName} Maintenance",
                    'perangkat' => 'Semua Perangkat',
                    'status' => $status,
                ];
            }
        }

        // === GABUNGKAN SEMUA DATA UNTUK VIEW ===
        $mapsData = array_merge($mergedMaps, $individualMaps);

        // === HITUNG RATA-RATA SIGNAL ===
        $signalAverages = [];
        foreach ($avgAccumulators as $status => $data) {
            $signalAverages[$status] = [
                'rsrp_avg' => $data['rsrp_count'] > 0 ? round($data['rsrp_sum'] / $data['rsrp_count'], 2) : 0,
                'rssi_avg' => $data['rssi_count'] > 0 ? round($data['rssi_sum'] / $data['rssi_count'], 2) : 0,
                'rsrq_avg' => $data['rsrq_count'] > 0 ? round($data['rsrq_sum'] / $data['rsrq_count'], 2) : 0,
                'sinr_avg' => $data['sinr_count'] > 0 ? round($data['sinr_sum'] / $data['sinr_count'], 2) : 0,
            ];
        }

        // === API Response (jika permintaan JSON) ===
        if ($request->wantsJson() || $request->is('api/*')) {
            $payload = [
                'status' => 'success',
                'perjalanan' => $perjalanan,
                'maps_data' => $mapsData,
                'signal_averages' => $signalAverages,
            ];
            $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
            return response($json, 200)
                ->header('Content-Type', 'application/json')
                ->header('Content-Length', strlen($json));
        }

        // === View Blade ===
        return view('telkominfra-show', [
            'perjalananDetail' => $perjalanan,
            'mapsData' => $mapsData,
            'signalAverages' => $signalAverages,
        ]);
    }
        

}