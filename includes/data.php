<?php
// data.php — the entire "database" of this app, hardcoded as PHP arrays and
// kept alive in $_SESSION. This mirrors the ERD from the jobsheet:
//
//   SISWA (1) ───< PEMINJAMAN >─── (1) BARANG
//                       │ 1
//                       │
//                       ▼ 1
//                 PENGEMBALIAN
//
// - peminjaman.id_siswa   → FK ke siswa.id
// - peminjaman.id_barang  → FK ke barang.id
// - pengembalian.id_peminjaman → FK ke peminjaman.id (relasi 1-ke-1)

function init_data() {
    if (isset($_SESSION['data_init'])) return;

    $_SESSION['barang'] = [
        ['id' => 'BR-001', 'nama' => 'Laptop Asus Vivobook X441', 'kategori' => 'Laptop',   'stok' => 8,  'kondisi' => 'Baik'],
        ['id' => 'BR-002', 'nama' => 'Laptop Lenovo ThinkPad E14',  'kategori' => 'Laptop',   'stok' => 5,  'kondisi' => 'Baik'],
        ['id' => 'BR-003', 'nama' => 'Proyektor Epson EB-X05',      'kategori' => 'Proyektor','stok' => 3,  'kondisi' => 'Baik'],
        ['id' => 'BR-004', 'nama' => 'Router Mikrotik RB941',       'kategori' => 'Jaringan', 'stok' => 6,  'kondisi' => 'Baik'],
        ['id' => 'BR-005', 'nama' => 'Switch TP-Link 8 Port',       'kategori' => 'Jaringan', 'stok' => 4,  'kondisi' => 'Baik'],
        ['id' => 'BR-006', 'nama' => 'Kabel LAN Cat6 (roll)',       'kategori' => 'Jaringan', 'stok' => 12, 'kondisi' => 'Baik'],
        ['id' => 'BR-007', 'nama' => 'Mouse Wireless Logitech',     'kategori' => 'Aksesoris','stok' => 15, 'kondisi' => 'Baik'],
        ['id' => 'BR-008', 'nama' => 'Headset Praktik Audio',       'kategori' => 'Aksesoris','stok' => 10, 'kondisi' => 'Rusak Ringan'],
    ];

    $_SESSION['siswa'] = [
        ['id' => 'SW-01', 'nama' => 'Ahmad Fauzan',      'kelas' => 'XII RPL 1', 'absen' => 4],
        ['id' => 'SW-02', 'nama' => 'Siti Nurhaliza',    'kelas' => 'XII RPL 1', 'absen' => 18],
        ['id' => 'SW-03', 'nama' => 'Bagas Prakoso',     'kelas' => 'XII RPL 2', 'absen' => 7],
        ['id' => 'SW-04', 'nama' => 'Dewi Anjani',       'kelas' => 'XII TKJ 1', 'absen' => 11],
        ['id' => 'SW-05', 'nama' => 'Rizky Maulana',     'kelas' => 'XII TKJ 2', 'absen' => 2],
        ['id' => 'SW-06', 'nama' => 'Putri Wulandari',   'kelas' => 'XII RPL 2', 'absen' => 21],
    ];

    $_SESSION['peminjaman'] = [
        ['id' => 'PJ-0001', 'id_siswa' => 'SW-01', 'id_barang' => 'BR-001', 'jumlah' => 1, 'tgl_pinjam' => '2026-09-10', 'tgl_wajib_kembali' => '2026-09-17', 'status' => 'Dipinjam'],
        ['id' => 'PJ-0002', 'id_siswa' => 'SW-02', 'id_barang' => 'BR-004', 'jumlah' => 1, 'tgl_pinjam' => '2026-09-12', 'tgl_wajib_kembali' => '2026-09-19', 'status' => 'Dipinjam'],
        ['id' => 'PJ-0003', 'id_siswa' => 'SW-03', 'id_barang' => 'BR-006', 'jumlah' => 2, 'tgl_pinjam' => '2026-09-05', 'tgl_wajib_kembali' => '2026-09-12', 'status' => 'Selesai'],
        ['id' => 'PJ-0004', 'id_siswa' => 'SW-04', 'id_barang' => 'BR-003', 'jumlah' => 1, 'tgl_pinjam' => '2026-09-14', 'tgl_wajib_kembali' => '2026-09-21', 'status' => 'Dipinjam'],
        ['id' => 'PJ-0005', 'id_siswa' => 'SW-05', 'id_barang' => 'BR-007', 'jumlah' => 1, 'tgl_pinjam' => '2026-09-01', 'tgl_wajib_kembali' => '2026-09-08', 'status' => 'Selesai'],
    ];

    $_SESSION['pengembalian'] = [
        ['id' => 'PG-0001', 'id_peminjaman' => 'PJ-0003', 'tgl_kembali' => '2026-09-11', 'kondisi' => 'Baik', 'catatan' => 'Lengkap, kondisi baik.'],
        ['id' => 'PG-0002', 'id_peminjaman' => 'PJ-0005', 'tgl_kembali' => '2026-09-07', 'kondisi' => 'Baik', 'catatan' => '-'],
    ];

    $_SESSION['data_init'] = true;
}

function next_id($prefix, $table) {
    $max = 0;
    foreach ($_SESSION[$table] as $row) {
        $n = (int) substr($row['id'], strlen($prefix) + 1);
        if ($n > $max) $max = $n;
    }
    $width = ($prefix === 'SW') ? 2 : 4;
    return $prefix . '-' . str_pad($max + 1, $width, '0', STR_PAD_LEFT);
}

function find_row($table, $id) {
    foreach ($_SESSION[$table] as $row) {
        if ($row['id'] === $id) return $row;
    }
    return null;
}

function siswa_nama($id) {
    $s = find_row('siswa', $id);
    return $s ? $s['nama'] : '(siswa dihapus)';
}

function barang_nama($id) {
    $b = find_row('barang', $id);
    return $b ? $b['nama'] : '(barang dihapus)';
}

function peminjaman_label($id) {
    $p = find_row('peminjaman', $id);
    if (!$p) return '(data dihapus)';
    return $p['id'] . ' — ' . siswa_nama($p['id_siswa']) . ' / ' . barang_nama($p['id_barang']);
}

// Barang yang stoknya masih tersedia (dipakai di form peminjaman)
function barang_tersedia() {
    return array_filter($_SESSION['barang'], fn($b) => $b['stok'] > 0);
}

// Peminjaman yang masih berstatus "Dipinjam" dan belum punya baris pengembalian
function peminjaman_belum_kembali() {
    $sudah = array_column($_SESSION['pengembalian'], 'id_peminjaman');
    return array_filter($_SESSION['peminjaman'], fn($p) => $p['status'] === 'Dipinjam' && !in_array($p['id'], $sudah));
}
