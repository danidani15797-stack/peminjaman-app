<?php
require_once __DIR__ . '/includes/config.php';
require_login();
require_once __DIR__ . '/includes/data.php';
init_data();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id_peminjaman = $_POST['id_peminjaman'] ?? '';
        $tgl_kembali = $_POST['tgl_kembali'] ?: date('Y-m-d');
        $kondisi = $_POST['kondisi'] ?? 'Baik';
        $catatan = trim($_POST['catatan'] ?? '') ?: '-';

        $p = find_row('peminjaman', $id_peminjaman);

        if (!$p || $p['status'] !== 'Dipinjam') {
            flash_set('Pilih transaksi peminjaman yang masih berstatus dipinjam.', 'error');
        } else {
            $_SESSION['pengembalian'][] = [
                'id' => next_id('PG', 'pengembalian'), 'id_peminjaman' => $id_peminjaman,
                'tgl_kembali' => $tgl_kembali, 'kondisi' => $kondisi, 'catatan' => $catatan,
            ];

            // relasi: tutup status peminjaman & kembalikan stok barang (FK id_barang)
            foreach ($_SESSION['peminjaman'] as &$row) {
                if ($row['id'] === $id_peminjaman) { $row['status'] = 'Selesai'; break; }
            }
            unset($row);

            foreach ($_SESSION['barang'] as &$b) {
                if ($b['id'] === $p['id_barang']) {
                    $b['stok'] += $p['jumlah'];
                    if ($kondisi !== 'Baik') $b['kondisi'] = $kondisi;
                    break;
                }
            }
            unset($b);

            flash_set('Pengembalian tercatat, stok barang otomatis diperbarui.');
        }
    }

    header('Location: pengembalian.php');
    exit;
}

$prefill = $_GET['loan'] ?? '';
$daftar = $_SESSION['pengembalian'];
usort($daftar, fn($a, $b) => strcmp($b['tgl_kembali'], $a['tgl_kembali']));
$belum_kembali = peminjaman_belum_kembali();

$page_title = 'Pengembalian';
$page_sub = 'Catat barang yang sudah dikembalikan siswa';
$active = 'pengembalian';
require __DIR__ . '/includes/header.php';
?>

<div class="grid-2" style="grid-template-columns: 1fr 340px; align-items: start;">

    <div>
        <div class="panel">
            <div class="panel-head">
                <div>
                    <h3>Riwayat pengembalian</h3>
                    <div class="desc">Setiap baris terhubung ke satu transaksi peminjaman</div>
                </div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Kode</th><th>Transaksi</th><th>Peminjam</th><th>Barang</th><th>Tgl kembali</th><th>Kondisi</th></tr></thead>
                    <tbody>
                    <?php foreach ($daftar as $r): $p = find_row('peminjaman', $r['id_peminjaman']); ?>
                        <tr>
                            <td><span class="code-tag"><?= h($r['id']) ?></span></td>
                            <td><span class="code-tag"><?= h($r['id_peminjaman']) ?></span></td>
                            <td><?= $p ? h(siswa_nama($p['id_siswa'])) : '-' ?></td>
                            <td><?= $p ? h(barang_nama($p['id_barang'])) : '-' ?></td>
                            <td><?= format_tanggal($r['tgl_kembali']) ?></td>
                            <td>
                                <?php if ($r['kondisi'] === 'Baik'): ?>
                                    <span class="badge badge-teal">Baik</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><?= h($r['kondisi']) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$daftar): ?>
                        <tr><td colspan="6"><div class="empty-state"><div class="t">Belum ada pengembalian</div>Catat pengembalian lewat panel di samping.</div></td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="form-card">
        <h3>Catat pengembalian</h3>
        <div class="desc">Stok barang bertambah otomatis sesuai jumlah</div>
        <?php if ($belum_kembali): ?>
        <form method="post">
            <input type="hidden" name="action" value="save">
            <div class="field">
                <label>Transaksi peminjaman</label>
                <select name="id_peminjaman" required>
                    <option value="">Pilih transaksi</option>
                    <?php foreach ($belum_kembali as $p): ?>
                        <option value="<?= h($p['id']) ?>" <?= $prefill === $p['id'] ? 'selected' : '' ?>>
                            <?= h($p['id']) ?> — <?= h(siswa_nama($p['id_siswa'])) ?> / <?= h(barang_nama($p['id_barang'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label>Tanggal kembali</label>
                <input type="date" name="tgl_kembali" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="field">
                <label>Kondisi saat kembali</label>
                <select name="kondisi">
                    <?php foreach (['Baik', 'Rusak Ringan', 'Rusak Berat'] as $k): ?>
                        <option value="<?= $k ?>"><?= $k ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label>Catatan (opsional)</label>
                <textarea name="catatan" placeholder="Contoh: kabel charger tertinggal"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-block">Simpan pengembalian</button>
            </div>
        </form>
        <?php else: ?>
            <div class="empty-state" style="padding: 24px 4px;">
                <div class="t">Semua barang sudah kembali</div>
                Tidak ada transaksi peminjaman yang masih menunggu pengembalian.
            </div>
        <?php endif; ?>
    </div>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
