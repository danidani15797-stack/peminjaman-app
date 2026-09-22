<?php
require_once __DIR__ . '/includes/config.php';
require_login();
require_once __DIR__ . '/includes/data.php';
init_data();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id_siswa = $_POST['id_siswa'] ?? '';
        $id_barang = $_POST['id_barang'] ?? '';
        $jumlah = max(1, (int) ($_POST['jumlah'] ?? 1));
        $tgl_pinjam = $_POST['tgl_pinjam'] ?: date('Y-m-d');
        $tgl_wajib = $_POST['tgl_wajib_kembali'] ?: date('Y-m-d', strtotime($tgl_pinjam . ' +7 days'));

        $siswa = find_row('siswa', $id_siswa);
        $barang = find_row('barang', $id_barang);

        if (!$siswa || !$barang) {
            flash_set('Pilih siswa dan barang yang valid.', 'error');
        } elseif ($jumlah > $barang['stok']) {
            flash_set('Jumlah melebihi stok tersedia (' . $barang['stok'] . ' unit).', 'error');
        } else {
            // relasi: kurangi stok barang (FK id_barang), catat FK id_siswa
            foreach ($_SESSION['barang'] as &$b) {
                if ($b['id'] === $id_barang) { $b['stok'] -= $jumlah; break; }
            }
            unset($b);

            $_SESSION['peminjaman'][] = [
                'id' => next_id('PJ', 'peminjaman'), 'id_siswa' => $id_siswa, 'id_barang' => $id_barang,
                'jumlah' => $jumlah, 'tgl_pinjam' => $tgl_pinjam, 'tgl_wajib_kembali' => $tgl_wajib, 'status' => 'Dipinjam',
            ];
            flash_set('Peminjaman berhasil dicatat, stok barang otomatis diperbarui.');
        }
    }

    // Siswa hanya boleh meminjam (save) dan mengembalikan lewat pengembalian.php,
    // tidak boleh menghapus transaksi langsung.
    if ($action === 'delete' && ($_SESSION['role'] ?? '') === 'siswa') {
        flash_set('Akun Siswa tidak dapat menghapus transaksi peminjaman.', 'error');
        header('Location: peminjaman.php');
        exit;
    }

    if ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $p = find_row('peminjaman', $id);
        $ada_pengembalian = array_filter($_SESSION['pengembalian'], fn($r) => $r['id_peminjaman'] === $id);
        if ($ada_pengembalian) {
            flash_set('Transaksi ini sudah memiliki catatan pengembalian dan tidak bisa dihapus langsung.', 'error');
        } elseif ($p) {
            if ($p['status'] === 'Dipinjam') {
                foreach ($_SESSION['barang'] as &$b) {
                    if ($b['id'] === $p['id_barang']) { $b['stok'] += $p['jumlah']; break; }
                }
                unset($b);
            }
            $_SESSION['peminjaman'] = array_values(array_filter($_SESSION['peminjaman'], fn($r) => $r['id'] !== $id));
            flash_set('Transaksi peminjaman dihapus, stok dikembalikan.');
        }
    }

    header('Location: peminjaman.php');
    exit;
}

$daftar = $_SESSION['peminjaman'];
usort($daftar, fn($a, $b) => strcmp($b['tgl_pinjam'], $a['tgl_pinjam']));

$page_title = 'Peminjaman';
$page_sub = 'Catat transaksi peminjaman barang lab oleh siswa';
$active = 'peminjaman';
require __DIR__ . '/includes/header.php';
?>

<div class="grid-2" style="grid-template-columns: 1fr 340px; align-items: start;">

    <div>
        <div class="toolbar">
            <div class="search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.2-3.2"/></svg>
                <input type="text" placeholder="Cari peminjam atau barang…" data-table-search="#tabel-peminjaman">
            </div>
        </div>

        <div class="panel">
            <div class="table-wrap">
                <table id="tabel-peminjaman">
                    <thead><tr><th>Kode</th><th>Peminjam</th><th>Barang</th><th>Jml</th><th>Tgl pinjam</th><th>Wajib kembali</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($daftar as $p): ?>
                        <tr>
                            <td><span class="code-tag"><?= h($p['id']) ?></span></td>
                            <td><?= h(siswa_nama($p['id_siswa'])) ?></td>
                            <td><?= h(barang_nama($p['id_barang'])) ?></td>
                            <td><?= (int) $p['jumlah'] ?></td>
                            <td><?= format_tanggal($p['tgl_pinjam']) ?></td>
                            <td><?= format_tanggal($p['tgl_wajib_kembali']) ?></td>
                            <td>
                                <?php if ($p['status'] === 'Dipinjam'): ?>
                                    <span class="badge badge-amber">Dipinjam</span>
                                <?php else: ?>
                                    <span class="badge badge-teal">Selesai</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="row-actions">
                                    <?php if ($p['status'] === 'Dipinjam'): ?>
                                        <a class="icon-btn" href="pengembalian.php?loan=<?= h($p['id']) ?>" title="Proses pengembalian">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 12H9"/><path d="M13 7l-5 5 5 5"/></svg>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (($_SESSION['role'] ?? '') !== 'siswa'): ?>
                                    <form method="post" data-confirm="Hapus transaksi <?= h($p['id']) ?>?">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= h($p['id']) ?>">
                                        <button type="submit" class="icon-btn" title="Hapus">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 7h14M9 7V5h6v2M7 7l1 13h8l1-13"/></svg>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$daftar): ?>
                        <tr><td colspan="8"><div class="empty-state"><div class="t">Belum ada transaksi</div>Catat peminjaman pertama lewat panel di samping.</div></td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="form-card">
        <h3>Catat peminjaman baru</h3>
        <div class="desc">Stok barang berkurang otomatis sesuai jumlah</div>
        <form method="post">
            <input type="hidden" name="action" value="save">
            <div class="field">
                <label>Siswa peminjam</label>
                <select name="id_siswa" required>
                    <option value="">Pilih siswa</option>
                    <?php foreach ($_SESSION['siswa'] as $s): ?>
                        <option value="<?= h($s['id']) ?>"><?= h($s['nama']) ?> — <?= h($s['kelas']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label>Barang dipinjam</label>
                <select name="id_barang" required>
                    <option value="">Pilih barang</option>
                    <?php foreach (barang_tersedia() as $b): ?>
                        <option value="<?= h($b['id']) ?>"><?= h($b['nama']) ?> (stok <?= $b['stok'] ?>)</option>
                    <?php endforeach; ?>
                </select>
                <?php if (!barang_tersedia()): ?><div class="field-hint">Tidak ada barang dengan stok tersisa.</div><?php endif; ?>
            </div>
            <div class="field">
                <label>Jumlah</label>
                <input type="number" name="jumlah" min="1" value="1" required>
            </div>
            <div class="field">
                <label>Tanggal pinjam</label>
                <input type="date" name="tgl_pinjam" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="field">
                <label>Wajib kembali</label>
                <input type="date" name="tgl_wajib_kembali" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-block">Simpan peminjaman</button>
            </div>
        </form>
    </div>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
