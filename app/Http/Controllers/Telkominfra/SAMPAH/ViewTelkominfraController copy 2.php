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


    // public function show(Request $request, string $id)
    // {
    //     $perjalanan = Perjalanan::where('id', $id)->firstOrFail();
    //     $perjalananDatas = PerjalananData::where('perjalanan_id', $perjalanan->id)->get();

    //     $mapsData = [];
    //     $mergedData = [
    //         'Before' => [],
    //         'After'  => [],
    //     ];

    //     $avgAccumulators = [
    //         'Before' => [
    //             'rsrp_sum' => 0, 'rsrp_count' => 0,
    //             'rssi_sum' => 0, 'rssi_count' => 0,
    //             'rsrq_sum' => 0, 'rsrq_count' => 0,
    //             'sinr_sum' => 0, 'sinr_count' => 0,
    //         ],
    //         'After'  => [
    //             'rsrp_sum' => 0, 'rsrp_count' => 0,
    //             'rssi_sum' => 0, 'rssi_count' => 0,
    //             'rsrq_sum' => 0, 'rsrq_count' => 0,
    //             'sinr_sum' => 0, 'sinr_count' => 0,
    //         ],
    //     ];

    //     $individualMaps = [];

    //     // === LOOP UNTUK SETIAP DATA PERJALANAN ===
    //     foreach ($perjalananDatas as $dataItem) {
    //         $visualData = [];
    //         $centerCoords = [-2.9105859, 104.8536157];

    //         try {
    //             // 🔹 Ambil data sinyal dari database atau dari file
                
    //             $perjalananData = PerjalananData::where('perjalanan_id', $perjalanan->id)->where('status', $dataItem->status)->get();
    //             $dataSinyal = PengukuranSinyal::where('perjalanan_data_id', $perjalananData->id)->get();

    //             // $nmfPath = public_path('uploads/perjalanan/' . $dataItem->file_nmf);

    //             if (!empty($nmfPath)) {
    //                 $parser = new FileTelkominfraController();
    //                 $dataSinyal = $parser->parseNmfSinyal($nmfPath, $dataItem->id);
    //             }

    //             foreach ($dataSinyal as $sinyal) {
    //                 if ((!$sinyal['latitude'] && $sinyal['longitude'])
    //                     &&($sinyal->latitude && $sinyal->longitude)) {
    //                     $latitudeData = $sinyal->latitude;
    //                     $longitudeData = $sinyal->longitude;
    //                 } else {
    //                     $latitudeData = $sinyal['latitude'];
    //                     $longitudeData = $sinyal['longitude'];
    //                 }
    //                 if ($latitudeData === null || $longitudeData === null) {
    //                     continue; 
    //                 }

    //                 $status = $dataItem->status;

    //                 // --- Akumulasi rata-rata ---
    //                 if (isset($avgAccumulators[$status])) {
    //                     foreach (['rsrp', 'rssi', 'rsrq', 'sinr'] as $metric) {
    //                         if (isset($sinyal[$metric])) {
    //                             $avgAccumulators[$status]["{$metric}_sum"] += (float)$sinyal[$metric];
    //                             $avgAccumulators[$status]["{$metric}_count"]++;
    //                         }
    //                     }
    //                 }

    //                 // --- Format waktu ---
    //                 $rawTimestamp = $sinyal['timestamp_waktu'] ?? null;
    //                 $formattedTimestamp = null;
    //                 if ($rawTimestamp) {
    //                     try {
    //                         $carbonTime = Carbon::parse($rawTimestamp);
    //                         $formattedTimestamp = $carbonTime->format('H:i:s d/m/y');
    //                     } catch (\Exception $e) {
    //                         $formattedTimestamp = 'Invalid Time';
    //                     }
    //                 }

    //                 // --- Susun data titik untuk peta ---
    //                 $point = [
    //                     'id'        => $dataItem->id,
    //                     'latitude'  => (float) $sinyal['latitude'] ?? (float)$sinyal->latitude,
    //                     'longitude' => (float) $sinyal['longitude'] ?? (float)$sinyal->longitude,
    //                     'rsrp'      => $sinyal['rsrp'] ?? $sinyal->rsrp ?? null,
    //                     'rssi'      => $sinyal['rssi'] ?? $sinyal->rssi ?? null,
    //                     'rsrq'      => $sinyal['rsrq'] ?? $sinyal->rsrq ?? null,
    //                     'sinr'      => $sinyal['sinr'] ?? $sinyal->sinr ??null,
    //                     'cell_id'   => $sinyal['cell_id'] ?? $sinyal->cell_id ?? null,
    //                     'pci'       => $sinyal['pci'] ?? $sinyal->pci ?? null,
    //                     'earfcn'    => $sinyal['earfcn'] ?? $sinyal->earfcn ?? null,
    //                     'band'      => $sinyal['band'] ?? $sinyal->band ?? null,
    //                     'frekuensi' => $sinyal['frekuensi'] ?? $sinyal->frekuensi ?? null,
    //                     'bandwidth' => $sinyal['bandwidth'] ?? $sinyal->bandwidth ?? null,
    //                     'n_value'   => $sinyal['n_value'] ?? $sinyal->n_value ?? null,
    //                     'timestamp_waktu' => $formattedTimestamp,
    //                 ];

    //                 $visualData[] = $point;
    //                 $mergedData[$status][] = $point;
    //             }

    //             // 🔹 Hitung koordinat tengah (untuk fokus peta)
    //             if (!empty($visualData)) {
    //                 $latitudes = array_column($visualData, 'latitude');
    //                 $longitudes = array_column($visualData, 'longitude');

    //                 $minLat = min($latitudes);
    //                 $maxLat = max($latitudes);
    //                 $minLon = min($longitudes);
    //                 $maxLon = max($longitudes);

    //                 $centerCoords = [
    //                     ($minLat + $maxLat) / 2,
    //                     ($minLon + $maxLon) / 2,
    //                 ];
    //             }
    //         } catch (\Exception $e) {
    //             Log::error("Gagal mengambil data sinyal dari DB untuk perjalananData ID: {$dataItem->id}. Error: " . $e->getMessage());
    //         }

    //         // Simpan per dataset (per file/per status)
    //         $individualMaps[] = [
    //             'id' => $dataItem->id,
    //             'centerCoords' => $centerCoords,
    //             'visualData' => $visualData,
    //             'fileName' => $dataItem->file_nmf,
    //             'perangkat' => $dataItem->perangkat,
    //             'status' => $dataItem->status,
    //         ];
    //     }

    //     // === TAMBAHKAN DATA GABUNGAN (BEFORE & AFTER) ===
    //     $mergedMaps = [];
    //     foreach (['Before', 'After'] as $status) {
    //         if (!empty($mergedData[$status])) {
    //             $latitudes = array_column($mergedData[$status], 'latitude');
    //             $longitudes = array_column($mergedData[$status], 'longitude');

    //             $minLat = min($latitudes);
    //             $maxLat = max($latitudes);
    //             $minLon = min($longitudes);
    //             $maxLon = max($longitudes);

    //             $centerCoords = [
    //                 ($minLat + $maxLat) / 2,
    //                 ($minLon + $maxLon) / 2,
    //             ];

    //             $fileName = $status === 'Before' ? 'Sebelum' : 'Sesudah';

    //             $mergedMaps[] = [
    //                 'id' => "Merged_{$status}",
    //                 'centerCoords' => $centerCoords,
    //                 'visualData' => $mergedData[$status],
    //                 'fileName' => "Gabungan Data {$fileName} Maintenance",
    //                 'perangkat' => 'Semua Perangkat',
    //                 'status' => $status,
    //             ];
    //         }
    //     }

    //     // === GABUNGKAN SEMUA DATA UNTUK VIEW ===
    //     $mapsData = array_merge($mergedMaps, $individualMaps);

    //     // === HITUNG RATA-RATA SIGNAL ===
    //     $signalAverages = [];
    //     foreach ($avgAccumulators as $status => $data) {
    //         $signalAverages[$status] = [
    //             'rsrp_avg' => $data['rsrp_count'] > 0 ? round($data['rsrp_sum'] / $data['rsrp_count'], 2) : 0,
    //             'rssi_avg' => $data['rssi_count'] > 0 ? round($data['rssi_sum'] / $data['rssi_count'], 2) : 0,
    //             'rsrq_avg' => $data['rsrq_count'] > 0 ? round($data['rsrq_sum'] / $data['rsrq_count'], 2) : 0,
    //             'sinr_avg' => $data['sinr_count'] > 0 ? round($data['sinr_sum'] / $data['sinr_count'], 2) : 0,
    //         ];
    //     }

    //     // === API Response (jika permintaan JSON) ===
    //     if ($request->wantsJson() || $request->is('api/*')) {
    //         $payload = [
    //             'status' => 'success',
    //             'perjalanan' => $perjalanan,
    //             'maps_data' => $mapsData,
    //             'signal_averages' => $signalAverages,
    //         ];
    //         $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
    //         return response($json, 200)
    //             ->header('Content-Type', 'application/json')
    //             ->header('Content-Length', strlen($json));
    //     }

    //     // === View Blade ===
    //     return view('telkominfra-show', [
    //         'perjalananDetail' => $perjalanan,
    //         'mapsData' => $mapsData,
    //         'signalAverages' => $signalAverages,
    //     ]);
    // }
    public function show(Request $request, string $id)
    {
        $perjalanan = Perjalanan::findOrFail($id);
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
                // 🔹 Ambil PengukuranSinyal berdasarkan perjalanan_data_id
                // $dataSinyal = PengukuranSinyal::where('perjalanan_data_id', $dataItem->id)->get();

                // --- Jika tidak ada data di DB, coba baca dari file NMF (opsional)
                $nmfPath = public_path('uploads/perjalanan/' . $dataItem->file_nmf);
                if (File::exists($nmfPath)) {
                    $parser = new FileTelkominfraController();
                    $dataSinyal = $parser->parseNmfSinyal($nmfPath, $dataItem->id);
                }

                foreach ($dataSinyal as $sinyal) {
                    // 🔸 Pastikan format data fleksibel (bisa array atau objek)
                    $latitudeData  = $sinyal['latitude'] ?? $sinyal->latitude ?? null;
                    $longitudeData = $sinyal['longitude'] ?? $sinyal->longitude ?? null;

                    if ($latitudeData === null || $longitudeData === null) {
                        continue;
                    }

                    $status = $dataItem->status;       // hanya dari perjalanan_data
                    $device = $dataItem->perangkat;    // hanya dari perjalanan_data

                    // --- Akumulasi rata-rata ---
                    if (isset($avgAccumulators[$status])) {
                        foreach (['rsrp', 'rssi', 'rsrq', 'sinr'] as $metric) {
                            $value = $sinyal[$metric] ?? $sinyal->$metric ?? null;
                            if ($value !== null) {
                                $avgAccumulators[$status]["{$metric}_sum"] += (float)$value;
                                $avgAccumulators[$status]["{$metric}_count"]++;
                            }
                        }
                    }

                    // --- Format waktu ---
                    $rawTimestamp = $sinyal['timestamp_waktu'] ?? $sinyal->timestamp_waktu ?? null;
                    $formattedTimestamp = null;
                    if ($rawTimestamp) {
                        try {
                            $carbonTime = Carbon::parse($rawTimestamp);
                            $formattedTimestamp = $carbonTime->format('H:i:s d/m/y');
                        } catch (\Exception $e) {
                            $formattedTimestamp = 'Invalid Time';
                        }
                    }

                    // --- Susun data titik untuk peta ---
                    $point = [
                        'id'        => $dataItem->id,
                        'latitude'  => (float)$latitudeData,
                        'longitude' => (float)$longitudeData,
                        'rsrp'      => $sinyal['rsrp'] ?? $sinyal->rsrp ?? null,
                        'rssi'      => $sinyal['rssi'] ?? $sinyal->rssi ?? null,
                        'rsrq'      => $sinyal['rsrq'] ?? $sinyal->rsrq ?? null,
                        'sinr'      => $sinyal['sinr'] ?? $sinyal->sinr ?? null,
                        'cell_id'   => $sinyal['cell_id'] ?? $sinyal->cell_id ?? null,
                        'pci'       => $sinyal['pci'] ?? $sinyal->pci ?? null,
                        'earfcn'    => $sinyal['earfcn'] ?? $sinyal->earfcn ?? null,
                        'band'      => $sinyal['band'] ?? $sinyal->band ?? null,
                        'frekuensi' => $sinyal['frekuensi'] ?? $sinyal->frekuensi ?? null,
                        'bandwidth' => $sinyal['bandwidth'] ?? $sinyal->bandwidth ?? null,
                        'n_value'   => $sinyal['n_value'] ?? $sinyal->n_value ?? null,
                        'timestamp_waktu' => $formattedTimestamp,
                        'device'    => $device,
                        'status'    => $status,
                    ];

                    $visualData[] = $point;
                    $mergedData[$status][] = $point;
                }

                // 🔹 Hitung koordinat tengah
                if (!empty($visualData)) {
                    $latitudes = array_column($visualData, 'latitude');
                    $longitudes = array_column($visualData, 'longitude');
                    $centerCoords = [
                        (min($latitudes) + max($latitudes)) / 2,
                        (min($longitudes) + max($longitudes)) / 2,
                    ];
                }
            } catch (\Exception $e) {
                Log::error("Gagal mengambil data sinyal dari DB untuk perjalananData ID: {$dataItem->id}. Error: " . $e->getMessage());
            }

            // 🔹 Simpan hasil setiap status (Before / After)
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
                $centerCoords = [
                    (min($latitudes) + max($latitudes)) / 2,
                    (min($longitudes) + max($longitudes)) / 2,
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

        // === API Response (jika JSON) ===
        if ($request->wantsJson() || $request->is('api/*')) {
            $payload = [
                'status' => 'success',
                'perjalanan' => $perjalanan,
                'maps_data' => $mapsData,
                'signal_averages' => $signalAverages,
            ];
            return response()->json($payload);
        }

        // === View Blade ===
        return view('telkominfra-show', [
            'perjalananDetail' => $perjalanan,
            'mapsData' => $mapsData,
            'signalAverages' => $signalAverages,
        ]);
    }

        

}