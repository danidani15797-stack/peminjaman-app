<?php
require_once __DIR__ . '/includes/config.php';
require_login();
require_role(['admin', 'petugas']);
require_once __DIR__ . '/includes/data.php';
init_data();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = trim($_POST['id'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $kelas = trim($_POST['kelas'] ?? '');
        $absen = max(0, (int) ($_POST['absen'] ?? 0));

        if ($nama === '' || $kelas === '') {
            flash_set('Nama dan kelas siswa wajib diisi.', 'error');
        } elseif ($id === '') {
            $_SESSION['siswa'][] = ['id' => next_id('SW', 'siswa'), 'nama' => $nama, 'kelas' => $kelas, 'absen' => $absen];
            flash_set('Siswa baru berhasil ditambahkan.');
        } else {
            foreach ($_SESSION['siswa'] as &$s) {
                if ($s['id'] === $id) { $s['nama'] = $nama; $s['kelas'] = $kelas; $s['absen'] = $absen; break; }
            }
            unset($s);
            flash_set('Data siswa berhasil diperbarui.');
        }
    }

    if ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $dipakai = array_filter($_SESSION['peminjaman'], fn($p) => $p['id_siswa'] === $id);
        if ($dipakai) {
            flash_set('Siswa ini memiliki riwayat peminjaman dan tidak bisa dihapus.', 'error');
        } else {
            $_SESSION['siswa'] = array_values(array_filter($_SESSION['siswa'], fn($s) => $s['id'] !== $id));
            flash_set('Siswa berhasil dihapus.');
        }
    }

    header('Location: siswa.php');
    exit;
}

$edit = null;
if (isset($_GET['edit'])) {
    $edit = find_row('siswa', $_GET['edit']);
}

$page_title = 'Data Siswa';
$page_sub = 'Siswa yang terdaftar dapat meminjam barang lab';
$active = 'siswa';
require __DIR__ . '/includes/header.php';
?>

<div class="grid-2" style="grid-template-columns: 1fr 320px; align-items: start;">

    <div>
        <div class="toolbar">
            <div class="search-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.2-3.2"/></svg>
                <input type="text" placeholder="Cari nama atau kelas…" data-table-search="#tabel-siswa">
            </div>
        </div>

        <div class="panel">
            <div class="table-wrap">
                <table id="tabel-siswa">
                    <thead><tr><th>Kode</th><th>Nama</th><th>Kelas</th><th>No. absen</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($_SESSION['siswa'] as $s): ?>
                        <tr>
                            <td><span class="code-tag"><?= h($s['id']) ?></span></td>
                            <td><?= h($s['nama']) ?></td>
                            <td><?= h($s['kelas']) ?></td>
                            <td><?= (int) $s['absen'] ?></td>
                            <td>
                                <div class="row-actions">
                                    <a class="icon-btn" href="siswa.php?edit=<?= h($s['id']) ?>" title="Ubah">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 20h4l10-10-4-4L4 16v4Z"/></svg>
                                    </a>
                                    <form method="post" data-confirm="Hapus <?= h(addslashes($s['nama'])) ?> dari data siswa?">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= h($s['id']) ?>">
                                        <button type="submit" class="icon-btn" title="Hapus">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 7h14M9 7V5h6v2M7 7l1 13h8l1-13"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$_SESSION['siswa']): ?>
                        <tr><td colspan="5"><div class="empty-state"><div class="t">Belum ada siswa</div>Tambahkan siswa pertama lewat panel di samping.</div></td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="form-card">
        <h3><?= $edit ? 'Ubah siswa' : 'Tambah siswa' ?></h3>
        <div class="desc"><?= $edit ? 'Kode ' . h($edit['id']) : 'Kode akan dibuat otomatis' ?></div>
        <form method="post">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= h($edit['id'] ?? '') ?>">
            <div class="field">
                <label>Nama siswa</label>
                <input type="text" name="nama" value="<?= h($edit['nama'] ?? '') ?>" placeholder="Nama lengkap" required>
            </div>
            <div class="field">
                <label>Kelas</label>
                <input type="text" name="kelas" value="<?= h($edit['kelas'] ?? '') ?>" placeholder="Contoh: XII RPL 1" required>
            </div>
            <div class="field">
                <label>Nomor absen</label>
                <input type="number" min="0" name="absen" value="<?= h($edit['absen'] ?? '') ?>">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-block"><?= $edit ? 'Simpan perubahan' : 'Tambah siswa' ?></button>
                <?php if ($edit): ?><a href="siswa.php" class="btn btn-ghost">Batal</a><?php endif; ?>
            </div>
        </form>
    </div>

</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
