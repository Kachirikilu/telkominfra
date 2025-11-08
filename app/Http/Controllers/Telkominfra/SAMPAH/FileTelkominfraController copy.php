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
            $bandwidth = null; // Tambahkan Bandwidth
            $n_value = null;   // Tambahkan nilai N (Resource Blocks)

            // --- Definisi Umum Bandwidth dan N ---
            // Mapping umum (perlu disesuaikan dengan alokasi spektrum aktual Anda)
            $bandwidth_mapping = [
                40 => 20, // Band 40 (2300 MHz): Sering 20 MHz
                3  => 20, // Band 3 (1800 MHz): Sering 20 MHz
                1  => 10, // Band 1 (2100 MHz): Umumnya 10 MHz atau 20 MHz
                8  => 10, // Band 8 (900 MHz): Umumnya 5 atau 10 MHz
                42 => 20, // Band 42 (3500 MHz): Sering 20 MHz
                10 => 10, // Band 10 (2100 MHz): Umumnya 10 MHz
                13 => 10, // Band 13 (700 MHz): Umumnya 5 atau 10 MHz
                // Tambahkan band lain sesuai kebutuhan Anda
            ];
            // Formula sederhana N (RB) = (Bandwidth dalam MHz) * 5
            // (Contoh: 20 MHz = 100 RB, 10 MHz = 50 RB)
            // Ini mengabaikan overhead guard band, tapi cukup untuk perkiraan log.
            // ----------------------------------------

            if ($earfcn !== null) {
                switch ($earfcn) {
                    case 38750:
                    case 38948: $band = 40; $frekuensi = 2300; break;
                    case 19850: $band = 3;  $frekuensi = 1800; break;
                    case 18500: $band = 1;  $frekuensi = 2100; break;
                    case 900:   $band = 8;  $frekuensi = 900;  break;
                    case 3500:  $band = 42; $frekuensi = 3500; break; // EARFCN yang Anda gunakan di log
                    case 1850:  $band = 10; $frekuensi = 2100; break;
                    
                    case 500: 
                        $band = 13; 
                        $frekuensi = 700; 
                        break; 

                    default:
                        if ($earfcn >= 41590 && $earfcn <= 43589) { // Band 42 Range
                            $band = 42;
                            $frekuensi = 3500;
                        } elseif ($earfcn >= 0 && $earfcn <= 599) { // Band 13 Range
                            $band = 13;
                            $frekuensi = 700;
                        } else {
                            $band = null;
                            $frekuensi = null;
                        }
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

                if (str_starts_with($line, 'CELLMEAS')) {
                    try {
                        $rawTime = $parts[1] ?? null;
                        $lat = $currentGps['lat'];
                        $lon = $currentGps['lon'];
                        $timestampWaktu = $convertTime($rawTime, $logDate); 
                        $earfcn = (int)($parts[9] ?? null);
                        
                        $bandFreq = $resolveBandFrequency($earfcn); // Panggil fungsi Band/Freq

                        if ($lat === null || $lon === null) {
                            Log::warning("CELLMEAS baris $lineCount tidak memiliki koordinat dari GPS sebelumnya.");
                        }

                        $rawRsrq = (float)($parts[13] ?? -20);
                        $rsrqValue = $rawRsrq > 0 ? -20.0 : $rawRsrq;
                        // $rsrqValue = $rawRsrq;

                        $nValue = $bandFreq['n_value'] ?? 100;
                        $rawRssi = (float)($parts[11] ?? -120);
                        $rawRsrp = (float)($parts[12] ?? -120);
                        // $rawRsrp = (float)($parts[11] ?? -120);
                        // $rawRssi = isset($parts[12]) ? $rawRsrp : -120;
                        $rawSinr = (float)($parts[14] ?? 0);

                        if ($rawSinr == 0 || $rawSinr < -30.0 || $rawSinr > 30.0) {
                            $rsrp_mW = pow(10, ($rawRsrp / 10));
                            $rsrq_dB = min($rsrqValue, -3.0);
                            $rsrq_linear = pow(10, ($rsrq_dB / 10));

                            $epsilon = 1e-12;
                            if ($rsrq_linear < $epsilon) {
                                $rsrq_linear = $epsilon;
                            }
                            if ($nValue <= 0) {
                                $nValue = 100;
                            }
                                if ($rawRssi !== null) {
                                    $rssi_mW = pow(10, ($rawRssi / 10));
                                } else {
                                    $rssi_mW = ($nValue * $rsrp_mW) / $rsrq_linear;
                                }
                            $interference_noise_mW = $rssi_mW - $rsrp_mW;

                            if ($interference_noise_mW <= 0) {
                                $sinrValue = 30.0;
                            } else {
                                $sinr_linear = $rsrp_mW / $interference_noise_mW;
                                $sinr_dB = 10 *  log10($sinr_linear);
                                $sinrValue = max(-30.0, min($sinr_dB, 30.0));
                            }
                        } else {
                            $sinrValue = $rawSinr;
                        }

                        $dataSinyal[] = [
                            'perjalanan_id'     => $perjalananId,
                            'timestamp_waktu'   => $timestampWaktu,
                            'teknologi'         => 'LTE', 
                            'pci'               => $parts[10] ?? 'Unknown', 
                            'rsrp'              => $rawRsrp,
                            'rssi'              => $rawRssi, 
                            'rsrq'              => $rsrqValue, 
                            'sinr'              => $sinrValue,
                            'earfcn'            => $earfcn,
                            'band'              => $bandFreq['band'],
                            'frekuensi'         => $bandFreq['frekuensi'],
                            'bandwidth'         => $bandFreq['bandwidth'],
                            'n_value'           => $nValue,
                            'latitude'          => $lat, 
                            'longitude'         => $lon, 
                            'cell_id'           => $parts[7] ?? null,
                        ];
                    } catch (\Exception $e) {
                        Log::warning("Gagal parsing baris CELLMEAS ke-$lineCount: " . $e->getMessage());
                    }
                }
            }

            fclose($handle);
        }
        return $dataSinyal;
    }

}