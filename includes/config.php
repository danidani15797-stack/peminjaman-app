<?php
// config.php — bootstraps the session. No database is used anywhere in this
// project: every table from the ERD (barang, siswa, peminjaman, pengembalian)
// lives as a PHP array in includes/data.php and is kept alive in $_SESSION
// for the duration of the admin's session.

session_start();

date_default_timezone_set('Asia/Jakarta');

function require_login() {
    if (empty($_SESSION['login'])) {
        header('Location: login.php');
        exit;
    }
}

// Batasi halaman ke role tertentu. Siswa yang mencoba membuka halaman admin
// (Barang, Siswa, Laporan, Dashboard) otomatis diarahkan ke Peminjaman.
function require_role(array $allowed_roles) {
    $role = $_SESSION['role'] ?? '';
    if (!in_array($role, $allowed_roles, true)) {
        flash_set('Halaman tersebut tidak tersedia untuk akun Siswa.', 'error');
        header('Location: ' . ($role === 'siswa' ? 'peminjaman.php' : 'dashboard.php'));
        exit;
    }
}

function h($str) {
    return htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8');
}

function format_tanggal($tgl) {
    if (!$tgl) return '-';
    $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $t = strtotime($tgl);
    return date('d', $t) . ' ' . $bulan[(int) date('n', $t)] . ' ' . date('Y', $t);
}

function flash_set($msg, $type = 'ok') {
    $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
}

function flash_get() {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}
