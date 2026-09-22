<?php
// header.php — expects $page_title, $page_sub (optional), $active to be set
// by the including page before this file is required.
$active = $active ?? '';
$flash = flash_get();
$role = $_SESSION['role'] ?? '';
$is_siswa = $role === 'siswa';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($page_title) ?> — Peminjaman Lab</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="shell">

    <div class="scrim"></div>

    <aside class="sidebar">
        <div class="brand">
            <div class="brand-mark">LB</div>
            <div class="brand-text">
                <div class="t1">Peminjaman Lab</div>
                <div class="t2">RPL &amp; TKJ · SMK</div>
            </div>
        </div>

        <nav class="nav">
            <?php if (!$is_siswa): ?>
            <a href="dashboard.php" class="<?= $active === 'dashboard' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3.5" y="3.5" width="7" height="7" rx="1.3"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.3"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.3"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.3"/></svg>
                Dashboard
            </a>
            <div class="nav-group-label">Data induk</div>
            <a href="barang.php" class="<?= $active === 'barang' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z"/><path d="M4 7.5 12 12l8-4.5"/><path d="M12 12v9"/></svg>
                Data Barang
            </a>
            <a href="siswa.php" class="<?= $active === 'siswa' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="8" r="3.4"/><path d="M4.5 20c1.2-4 4-6 7.5-6s6.3 2 7.5 6"/></svg>
                Data Siswa
            </a>
            <div class="nav-group-label">Transaksi</div>
            <?php endif; ?>
            <a href="peminjaman.php" class="<?= $active === 'peminjaman' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 12h11"/><path d="M11 7l5 5-5 5"/><path d="M15 4.5h4.5V19H15"/></svg>
                Peminjaman
            </a>
            <a href="pengembalian.php" class="<?= $active === 'pengembalian' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M20 12H9"/><path d="M13 7l-5 5 5 5"/><path d="M9 4.5H4.5V19H9"/></svg>
                Pengembalian
            </a>
            <?php if (!$is_siswa): ?>
            <a href="laporan.php" class="<?= $active === 'laporan' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M6 3.5h9l3.5 3.5V20.5H6Z"/><path d="M15 3.5V7h3.5"/><path d="M8.5 12h7M8.5 15.5h7M8.5 8.5h3"/></svg>
                Laporan
            </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-foot">
            <div class="admin-chip">
                <div class="admin-avatar"><?= h(strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1))) ?></div>
                <div>
                    <div class="admin-name"><?= h($_SESSION['nama'] ?? 'Admin') ?></div>
                    <div class="admin-role"><?= h(ucfirst($_SESSION['role'] ?? 'Administrator')) ?></div>
                </div>
            </div>
            <a href="logout.php" class="btn btn-ghost btn-block btn-sm">Keluar</a>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <div style="display:flex; align-items:center; gap:14px;">
                <button class="menu-btn" aria-label="Buka menu">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                </button>
                <div>
                    <h1><?= h($page_title) ?></h1>
                    <?php if (!empty($page_sub)): ?><div class="sub"><?= h($page_sub) ?></div><?php endif; ?>
                </div>
            </div>
            <div class="topbar-right">
                <span class="clock"></span>
            </div>
        </header>

        <div class="content">
            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] === 'error' ? 'error' : 'ok' ?>"><?= h($flash['msg']) ?></div>
            <?php endif; ?>
