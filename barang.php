<?php
require_once __DIR__ . '/includes/config.php';
require_login();
require_role(['admin', 'petugas']);
require_once __DIR__ . '/includes/data.php';
init_data();

// ---- aksi tulis (create / update / delete) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = trim($_POST['id'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $kategori = trim($_POST['kategori'] ?? '');
        $stok = max(0, (int) ($_POST['stok'] ?? 0));
        $kondisi = trim($_POST['kondisi'] ?? 'Baik');

        if ($nama === '' || $kategori === '') {
            flash_set('Nama dan kategori barang wajib diisi.', 'error');
        } elseif ($id === '') {
            $_SESSION['barang'][] = [
                'id' => next_id('BR', 'barang'), 'nama' => $nama, 'kategori' => $kategori,
                'stok' => $stok, 'kondisi' => $kondisi,
            ];
            flash_set('Barang baru berhasil ditambahkan.');
        } else {
            foreach ($_SESSION['barang'] as &$b) {
                if ($b['id'] === $id) {
                    $b['nama'] = $nama; $b['kategori'] = $kategori; $b['stok'] = $stok; $b['kondisi'] = $kondisi;
                    break;
                }
            }
            unset($b);
            flash_set('Data barang berhasil diperbarui.');
        }
    }

    if ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $dipakai = array_filter($_SESSION['peminjaman'], fn($p) => $p['id_barang'] === $id && $p['status'] === 'Dipinjam');
        if ($dipakai) {
            flash_set('Barang ini masih ada dalam peminjaman aktif dan belum bisa dihapus.', 'error');
        } else {
            $_SESSION['barang'] = array_values(array_filter($_SESSION['barang'], fn($b) => $b['id'] !== $id));
            flash_set('Barang berhasil dihapus.');
        }
    }

    header('Location: barang.php');
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $edit = find_row('barang', $_GET['edit']);
}

$page_title = 'Data Barang';
$page_sub = 'Aset laboratorium yang dapat dipinjam';
$active = 'barang';
require __DIR__ . '/includes/header.php';
?>

<div class="grid-2" style="grid-template-columns: 1fr 320px; align-items: start;">

    <div>
        <div class="toolbar">
            <div class="search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.2-3.2"/></svg>
                <input type="text" placeholder="Cari nama atau kategori barang…" data-table-search="#tabel-barang">
            </div>
        </div>

        <div class="panel">
            <div class="table-wrap">
                <table id="tabel-barang">
                    <thead><tr><th>Kode</th><th>Nama barang</th><th>Kategori</th><th>Stok</th><th>Kondisi</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($_SESSION['barang'] as $b): ?>
                        <tr>
                            <td><span class="code-tag"><?= h($b['id']) ?></span></td>
                            <td><?= h($b['nama']) ?></td>
                            <td><?= h($b['kategori']) ?></td>
                            <td><?= $b['stok'] ?> unit</td>
                            <td>
                                <?php if ($b['kondisi'] === 'Baik'): ?>
                                    <span class="badge badge-teal">Baik</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><?= h($b['kondisi']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a class="icon-btn" href="barang.php?edit=<?= h($b['id']) ?>" title="Ubah">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 20h4l10-10-4-4L4 16v4Z"/></svg>
                                    </a>
                                    <form method="post" data-confirm="Hapus barang <?= h(addslashes($b['nama'])) ?> dari data?">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= h($b['id']) ?>">
                                        <button type="submit" class="icon-btn" title="Hapus">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 7h14M9 7V5h6v2M7 7l1 13h8l1-13"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$_SESSION['barang']): ?>
                        <tr><td colspan="6"><div class="empty-state"><div class="t">Belum ada barang</div>Tambahkan barang pertama lewat panel di samping.</div></td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="form-card">
        <h3><?= $edit ? 'Ubah barang' : 'Tambah barang' ?></h3>
        <div class="desc"><?= $edit ? 'Kode ' . h($edit['id']) : 'Kode akan dibuat otomatis' ?></div>
        <form method="post">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= h($edit['id'] ?? '') ?>">
            <div class="field">
                <label>Nama barang</label>
                <input type="text" name="nama" value="<?= h($edit['nama'] ?? '') ?>" placeholder="Contoh: Laptop Acer Aspire 5" required>
            </div>
            <div class="field">
                <label>Kategori</label>
                <input type="text" name="kategori" value="<?= h($edit['kategori'] ?? '') ?>" placeholder="Laptop / Jaringan / Aksesoris" required>
            </div>
            <div class="field">
                <label>Jumlah stok</label>
                <input type="number" min="0" name="stok" value="<?= h($edit['stok'] ?? 0) ?>" required>
            </div>
            <div class="field">
                <label>Kondisi</label>
                <select name="kondisi">
                    <?php foreach (['Baik', 'Rusak Ringan', 'Rusak Berat'] as $k): ?>
                        <option value="<?= $k ?>" <?= (($edit['kondisi'] ?? 'Baik') === $k) ? 'selected' : '' ?>><?= $k ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-block"><?= $edit ? 'Simpan perubahan' : 'Tambah barang' ?></button>
                <?php if ($edit): ?><a href="barang.php" class="btn btn-ghost">Batal</a><?php endif; ?>
            </div>
        </form>
    </div>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
