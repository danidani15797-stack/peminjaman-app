<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$file = __DIR__ . '/data/riwayat.json';

if (!file_exists($file)) {
    file_put_contents($file, '[]');
}

$data = json_decode(file_get_contents($file), true);

if (!is_array($data)) {
    $data = [];
}

/* =========================
   FILTER PENCARIAN
========================= */

$keyword = trim($_GET['search'] ?? '');

if ($keyword !== '') {
    $data = array_filter($data, function ($item) use ($keyword) {

        return
            stripos($item['kode'] ?? '', $keyword) !== false ||
            stripos($item['peminjam'] ?? '', $keyword) !== false ||
            stripos($item['barang'] ?? '', $keyword) !== false ||
            stripos($item['status'] ?? '', $keyword) !== false;
    });
}

/* Data terbaru di atas */
$data = array_reverse($data);

?>
<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Riwayat Peminjaman — Peminjaman Lab</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="assets/style.css">

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background:
                radial-gradient(
                    circle at 10% 20%,
                    rgba(245, 158, 11, .08),
                    transparent 30%
                ),
                radial-gradient(
                    circle at 90% 80%,
                    rgba(20, 184, 166, .07),
                    transparent 30%
                ),
                #060b12;
            color: #e5e7eb;
            font-family: Inter, sans-serif;
        }

        .page {
            min-height: 100vh;
            display: flex;
        }

        /* SIDEBAR */

        .sidebar {
            width: 235px;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            padding: 28px 14px;
            background: rgba(7, 14, 24, .96);
            border-right: 1px solid rgba(148, 163, 184, .16);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 8px 25px;
            border-bottom: 1px solid rgba(148, 163, 184, .15);
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: linear-gradient(
                145deg,
                #fbbf24,
                #d97706
            );
            color: #111827;
            font-weight: 900;
            box-shadow: 0 0 30px rgba(245,158,11,.25);
        }

        .brand strong {
            display: block;
            font-size: 15px;
        }

        .brand small {
            color: #64748b;
            font-size: 11px;
        }

        .menu-title {
            margin: 25px 8px 10px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.4px;
            color: #64748b;
        }

        .menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            margin: 4px 0;
            border-radius: 10px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: .25s;
        }

        .menu a:hover,
        .menu a.active {
            color: #fbbf24;
            background: rgba(245,158,11,.10);
        }

        .menu-icon {
            width: 20px;
            text-align: center;
            font-size: 17px;
        }

        .user-box {
            position: absolute;
            bottom: 70px;
            left: 14px;
            right: 14px;
            padding: 13px;
            border: 1px solid rgba(148,163,184,.15);
            border-radius: 13px;
            background: rgba(15,23,42,.65);
        }

        .user-name {
            font-size: 12px;
            font-weight: 700;
        }

        .user-role {
            color: #64748b;
            font-size: 10px;
            margin-top: 3px;
        }

        .logout {
            position: absolute;
            bottom: 18px;
            left: 14px;
            right: 14px;
        }

        .logout a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 12px;
        }

        /* CONTENT */

        .content {
            margin-left: 235px;
            width: calc(100% - 235px);
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0;
            font-family: "Space Grotesk", sans-serif;
            font-size: 28px;
        }

        .header p {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 12px;
        }

        .date {
            color: #64748b;
            font-size: 12px;
        }

        /* STATS */

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat {
            padding: 20px;
            border-radius: 15px;
            border: 1px solid rgba(148,163,184,.14);
            background: rgba(15,23,42,.75);
        }

        .stat-label {
            color: #64748b;
            font-size: 11px;
        }

        .stat-value {
            margin-top: 7px;
            font-size: 27px;
            font-weight: 800;
        }

        /* CARD */

        .card {
            border: 1px solid rgba(148,163,184,.15);
            border-radius: 16px;
            background: rgba(15,23,42,.72);
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,.15);
        }

        .card-header {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(148,163,184,.12);
        }

        .card-title {
            font-size: 16px;
            font-weight: 800;
        }

        .search {
            width: 300px;
            padding: 10px 13px;
            border-radius: 9px;
            border: 1px solid #243449;
            outline: none;
            background: #09121e;
            color: white;
        }

        .search:focus {
            border-color: #f59e0b;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 14px 18px;
            text-align: left;
            font-size: 10px;
            letter-spacing: .7px;
            color: #64748b;
            background: rgba(255,255,255,.025);
        }

        td {
            padding: 17px 18px;
            border-top: 1px solid rgba(148,163,184,.09);
            font-size: 12px;
            color: #cbd5e1;
        }

        tr:hover td {
            background: rgba(245,158,11,.025);
        }

        .code {
            color: #fbbf24;
            font-weight: 700;
        }

        .badge {
            display: inline-flex;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
        }

        .badge-dipinjam {
            color: #fbbf24;
            background: rgba(245,158,11,.12);
        }

        .badge-selesai {
            color: #34d399;
            background: rgba(16,185,129,.12);
        }

        .badge-terlambat {
            color: #fb7185;
            background: rgba(244,63,94,.12);
        }

        .empty {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }

        .empty-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }

        @media(max-width: 900px) {

            .sidebar {
                width: 190px;
            }

            .content {
                margin-left: 190px;
                width: calc(100% - 190px);
                padding: 20px;
            }

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

        }

    </style>

