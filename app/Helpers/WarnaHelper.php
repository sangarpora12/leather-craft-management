<?php

namespace App\Helpers;

class WarnaHelper
{
    /**
     * Kamus Warna Bahasa Indonesia — lengkap dengan variasi dan turunan.
     * Key: nama warna (lowercase, tanpa spasi berlebih).
     * Value: kode Hex.
     */
    public static array $KAMUS_WARNA = [
        // ===== WARNA DASAR =====
        'merah'       => '#DC143C',
        'kuning'      => '#FFD700',
        'hijau'       => '#228B22',
        'biru'        => '#1E90FF',
        'hitam'       => '#1A1A1A',
        'putih'       => '#F5F5F5',
        'abu abu'     => '#808080',
        'abuabu'      => '#808080',
        'abu'         => '#808080',
        'cokelat'     => '#8B4513',
        'coklat'      => '#8B4513',
        'orange'      => '#FF8C00',
        'oranye'      => '#FF8C00',
        'ungu'        => '#8A2BE2',
        'violet'      => '#8A2BE2',
        'pink'        => '#FF69B4',
        'merah muda'  => '#FF69B4',
        'biru muda'   => '#87CEEB',

        // ===== MERAH & TURUNAN =====
        'merah darah'    => '#8B0000',
        'merah hati'     => '#8B0000',
        'merah marun'    => '#800000',
        'marun'          => '#800000',
        'merah bata'     => '#B22222',
        'merah tua'      => '#A52A2A',
        'merah jambu'    => '#FFB6C1',
        'merah ros'      => '#FF007F',
        'rose'           => '#FF007F',
        'merah cerah'    => '#FF0000',
        'merah terang'   => '#FF0000',
        'merah gelap'    => '#8B0000',
        'merah anggur'   => '#722F37',
        'angggur'        => '#722F37',

        // ===== BIRU & TURUNAN =====
        'biru laut'    => '#000080',
        'biru dongker' => '#000080',
        'biru navy'    => '#000080',
        'navy'         => '#000080',
        'biru langit'  => '#87CEEB',
        'biru terang'  => '#00BFFF',
        'biru cerah'   => '#00BFFF',
        'biru gelap'   => '#00008B',
        'biru tua'     => '#00008B',
        'biru elektrik' => '#0066FF',
        'biru baja'    => '#4682B4',
        'biru abu'     => '#6699CC',
        'biru kehijauan' => '#008080',
        'biru hijau'   => '#008080',
        'biru pucat'   => '#ADD8E6',
        'biru susu'    => '#ADD8E6',
        'tosca'        => '#40E0D0',
        'biru tosca'   => '#40E0D0',
        'cyan'         => '#00FFFF',
        'biru cyan'    => '#00FFFF',

        // ===== HIJAU & TURUNAN =====
        'hijau tua'     => '#006400',
        'hijau gelap'   => '#006400',
        'hijau muda'    => '#90EE90',
        'hijau terang'  => '#32CD32',
        'hijau cerah'   => '#32CD32',
        'hijau lumut'   => '#556B2F',
        'lumut'         => '#556B2F',
        'hijau daun'    => '#2E8B57',
        'hijau botol'   => '#006400',
        'hijau army'    => '#4B5320',
        'army'          => '#4B5320',
        'hijau olive'   => '#6B8E23',
        'olive'         => '#6B8E23',
        'hijau pupus'   => '#98FB98',
        'hijau pastel'  => '#77DD77',
        'hijau lumut tua' => '#3B5323',
        'hijau pinus'   => '#01796F',
        'hijau zamrud'  => '#50C878',
        'zamrud'        => '#50C878',
        'hijau apel'    => '#8DB600',
        'hijau neon'    => '#39FF14',
        'hijau hutan'   => '#228B22',

        // ===== KUNING & TURUNAN =====
        'kuning tua'    => '#B8860B',
        'kuning gelap'  => '#B8860B',
        'kuning muda'   => '#FFFACD',
        'kuning terang' => '#FFFF00',
        'kuning cerah'  => '#FFFF00',
        'kuning emas'   => '#FFD700',
        'emas'          => '#FFD700',
        'kuning kunyit' => '#FFDB58',
        'kuning jerami' => '#F0E68C',
        'kuning pucat'  => '#FFF8DC',
        'kuning madu'   => '#F0E68C',

        // ===== COKELAT & TURUNAN =====
        'cokelat tua'    => '#5C4033',
        'cokelat gelap'  => '#5C4033',
        'cokelat muda'   => '#D2B48C',
        'cokelat terang' => '#D2B48C',
        'cokelat susu'   => '#C4A882',
        'cokelat tanah'  => '#8B4513',
        'cokelat kayu'   => '#A0522D',
        'cokelat karamel' => '#AF6E4D',
        'karamel'        => '#AF6E4D',
        'cokelat kopi'   => '#4A3728',
        'kopi'           => '#4A3728',
        'cokelat coklat' => '#8B4513',
        'coklat tua'     => '#5C4033',
        'coklat muda'    => '#D2B48C',
        'coklat susu'    => '#C4A882',
        'coklat tanah'   => '#8B4513',
        'krem'           => '#FFFDD0',
        'krem tua'       => '#E8D5B7',
        'beige'          => '#F5E6D3',
        'cokelat krem'   => '#F5E6D3',

        // ===== ABU-ABU & TURUNAN =====
        'abu abu tua'    => '#404040',
        'abu tua'        => '#404040',
        'abu gelap'      => '#404040',
        'abu abu muda'   => '#C0C0C0',
        'abu muda'       => '#C0C0C0',
        'abu terang'     => '#C0C0C0',
        'abu abu terang' => '#C0C0C0',
        'abu metalik'    => '#B0B0B0',
        'metalik'        => '#B0B0B0',
        'abu perak'      => '#C0C0C0',
        'perak'          => '#C0C0C0',
        'abu gelap tua'  => '#2F2F2F',
        'abu arang'      => '#36454F',
        'abu abu arang'  => '#36454F',
        'grafit'         => '#414A4C',
        'abu grafit'     => '#414A4C',
        'abu kayu'       => '#A9A9A9',
        'abu pucat'      => '#D3D3D3',
        'silver'         => '#C0C0C0',
        'gray'           => '#808080',
        'grey'           => '#808080',

        // ===== UNGU & TURUNAN =====
        'ungu tua'     => '#4B0082',
        'ungu gelap'   => '#4B0082',
        'ungu muda'    => '#DDA0DD',
        'ungu terang'  => '#DDA0DD',
        'ungu violet'  => '#8A2BE2',
        'ungu lavender' => '#E6E6FA',
        'lavender'     => '#E6E6FA',
        'ungu lembayung' => '#DA70D6',
        'lembayung'    => '#DA70D6',
        'ungu plum'    => '#DDA0DD',
        'plum'         => '#DDA0DD',
        'ungu magenta' => '#FF00FF',
        'magenta'      => '#FF00FF',
        'ungu biru'    => '#6A0DAD',
        'ungu kecubung' => '#9966CC',

        // ===== PINK / MERAH MUDA & TURUNAN =====
        'pink muda'    => '#FFB6C1',
        'pink terang'  => '#FF69B4',
        'pink cerah'   => '#FF1493',
        'pink gelap'   => '#FF1493',
        'pink tua'     => '#FF1493',
        'pink hot'     => '#FF1493',
        'pink salmon'  => '#FA8072',
        'salmon'       => '#FA8072',
        'pink pastel'  => '#FFD1DC',
        'pink fuchsia' => '#FF00FF',
        'fuchsia'      => '#FF00FF',
        'pink baby'    => '#F4C2C2',

        // ===== ORANYE & TURUNAN =====
        'oranye tua'   => '#FF4500',
        'oranye gelap' => '#FF4500',
        'oranye muda'  => '#FFA500',
        'oranye terang' => '#FF8C00',
        'oranye cerah'  => '#FF8C00',
        'orange tua'   => '#FF4500',
        'orange muda'  => '#FFA500',
        'orange cerah' => '#FF8C00',
        'oranye kuning' => '#FFD700',
        'oranye salmon' => '#FFA07A',

        // ===== HITAM & PUTIH =====
        'hitam pekat'  => '#000000',
        'hitam legam'  => '#000000',
        'hitam total'  => '#000000',
        'hitam arang'  => '#1C1C1C',
        'hitam pucat'  => '#2C2C2C',
        'putih bersih' => '#FFFFFF',
        'putih susu'   => '#FFF8F0',
        'putih gading' => '#FFFFF0',
        'gading'       => '#FFFFF0',
        'putih krem'   => '#FFFDD0',
        'putih pucat'  => '#FAFAFA',
        'putih tulang' => '#F5F5DC',
        'ivory'        => '#FFFFF0',
        'cream'        => '#FFFDD0',

        // ===== WARNA KHUSUS / EFEK =====
        'transparan'   => '#00000000',
        'natural'      => '#D2B48C',
        'netral'       => '#A9A9A9',
        'pastel'       => '#B0C4DE',
        'warna kulit'  => '#F5DEB3',
        'kulit'        => '#F5DEB3',
        'coral'        => '#FF7F50',
        'gold'         => '#FFD700',
        'brown'        => '#8B4513',
        'khaki'        => '#C3B091',
        'cokelat khaki' => '#C3B091',
        'chocolate'    => '#7B3F00',
        'cokelat cokelat' => '#8B4513',
        'teal'         => '#008080',
        'hijau kebiruan' => '#008080',
        'indigo'       => '#4B0082',
        'nila'         => '#4B0082',
        'tan'          => '#D2B48C',
        'cokelat tan'  => '#D2B48C',

        // ===== VARIAN UMUM LAINNYA =====
        'biru dongker tua' => '#000080',
        'biru langit cerah' => '#00BFFF',
        'hijau daun tua' => '#006400',
        'kuning mentega' => '#FFFACD',
        'kuning telur'  => '#FFD700',
        'orange jingga' => '#FF8C00',
        'jingga'        => '#FF8C00',
        'merah labu'    => '#DC143C',
        'merah sekar'   => '#DC143C',
        'cokelat pirang' => '#D2B48C',
        'pirang'        => '#D2B48C',
        'biru soft'     => '#ADD8E6',
        'biru baby'     => '#89CFF0',
        'pink soft'     => '#FFD1DC',
        'hijau soft'    => '#B2D8B2',
        'kuning soft'   => '#FFFACD',
        'abu soft'      => '#D3D3D3',
        'ungu soft'     => '#E6E6FA',
        'biru donker'   => '#000080',  // common typo
        'coklat donker' => '#5C4033',  // common typo
        'ijo'           => '#228B22',
        'ijo tua'       => '#006400',
        'ijo muda'      => '#90EE90',
        'biru donk'     => '#000080',
        'item'          => '#1A1A1A',   // bahasa jawa
        'abang'         => '#DC143C',   // bahasa jawa
        'kuning bata'   => '#FF8C00',
    ];

