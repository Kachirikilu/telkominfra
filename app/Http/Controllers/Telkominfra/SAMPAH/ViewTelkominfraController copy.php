<?php

namespace App\Http\Controllers\Telkominfra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Perjalanan;
use App\Models\PerjalananData;
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
                    if (isset($avgAccumulators[$status])) {
                        if (isset($sinyal['rsrp'])) {
                            $avgAccumulators[$status]['rsrp_sum'] += (float) $sinyal['rsrp'];
                            $avgAccumulators[$status]['rsrp_count']++;
                        }
                        if (isset($sinyal['rssi'])) {
                            $avgAccumulators[$status]['rssi_sum'] += (float) $sinyal['rssi'];
                            $avgAccumulators[$status]['rssi_count']++;
                        }
                        if (isset($sinyal['rsrq'])) {
                            $avgAccumulators[$status]['rsrq_sum'] += (float) $sinyal['rsrq'];
                            $avgAccumulators[$status]['rsrq_count']++;
                        }
                        if (isset($sinyal['sinr'])) {
                            $avgAccumulators[$status]['sinr_sum'] += (float) $sinyal['sinr'];
                            $avgAccumulators[$status]['sinr_count']++;
                        }
                    }
                    // --- Akhir Akumulasi ---

                    $rawTimestamp = $sinyal['timestamp_waktu'] ?? null;
                    $formattedTimestamp = null;

                    if ($rawTimestamp) {
                        try {
                            $carbonTime = Carbon::parse($rawTimestamp);
                            $formattedTimestamp = $carbonTime->format('H:i:s d M Y'); 
                            
                        } catch (\Exception $e) {
                            $formattedTimestamp = 'Invalid Time';
                        }
                    }

                    $visualData[] = [
                        'id'        => $dataItem->id,
                        'latitude'  => (float) $sinyal['latitude'],
                        'longitude' => (float) $sinyal['longitude'],
                        'rsrp'      => $sinyal['rsrp'] ?? null,
                        'rssi'      => $sinyal['rssi'] ?? null,
                        'rsrq'      => $sinyal['rsrq'] ?? null,
                        'sinr'      => $sinyal['sinr'] ?? null,
                        'pci'       => $sinyal['pci'] ?? null,
                        'earfcn'   => $sinyal['earfcn'] ?? null,
                        'band'     => $sinyal['band'] ?? null,
                        'frekuensi' => $sinyal['frekuensi'] ?? null,
                        'bandwidth' => $sinyal['bandwidth'] ?? null,
                        'n_value' => $sinyal['n_value'] ?? null,
                        'timestamp_waktu' => $formattedTimestamp,
                    ];
                }
                if (!empty($visualData)) {
                    $midIndex = intval(count($visualData) / 2);
                    $centerCoords = [
                        $visualData[$midIndex]['latitude'],
                        $visualData[$midIndex]['longitude'],
                    ];
                }
            } catch (\Exception $e) {
                Log::error("Gagal memproses file NMF untuk PerjalananData ID: " . $dataItem->id . ". Error: " . $e->getMessage());
            }

            $mapsData[] = [
                'id' => $dataItem->id,
                'centerCoords' => $centerCoords,
                'visualData' => $visualData,
                'fileName' => $dataItem->file_nmf,
                'perangkat' => $dataItem->perangkat,
                'status' => $dataItem->status,
            ];
        }

        $signalAverages = [];
        foreach ($avgAccumulators as $status => $data) {
            $signalAverages[$status] = [
                'rsrp_avg' => $data['rsrp_count'] > 0 ? round($data['rsrp_sum'] / $data['rsrp_count'], 2) : 0,
                'rssi_avg' => $data['rssi_count'] > 0 ? round($data['rssi_sum'] / $data['rssi_count'], 2) : 0,
                'rsrq_avg' => $data['rsrq_count'] > 0 ? round($data['rsrq_sum'] / $data['rsrq_count'], 2) : 0,
                'sinr_avg' => $data['sinr_count'] > 0 ? round($data['sinr_sum'] / $data['sinr_count'], 2) : 0,
            ];
        }

        $isBeforeEmpty = $avgAccumulators['Before']['rsrp_count'] === null &&
                         $avgAccumulators['Before']['rssi_count'] === null &&
                         $avgAccumulators['Before']['rsrq_count'] === null &&
                         $avgAccumulators['Before']['sinr_count'] === null;

        $isAfterEmpty = $avgAccumulators['After']['rsrp_count'] === null &&
                        $avgAccumulators['After']['rssi_count'] === null &&
                        $avgAccumulators['After']['rsrq_count'] === null &&
                        $avgAccumulators['After']['sinr_count'] === null;

        if ($isBeforeEmpty && !$isAfterEmpty) {
            $signalAverages['Before'] = [
                'rsrp_avg' => null,
                'rssi_avg' => null,
                'rsrq_avg' => null,
                'sinr_avg' => null,
            ];
            Log::info("Data 'Before' kosong, menggunakan data 'After' sebagai fallback.");

        } elseif ($isAfterEmpty && !$isBeforeEmpty) {
            $signalAverages['After'] = [
                'rsrp_avg' => null,
                'rssi_avg' => null,
                'rsrq_avg' => null,
                'sinr_avg' => null,
            ];
            Log::info("Data 'After' kosong, menggunakan data 'Before' sebagai fallback.");
            
        } elseif ($isAfterEmpty && $isBeforeEmpty) {
            $signalAverages = [
                'rsrp_avg' => null,
                'rssi_avg' => null,
                'rsrq_avg' => null,
                'sinr_avg' => null,
            ];
            Log::warning("Data 'Before' dan 'After' kosong untuk perbandingan sinyal.");
        }
        
        // --- END: LOGIKA FALLBACK UNTUK DATA YANG HILANG ---

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

}