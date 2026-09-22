<?php
require_once __DIR__ . '/includes/config.php';
require_login();
require_role(['admin', 'petugas']);
require_once __DIR__ . '/includes/data.php';
init_data();

$filter_status = $_GET['status'] ?? 'Semua';
$dari = $_GET['dari'] ?? '';
$sampai = $_GET['sampai'] ?? '';

$rows = [];
foreach ($_SESSION['peminjaman'] as $p) {
    if ($filter_status !== 'Semua' && $p['status'] !== $filter_status) continue;
    if ($dari && $p['tgl_pinjam'] < $dari) continue;
    if ($sampai && $p['tgl_pinjam'] > $sampai) continue;

    $pengembalian = null;
    foreach ($_SESSION['pengembalian'] as $r) {
        if ($r['id_peminjaman'] === $p['id']) { $pengembalian = $r; break; }
    }

    $rows[] = [
        'peminjaman' => $p,
        'siswa' => siswa_nama($p['id_siswa']),
        'barang' => barang_nama($p['id_barang']),
        'pengembalian' => $pengembalian,
    ];
}
usort($rows, fn($a, $b) => strcmp($b['peminjaman']['tgl_pinjam'], $a['peminjaman']['tgl_pinjam']));

$page_title = 'Laporan';
$page_sub = 'Rekap seluruh transaksi peminjaman & pengembalian';
$active = 'laporan';
require __DIR__ . '/includes/header.php';
?>

<div class="print-only" style="margin-bottom:18px;">
    <h2 style="font-size:18px;">Laporan Peminjaman Barang Laboratorium</h2>
    <p style="color:#555; font-size:13px; margin-top:4px;">Dicetak <?= date('d/m/Y H:i') ?> — filter status: <?= h($filter_status) ?></p>
</div>

<form class="toolbar no-print" method="get">
    <div class="filters">
        <select name="status" onchange="this.form.submit()">
            <?php foreach (['Semua', 'Dipinjam', 'Selesai'] as $s): ?>
                <option value="<?= $s ?>" <?= $filter_status === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
        <input type="date" name="dari" value="<?= h($dari) ?>" onchange="this.form.submit()">
        <input type="date" name="sampai" value="<?= h($sampai) ?>" onchange="this.form.submit()">
    </div>
    <button type="button" class="btn btn-ghost btn-sm" onclick="window.print()">Cetak laporan</button>
</form>

<div class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kode</th><th>Peminjam</th><th>Barang</th><th>Jml</th>
                    <th>Tgl pinjam</th><th>Wajib kembali</th><th>Tgl kembali</th><th>Kondisi</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): $p = $row['peminjaman']; $r = $row['pengembalian']; ?>
                <tr>
                    <td><span class="code-tag"><?= h($p['id']) ?></span></td>
                    <td><?= h($row['siswa']) ?></td>
                    <td><?= h($row['barang']) ?></td>
                    <td><?= (int) $p['jumlah'] ?></td>
                    <td><?= format_tanggal($p['tgl_pinjam']) ?></td>
                    <td><?= format_tanggal($p['tgl_wajib_kembali']) ?></td>
                    <td><?= $r ? format_tanggal($r['tgl_kembali']) : '—' ?></td>
                    <td><?= $r ? h($r['kondisi']) : '—' ?></td>
                    <td>
                        <?php if ($p['status'] === 'Dipinjam'): ?>
                            <span class="badge badge-amber">Dipinjam</span>
                        <?php else: ?>
                            <span class="badge badge-teal">Selesai</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$rows): ?>
                <tr><td colspan="9"><div class="empty-state"><div class="t">Tidak ada data</div>Coba ubah filter status atau rentang tanggal.</div></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