    /**
     * Default fallback color jika warna tidak dikenal atau null.
     * Gunakan warna random konsisten dari hash.
     */
    const DEFAULT_COLOR = '#CCCCCC';
    
    /**
     * Digunakan untuk varian yang tidak memiliki warna (NULL / Tidak Ada Warna / Polos).
     * Semua dikembalikan ke warna COKELAT (#8B4513) — warna kulit/kayu natural.
     * 
     * @param string $namaMaterial Nama material/bahan (tidak dipakai)
     * @return string Kode Hex cokelat (dengan #)
     */
    public static function getColorForNull(string $namaMaterial): string
    {
        return '#8B4513';
    }

    /**
     * Deteksi kode Hex dari nama warna.
     *
     * Algoritma:
     * 1. Trim & lowercase input.
     * 2. Cari exact match di kamus.
     * 3. Jika tidak cocok, generate warna Hex konsisten dari hash kriptografis
     *    (menggunakan SHA256 — lebih stabil daripada MD5 untuk konsistensi).
     * 4. Pastikan warna tidak terlalu gelap (minimal brightness).
     *
     * @param string|null $namaWarna
     * @return string Kode Hex (dengan #)
     */
    public static function detectColor(?string $namaWarna): string
    {
        if (empty(trim($namaWarna ?? ''))) {
            return self::DEFAULT_COLOR;
        }

        // Step 1: Normalisasi
        $normalized = trim(mb_strtolower($namaWarna));
        $normalized = preg_replace('/\s+/', ' ', $normalized); // ganti multiple spasi jadi 1 spasi

        // Step 2: Cek exact match di kamus
        if (isset(self::$KAMUS_WARNA[$normalized])) {
            return self::$KAMUS_WARNA[$normalized];
        }

        // Step 3: Fallback — generate warna konsisten dari hash
        return self::generateConsistentColor($normalized);
    }

    /**
     * Generate warna Hex yang konsisten (deterministic) dari string.
     * Menggunakan SHA256 agar lebih stabil. Menjamin warna tidak terlalu gelap
     * dengan me-OR-kan byte pertama dengan 0x44.
     *
     * @param string $input
     * @return string Kode Hex (dengan #)
     */
    public static function generateConsistentColor(string $input): string
    {
        // Ambil 3 byte pertama dari SHA256
        $hash = hash('sha256', $input);
        $r = hexdec(substr($hash, 0, 2));
        $g = hexdec(substr($hash, 2, 2));
        $b = hexdec(substr($hash, 4, 2));

        // Pastikan setiap channel minimal 68 (0x44) agar tidak terlalu gelap
        $r = max($r, 68);
        $g = max($g, 68);
        $b = max($b, 68);

        // Pastikan tidak terlalu terang (maks 230) agar tetap terbaca di bg putih
        $r = min($r, 230);
        $g = min($g, 230);
        $b = min($b, 230);

        return sprintf('#%02X%02X%02X', $r, $g, $b);
    }
}