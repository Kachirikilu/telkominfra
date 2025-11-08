<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalCeramah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JadwalCeramahsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF;');

        DB::table('jadwal_ceramahs')->insert([
            [
                'slug' => 'kajian-subuh-keutamaan-sedekah_ustadz-abdul-somad_20250509225352604484',
                'judul_ceramah' => 'Kajian Subuh: Keutamaan Sedekah',
                'nama_ustadz' => 'Ustadz Abdul Somad',
                'gambar' => 'images/jadwal/bZBplkpYiaLiLuzPhYwClZPIsUKVtJ1Mw4sTT3qC7mTSdgSmn25vkcbOfxvTWTKpprfj5POxjoirxi4vLfK8odqnoqxpDRTlLf2g_20250509225352604522.jpg',
                'tanggal_ceramah' => '2025-05-15',
                'jam_mulai' => '10:30:00',
                'jam_selesai' => '11:45:00',
                'tempat_ceramah' => 'Masjid Al-Aqobah 1 - Ruang Utama',
                'tentang_ceramah' => 'Mengupas tuntas keutamaan dan keberkahan sedekah dalam Islam.',
                'kategori_ceramah' => 'Kajian Subuh',
                'link_streaming' => 'https://youtube.com/alaqobah1/live1',

                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'tafsir-al-kahfi-sore-ini_ustadz-abdul-somad_20250509225602233811',
                'judul_ceramah' => 'Tafsir Al-Kahfi Sore Ini',
                'nama_ustadz' => 'Ustadz Abdul Somad',
                'gambar' => 'images/jadwal/g1ymjTbMjASutFQTkAfqaFPG6Dpnyw3JgG7oBtNwhFAqu7sNSmwydUFsv0pWhhBMFi6ofkhoulEkVqh9N5h8RPWYS6YZcMwf6RQm_20250509225602233838.jpg',
                'tanggal_ceramah' => '2025-05-15',
                'jam_mulai' => '15:45:00',
                'jam_selesai' => '17:00:00',
                'tempat_ceramah' => 'Masjid Al-Aqobah 1 - Ruang Utama',
                'tentang_ceramah' => 'Mendalami makna dan hikmah yang terkandung dalam Surah Al-Kahfi.',
                'kategori_ceramah' => 'Tafsir',
                'link_streaming' => 'https://youtube.com/alaqobah1/live2',

                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'fikih-puasa-sunnah-senin-kamis_ustadz-abdul-somad_20250509230814894870',
                'judul_ceramah' => 'Fikih Puasa Sunnah Senin & Kamis',
                'nama_ustadz' => 'Ustadz Abdul Somad',
                'gambar' => 'images/jadwal/Gvp30xV7hhvJqdtipGSARtGxh3XkJQMMYOaaviC8lCSdlsVH66R8WTPEiKiChCMdv8pheDO4Bp4AZjNdRLTb8zFRn6EduIaZdtef_20250509230814894897.jpg',
                'tanggal_ceramah' => '2025-05-18',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '09:30:00',
                'tempat_ceramah' => 'Masjid Al-Aqobah 1 - Ruang Utama',
                'tentang_ceramah' => 'Pembahasan lengkap mengenai hukum dan keutamaan puasa sunnah Senin dan Kamis.',
                'kategori_ceramah' => 'Fiqih',
                'link_streaming' => 'https://youtube.com/alaqobah1/live3',

                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'adab-dan-akhlak-seorang-muslim_ustadz-abdul-somad_20250509230959768927',
                'judul_ceramah' => 'Adab dan Akhlak Seorang Muslim',
                'nama_ustadz' => 'Ustadz Abdul Somad',
                'gambar' => 'images/jadwal/qIUsTL349HdxiHoJ862weLYBjMFEw3UcaCjdy7zhwpAf2XjOUMArbKDLTB97AZae138wcByzNKrhoyAxdNmh2G0m0bueM7t6R0hb_20250509230959768956.jpg',
                'tanggal_ceramah' => '2025-05-19',
                'jam_mulai' => '10:00:00',
                'jam_selesai' => '11:30:00',
                'tempat_ceramah' => 'Masjid Al-Aqobah 1 - Ruang Utama',
                'tentang_ceramah' => 'Mempelajari adab dan akhlak mulia yang seharusnya dimiliki oleh setiap Muslim.',
                'kategori_ceramah' => 'Akhlak',
                'link_streaming' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'sirah-nabawiyah-perjalanan-hijrah_ustadz-abdul-somad_20250509231201110585',
                'judul_ceramah' => 'Sirah Nabawiyah: Perjalanan Hijrah',
                'nama_ustadz' => 'Ustadz Abdul Somad',
                'gambar' => 'images/jadwal/5o4Dg3uR4miljTqxkJf0ikDetXkn41nMgixdqDtw5RxcSw1W6qjE6tESjTSu2AnIRTGwLxhs228QUsHCTbJqLDB8LQZCme4pTiMg_20250509231201110613.jpg',
                'tanggal_ceramah' => '2025-05-25',
                'jam_mulai' => '06:15:00',
                'jam_selesai' => '08:45:00',
                'tempat_ceramah' => 'Masjid Al-Aqobah 1 - Ruang Utama',
                'tentang_ceramah' => 'Menelusuri kisah perjalanan hijrah Rasulullah SAW dan pelajaran yang bisa diambil.',
                'kategori_ceramah' => 'Sirah Nabawiyah',
                'link_streaming' => 'https://twitter.com/alaqobah1/live4',

                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'kajian-kitab-riyadhus-shalihin_ustadz-salman-al-farisi_20250509231445918078',
                'judul_ceramah' => 'Kajian Kitab Riyadhus Shalihin',
                'nama_ustadz' => 'Ustadz Salman Al-Farisi',
                'gambar' => 'images/jadwal/IQDEHLMqajZZIoSoTN2Tw7EAPS4RnuuAGMpjgmGlxlYp15XWBlKHv5OEYQUuJXmtgItIWqM9GlkyhKfDhY4QHpr0dqrQwWEFPZsP_20250509231445918104.jpg',
                'tanggal_ceramah' => '2025-05-01',
                'jam_mulai' => '08:30:00',
                'jam_selesai' => '10:00:00',
                'tempat_ceramah' => 'Masjid Al-Aqobah 1 - Ruang Utama',
                'tentang_ceramah' => 'Melanjutkan pembahasan hadits-hadits pilihan dari Kitab Riyadhus Shalihin.',
                'kategori_ceramah' => 'Kajian Kitab',
                'link_streaming' => 'https://archive.alaqobah1.com/kajian1',

                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'fiqih-wanita-seputar-haid_ustadzah-nurul-hidayah_20250509231609289479',
                'judul_ceramah' => 'Fiqih Wanita: Seputar Haid',
                'nama_ustadz' => 'Ustadzah Nurul Hidayah',
                'gambar' => 'images/jadwal/8XGuS59IDlEIAg55r59RB8ULw7yu5LSbAa50rqp6L5ntfqdCBxqkP4TDl9c2pmymmnlSBodmTXmxU1C1CFBbMxwqlCMMz24rD6F2_20250509231609289507.jpg',
                'tanggal_ceramah' => '2025-04-26',
                'jam_mulai' => '10:15:00',
                'jam_selesai' => '11:30:00',
                'tempat_ceramah' => 'Masjid Al-Aqobah 1 - Aula Wanita',
                'tentang_ceramah' => 'Pembahasan mendalam mengenai hukum-hukum seputar haid dalam Islam.',
                'kategori_ceramah' => 'Fiqih',
                'link_streaming' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        DB::statement('PRAGMA foreign_keys = ON;');
    }
}