</head>

<body>

<div class="page">

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="brand">

            <div class="brand-icon">
                LB
            </div>

            <div>
                <strong>Peminjaman Lab</strong>
                <small>RPL & TKJ · SMK</small>
            </div>

        </div>

        <div class="menu-title">
            MENU UTAMA
        </div>

        <nav class="menu">

            <a href="dashboard.php">
                <span class="menu-icon">▦</span>
                Dashboard
            </a>

            <a href="barang.php">
                <span class="menu-icon">◇</span>
                Data Barang
            </a>

            <a href="siswa.php">
                <span class="menu-icon">♙</span>
                Data Siswa
            </a>

        </nav>

        <div class="menu-title">
            TRANSAKSI
        </div>

        <nav class="menu">

            <a href="peminjaman.php">
                <span class="menu-icon">↪</span>
                Peminjaman
            </a>

            <a href="pengembalian.php">
                <span class="menu-icon">↩</span>
                Pengembalian
            </a>

            <a href="riwayat.php" class="active">
                <span class="menu-icon">◷</span>
                Riwayat
            </a>

            <a href="laporan.php">
                <span class="menu-icon">▤</span>
                Laporan
            </a>

        </nav>

        <div class="user-box">

            <div class="user-name">
                <?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?>
            </div>

            <div class="user-role">
                <?= htmlspecialchars($_SESSION['role'] ?? 'User') ?>
            </div>

        </div>

        <div class="logout">
            <a href="logout.php">
                ← &nbsp; Keluar
            </a>
        </div>

    </aside>


    <!-- CONTENT -->

    <main class="content">

        <div class="header">

            <div>

                <h1>Riwayat Peminjaman</h1>

                <p>
                    Rekap seluruh transaksi peminjaman laboratorium
                </p>

            </div>

            <div class="date">
                <?= date('l, d M Y') ?>
            </div>

        </div>


        <?php

        $total = count($data);

        $selesai = 0;
        $dipinjam = 0;
        $terlambat = 0;

        foreach ($data as $item) {

            $status = strtolower($item['status'] ?? '');

            if ($status === 'selesai') {
                $selesai++;
            }

            if ($status === 'dipinjam') {
                $dipinjam++;
            }

            if ($status === 'terlambat') {
                $terlambat++;
            }
        }

        ?>

        <div class="stats">

            <div class="stat">
                <div class="stat-label">
                    TOTAL PEMINJAMAN
                </div>
                <div class="stat-value">
                    <?= $total ?>
                </div>
            </div>

            <div class="stat">
                <div class="stat-label">
                    SEDANG DIPINJAM
                </div>
                <div class="stat-value">
                    <?= $dipinjam ?>
                </div>
            </div>

            <div class="stat">
                <div class="stat-label">
                    SELESAI
                </div>
                <div class="stat-value">
                    <?= $selesai ?>
                </div>
            </div>

            <div class="stat">
                <div class="stat-label">
                    TERLAMBAT
                </div>
                <div class="stat-value">
                    <?= $terlambat ?>
                </div>
            </div>

        </div>


        <section class="card">

            <div class="card-header">

                <div class="card-title">
                    Semua Riwayat Transaksi
                </div>

                <form method="GET">

                    <input
                        class="search"
                        type="search"
                        name="search"
                        value="<?= htmlspecialchars($keyword) ?>"
                        placeholder="Cari kode, siswa, atau barang..."
                    >

                </form>

            </div>


            <div class="table-wrap">

                <?php if (empty($data)): ?>

                    <div class="empty">

                        <div class="empty-icon">
                            ◷
                        </div>

                        Belum ada riwayat peminjaman.

                    </div>

                <?php else: ?>

                    <table>

                        <thead>

                        <tr>
                            <th>KODE</th>
                            <th>PEMINJAM</th>
                            <th>BARANG</th>
                            <th>JUMLAH</th>
                            <th>TGL PINJAM</th>
                            <th>WAJIB KEMBALI</th>
                            <th>STATUS</th>
                        </tr>

                        </thead>

                        <tbody>

                        <?php foreach ($data as $item): ?>

                            <?php
                            $status = strtolower($item['status'] ?? 'dipinjam');

                            $badge = 'badge-dipinjam';

                            if ($status === 'selesai') {
                                $badge = 'badge-selesai';
                            }

                            if ($status === 'terlambat') {
                                $badge = 'badge-terlambat';
                            }
                            ?>

                            <tr>

                                <td class="code">
                                    <?= htmlspecialchars($item['kode'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($item['peminjam'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($item['barang'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($item['jumlah'] ?? '1') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($item['tanggal_pinjam'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($item['tanggal_kembali'] ?? '-') ?>
                                </td>

                                <td>

                                    <span class="badge <?= $badge ?>">
                                        <?= htmlspecialchars(ucfirst($status)) ?>
                                    </span>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                <?php endif; ?>

            </div>

        </section>

    </main>

</div>

</body>
</html>