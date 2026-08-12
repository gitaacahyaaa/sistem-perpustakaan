<?php
// Tarif denda keterlambatan per hari
define('TARIF_DENDA', 2000);

/**
 * Catat aktivitas ke tabel log_aktivitas
 */
function catatLog(PDO $pdo, $userId, $aktivitas, $keterangan = '')
{
    $stmt = $pdo->prepare(
        "INSERT INTO log_aktivitas (user_id, aktivitas, keterangan, waktu) VALUES (?, ?, ?, NOW())"
    );
    $stmt->execute([$userId, $aktivitas, $keterangan]);
}

/**
 * Format angka ke Rupiah, contoh: 12000 -> "Rp 12.000"
 */
function formatRupiah($angka)
{
    return 'Rp ' . number_format((float) $angka, 0, ',', '.');
}

/**
 * Format tanggal Y-m-d ke format Indonesia, contoh: "12 Agustus 2026"
 */
function formatTanggal($tanggal)
{
    if (!$tanggal) {
        return '-';
    }
    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $ts = strtotime($tanggal);
    $d  = date('j', $ts);
    $m  = (int) date('n', $ts);
    $y  = date('Y', $ts);
    return "$d {$bulan[$m]} $y";
}

/**
 * Generate kode unik dengan prefix, contoh: generateKode($pdo, 'buku', 'kode_buku', 'BK')
 * menghasilkan BK001, BK002, dst berdasarkan jumlah baris yang ada.
 */
function generateKode(PDO $pdo, $table, $column, $prefix)
{
    $stmt = $pdo->query("SELECT COUNT(*) AS jumlah FROM {$table}");
    $jumlah = (int) $stmt->fetch()['jumlah'];
    do {
        $jumlah++;
        $kode = $prefix . str_pad($jumlah, 3, '0', STR_PAD_LEFT);
        $cek = $pdo->prepare("SELECT COUNT(*) AS jumlah FROM {$table} WHERE {$column} = ?");
        $cek->execute([$kode]);
    } while ((int) $cek->fetch()['jumlah'] > 0);
    return $kode;
}

/**
 * Redirect dengan pesan flash sederhana lewat session
 */
function redirectDengan($url, $tipe, $pesan)
{
    $_SESSION['flash_tipe']  = $tipe; // 'success' | 'danger'
    $_SESSION['flash_pesan'] = $pesan;
    header('Location: ' . $url);
    exit;
}

/**
 * Tampilkan & bersihkan pesan flash (dipanggil dari header.php)
 */
function tampilkanFlash()
{
    if (isset($_SESSION['flash_pesan'])) {
        $tipe  = $_SESSION['flash_tipe'];
        $pesan = htmlspecialchars($_SESSION['flash_pesan']);
        echo "<div class=\"alert alert-{$tipe} alert-dismissible fade show\" role=\"alert\">
                {$pesan}
                <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button>
              </div>";
        unset($_SESSION['flash_tipe'], $_SESSION['flash_pesan']);
    }
}
