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

class FileTelkominfraController extends Controller
{
    public function parseNmfHeader(string $nmfPath): array
    {
        $perangkat = 'Unknown Device';
        $idPerjalanan = Str::uuid()->toString();

        if (($handle = fopen($nmfPath, "r")) !== FALSE) {
            while (($line = fgets($handle)) !== FALSE) {
                $line = trim($line);

                if (str_starts_with($line, '#DN')) {
                    $parts = str_getcsv($line); 
                    if (isset($parts[3])) {
                        $perangkat = trim($parts[3], '"');
                    }
                }

                if (str_starts_with($line, '#ID')) {
                    $parts = str_getcsv($line);
                    if (isset($parts[3])) {
                        $idPerjalanan = trim($parts[3], '"');
                    }
                }
            }
            fclose($handle);
        }

        return [
            'perangkat' => $perangkat,
            'id_perjalanan' => $idPerjalanan,
        ];
    }

    public function parseGpxAndNmfGps(string $gpxPath, string $nmfPath, int $perjalananId): array
    {
        $dataGPS = [];
        try {
            $xml = simplexml_load_file($gpxPath);
            if ($xml === false) {
                 \Log::error("Gagal memuat file GPX.");
                 return [];
            }

            foreach ($xml->trk->trkseg->trkpt as $trkpt) {
                $lat = (float) $trkpt['lat'];
                $lon = (float) $trkpt['lon'];
                $time = (string) $trkpt->time;

                if ($lat !== 0.0 && $lon !== 0.0) {
                    $dataGPS[] = [
                        'perjalanan_id' => $perjalananId,
                        'timestamp_waktu' => Carbon::parse($time)->toDateTimeString(), 
                        'latitude' => $lat,
                        'longitude' => $lon,
                        'altitude' => (float) $trkpt->ele,
                        'sumber' => 'GPX',
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::error("Error saat parsing GPX:", ['message' => $e->getMessage()]);
        }
        return $dataGPS; 
    }


    public function convertRawHeaderTime(string $rawTime, string $rawDate): ?string
    {
        try {
            $dateString = trim($rawDate, '"');
            $dateTimeString = $dateString . ' ' . $rawTime;
            return Carbon::createFromFormat('d.m.Y H:i:s.v', $dateTimeString)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Membaca file NMF untuk mencari tag #START dan #STOP.
     * * @param string $nmfPath Path ke file NMF.
     * @return array Mengembalikan array ['timestamp_mulai', 'timestamp_selesai'].
     */
    public function extractNmfTimes(string $nmfPath): array
    {
        $times = [
            'timestamp_mulai' => null,
            'timestamp_selesai' => null,
        ];
        
        if (!File::exists($nmfPath)) {
            Log::warning("File NMF tidak ditemukan untuk ekstraksi waktu: $nmfPath");
            return $times;
        }

        if (($handle = fopen($nmfPath, "r")) !== FALSE) {
            $delimiter = ',';
            while (($line = fgets($handle)) !== FALSE) {
                $line = trim($line);

                if (str_starts_with($line, '#START') || str_starts_with($line, '#STOP')) {
                    $parts = str_getcsv($line, $delimiter);

                    $rawTime = $parts[1] ?? null;
                    $rawDate = $parts[3] ?? null;

                    if ($rawTime && $rawDate) {
                        $mysqlTimestamp = $this->convertRawHeaderTime($rawTime, $rawDate);
                        
                        if (str_starts_with($line, '#START')) {
                            $times['timestamp_mulai'] = $mysqlTimestamp;
                        } elseif (str_starts_with($line, '#STOP')) {
                            $times['timestamp_selesai'] = $mysqlTimestamp;
                        }
                    }
                }
                
                if ($times['timestamp_mulai'] !== null && $times['timestamp_selesai'] !== null) {
                    break;
                }
            }
            fclose($handle);
        }
        
        return $times;
    }


    public function parseNmfSinyal(string $nmfPath, int $perjalananId): array
    {
        $dataSinyal = [];
        $currentGps = [
            'lat' => null,
            'lon' => null,
        ]; 
        $lineCount = 0;
        $logDate = null; 
        
        $delimiter = ','; 

        $convertTime = function (?string $rawTime, ?string $logDate): ?string {
             if (!$rawTime || !$logDate) return null;
             try {
                $dateTimeString = $logDate . ' ' . $rawTime;
                return Carbon::createFromFormat('Y-m-d H:i:s.v', $dateTimeString)->format('Y-m-d H:i:s');
             } catch (\Exception $e) {
                 return null;
             }
        };

        $resolveBandFrequency = function (?int $earfcn) {
            $band = null;
            $frekuensi = null;
            $bandwidth = null;
            $n_value = null;

            $bandwidth_mapping = [
                40 => 20, // Band 40 (2300 MHz)
                3  => 20, // Band 3 (1800 MHz)
                1  => 10, // Band 1 (2100 MHz)
                8  => 10, // Band 8 (900 MHz)
                42 => 20, // Band 42 (3500 MHz)
                10 => 10, // Band 10 (2100 MHz)
                13 => 10, // Band 13 (700 MHz)
            ];

            if ($earfcn !== null) {
                switch ($earfcn) {
                    case 39092:
                    case 38750:
                    case 38948: $band = 40; $frekuensi = 2300; break;
                    case 1850: $band = 3;  $frekuensi = 1800; break;
                    case 500: $band = 1;  $frekuensi = 2100; break;
                    case 3500:   $band = 8;  $frekuensi = 900;  break;
                        break; 

                    default:
                        // if ($earfcn >= 41590 && $earfcn <= 43589) {
                        //     $band = 42;
                        //     $frekuensi = 3500;
                        // } elseif ($earfcn >= 0 && $earfcn <= 599) {
                        //     $band = 13;
                        //     $frekuensi = 700;
                        // } else {
                            $band = null;
                            $frekuensi = null;
                        // }
                        break;
                }

                if (is_int($band) && isset($bandwidth_mapping[$band])) {
                    $bandwidth = $bandwidth_mapping[$band];
                    $n_value = $bandwidth * 5; 
                } else {
                    $bandwidth = null;
                    $n_value = null;
                }
                // ----------------------------------------------
            }

            return [
                'band' => $band, 
                'frekuensi' => $frekuensi, 
                'bandwidth' => $bandwidth, 
                'n_value' => $n_value
            ];
        };

        if (($handle = fopen($nmfPath, "r")) !== FALSE) {
            while (($line = fgets($handle)) !== FALSE) {
                $lineCount++;
                $line = trim($line);

                if ($line === '' || str_starts_with($line, '#')) {
                    if (str_starts_with($line, '#START')) {
                        $parts = str_getcsv($line, $delimiter); 
                        $rawDate = $parts[3] ?? null; 
                        if ($rawDate) {
                            try {
                                $dateString = trim($rawDate, '"');
                                $logDate = Carbon::createFromFormat('d.m.Y', $dateString)->format('Y-m-d');
                            } catch (\Exception $e) {
                                $logDate = Carbon::now()->format('Y-m-d'); 
                            }
                        }
                    }
                    continue;
                }

                if (!$logDate) {
                    $logDate = Carbon::now()->format('Y-m-d'); 
                }

                $parts = str_getcsv($line, $delimiter); 

                if (str_starts_with($line, 'GPS')) {
                    if (isset($parts[3]) && isset($parts[4]) && $parts[3] !== '' && $parts[4] !== '') {
                        $currentGps['lat'] = (float)$parts[4];
                        $currentGps['lon'] = (float)$parts[3];
                    } else {
                        $currentGps['lat'] = null;
                        $currentGps['lon'] = null;
                    }
                    
                    continue; 
                }

                if (str_starts_with($line, 'CHI')) {
                    $n_value_chi = $parts[6] ?? null;
                    $bandwidth_chi = $n_value_chi / 5;
                    continue;
                }
                
                if (str_starts_with($line, 'CELLMEAS')) {
                    $pci = $parts[10] ?? null;
                    $earfcn_cell = $parts[9] ?? null;
                    continue;
                }

                if (str_starts_with($line, 'MIMOMEAS')) {
                    try {
                        $rawTime = $parts[1] ?? null;
                        $lat = $currentGps['lat'];
                        $lon = $currentGps['lon'];
                        $timestampWaktu = $convertTime($rawTime, $logDate); 
                        $earfcn = (int)($parts[8] ?? $earfcn_cell ?? null);
                        
                        $bandFreq = $resolveBandFrequency($earfcn);

                        if ($lat === null || $lon === null) {
                            Log::warning("MIMOMEAS baris $lineCount tidak memiliki koordinat dari GPS sebelumnya.");
                        }

                        // $nValue = $bandFreq['n_value'] ?? 100;

                        $nValue = $n_value_chi ?? $bandFreq['n_value'];
                        $bandwidth = $bandwidth_chi ?? $bandFreq['bandwidth'];

                        $rawRssi = (float)($parts[12] ?? -120);

                        $rawRsrp = (float)($parts[14] ?? -120);
                        $rsrpValue = $rawRsrp >= 0 ? -120 : $rawRsrp;

                        $rawRsrq = (float)($parts[13] ?? -20);
                        $rsrqValue = $rawRsrq >= 0 ? -20.0 : $rawRsrq;

                        $rawSinr = (float)($parts[11] ?? null);
                        if ($rawSinr == null) {

                            $rsrp_mW = pow(10, ($rawRsrp / 10));
                            $rssi_mW = pow(10, ($rawRssi / 10));

                            if ($nValue <= 0) {
                                $nValue = 100;
                            }
                            
                            $signal_total_mW = $rsrp_mW * 12 * $nValue;
                            $interference_noise_mW = $rssi_mW - $signal_total_mW;

                            if ($interference_noise_mW <= 0) {
                                $sinrValue = 20.0; 
                            } else {
                                $interference_noise_per_re_mW = $interference_noise_mW / (12 * $nValue);
                                $sinr_linear = $rsrp_mW / $interference_noise_per_re_mW;
                                $sinr_dB = 10 * log10($sinr_linear);
                                $sinrValue = max(-30.0, min($sinr_dB, 30.0));
                            }
                        } else {
                            $sinrValue = $rawSinr;
                        }

                        $dataSinyal[] = [
                            'perjalanan_id'     => $perjalananId,
                            'timestamp_waktu'   => $timestampWaktu,
                            'teknologi'         => 'LTE', 
                            'pci'               => $pci ?? 'Unknown', 
                            'rsrp'              => $rsrpValue,
                            'rssi'              => $rawRssi, 
                            'rsrq'              => $rsrqValue, 
                            'sinr'              => $sinrValue,
                            'earfcn'            => $earfcn,
                            'band'              => $bandFreq['band'],
                            'frekuensi'         => $bandFreq['frekuensi'],
                            'bandwidth'         => $bandwidth,
                            'n_value'           => $nValue,
                            'latitude'          => $lat, 
                            'longitude'         => $lon, 
                            'cell_id'           => $parts[7] ?? null,
                        ];
                    } catch (\Exception $e) {
                        Log::warning("Gagal parsing baris MIMOMEAS ke-$lineCount: " . $e->getMessage());
                    }
                }
            }

            fclose($handle);
        }
        return $dataSinyal;
    }

}