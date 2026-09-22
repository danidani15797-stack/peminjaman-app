<?php

session_start();

/*
|--------------------------------------------------------------------------
| PROTEKSI LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login.php');
    exit;
}

// Siswa tidak punya dashboard admin — langsung ke Peminjaman.
if (($_SESSION['role'] ?? '') === 'siswa') {
    header('Location: peminjaman.php');
    exit;
}

$nama = $_SESSION['nama'] ?? 'Administrator';
$username = $_SESSION['username'] ?? 'admin';

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard — Peminjaman Lab</title>

    <!-- FONT -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- ICON -->

    <script src="https://unpkg.com/lucide@latest"></script>

    <style>

        /* =====================================================
           RESET
        ===================================================== */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        :root {

            --bg: #050b12;
            --bg-soft: #09111c;

            --sidebar: rgba(8, 16, 27, .88);

            --card: rgba(15, 27, 42, .72);

            --border: rgba(148, 163, 184, .13);

            --text: #f8fafc;
            --muted: #8290a3;

            --gold: #f5b52c;
            --gold-light: #ffd166;
            --gold-dark: #d99308;

            --cyan: #22d3ee;

            --green: #22c55e;

            --red: #ef4444;

            --purple: #8b5cf6;

        }


        html {
            scroll-behavior: smooth;
        }


        body {

            font-family: 'Inter', sans-serif;

            background: var(--bg);

            color: var(--text);

            min-height: 100vh;

            overflow-x: hidden;

        }


        /* =====================================================
           ANIMATED BACKGROUND
        ===================================================== */

        .background {

            position: fixed;

            inset: 0;

            z-index: -10;

            overflow: hidden;

            background:

                radial-gradient(
                    circle at 15% 15%,
                    rgba(245, 181, 44, .10),
                    transparent 28%
                ),

                radial-gradient(
                    circle at 85% 70%,
                    rgba(34, 211, 238, .08),
                    transparent 30%
                ),

                radial-gradient(
                    circle at 50% 100%,
                    rgba(139, 92, 246, .06),
                    transparent 35%
                ),

                #050b12;

        }


        .background::before {

            content: '';

            position: absolute;

            inset: 0;

            background-image:

                linear-gradient(
                    rgba(255,255,255,.025) 1px,
                    transparent 1px
                ),

                linear-gradient(
                    90deg,
                    rgba(255,255,255,.025) 1px,
                    transparent 1px
                );

            background-size: 55px 55px;

            mask-image: linear-gradient(
                to bottom,
                black,
                transparent 90%
            );

        }


        .orb {

            position: absolute;

            border-radius: 50%;

            filter: blur(90px);

            opacity: .45;

            animation: floatOrb 12s ease-in-out infinite;

        }


        .orb-1 {

            width: 400px;

            height: 400px;

            background: rgba(245, 181, 44, .10);

            left: -150px;

            top: -120px;

        }


        .orb-2 {

            width: 350px;

            height: 350px;

            background: rgba(34, 211, 238, .08);

            right: -100px;

            top: 40%;

            animation-delay: -4s;

        }


        .orb-3 {

            width: 300px;

            height: 300px;

            background: rgba(139, 92, 246, .08);

            left: 35%;

            bottom: -150px;

            animation-delay: -7s;

        }


        @keyframes floatOrb {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(40px, -30px) scale(1.1);
            }

        }


        /* =====================================================
           LAYOUT
        ===================================================== */

        .app {

            min-height: 100vh;

            display: flex;

        }


        /* =====================================================
           SIDEBAR
        ===================================================== */

        .sidebar {

            width: 255px;

            min-height: 100vh;

            position: fixed;

            left: 0;

            top: 0;

            bottom: 0;

            background: var(--sidebar);

            backdrop-filter: blur(20px);

            -webkit-backdrop-filter: blur(20px);

            border-right: 1px solid var(--border);

            padding: 24px 16px;

            z-index: 100;

            display: flex;

            flex-direction: column;

        }


        /* BRAND */

        .brand {

            display: flex;

            align-items: center;

            gap: 12px;

            padding: 6px 10px 28px;

        }


        .brand-icon {

            width: 45px;

            height: 45px;

            flex-shrink: 0;

            border-radius: 14px;

            display: flex;

            align-items: center;

            justify-content: center;

            color: #111827;

            background:
                linear-gradient(
                    145deg,
                    var(--gold-light),
                    var(--gold-dark)
                );

            box-shadow:
                0 0 30px rgba(245, 181, 44, .25);

        }


        .brand-icon svg {
            width: 23px;
            height: 23px;
        }


        .brand-text strong {

            display: block;

            font-family: 'Space Grotesk', sans-serif;

            font-size: 16px;

            font-weight: 700;

        }


        .brand-text span {

            display: block;

            color: #657489;

            font-size: 10px;

            margin-top: 3px;

            letter-spacing: .8px;

        }


        /* NAV */

        .nav-title {

            font-size: 10px;

            font-weight: 700;

            color: #56657a;

            letter-spacing: 1.4px;

            padding: 0 12px;

            margin-bottom: 10px;

            margin-top: 8px;

        }


        .nav {

            display: flex;

            flex-direction: column;

            gap: 5px;

        }


        .nav-link {

            position: relative;

            display: flex;

            align-items: center;

            gap: 13px;

            padding: 12px 13px;

            border-radius: 11px;

            color: #8290a3;

            text-decoration: none;

            font-size: 13px;

            font-weight: 600;

            transition: .25s ease;

        }


        .nav-link svg {

            width: 18px;

            height: 18px;

        }


        .nav-link:hover {

            color: white;

            background: rgba(255,255,255,.05);

            transform: translateX(3px);

        }


        .nav-link.active {

            color: var(--gold-light);

            background:

                linear-gradient(
                    90deg,
                    rgba(245,181,44,.14),
                    rgba(245,181,44,.04)
                );

        }


        .nav-link.active::before {

            content: '';

            position: absolute;

            left: 0;

            top: 8px;

            bottom: 8px;

            width: 3px;

            border-radius: 10px;

            background: var(--gold);

            box-shadow: 0 0 12px var(--gold);

        }


        .sidebar-bottom {

            margin-top: auto;

        }


        .sidebar-user {

            padding: 14px;

            border: 1px solid var(--border);

            border-radius: 14px;

            background: rgba(255,255,255,.025);

            margin-bottom: 10px;

        }


        .sidebar-user-top {

            display: flex;

            align-items: center;

            gap: 10px;

        }


        .avatar {

            width: 38px;

            height: 38px;

            border-radius: 11px;

            background: linear-gradient(
                135deg,
                #fbbf24,
                #d97706
            );

            display: flex;

            align-items: center;

            justify-content: center;

            color: #111827;

            font-weight: 800;

        }


        .user-info {

            min-width: 0;

        }


        .user-info strong {

            display: block;

            font-size: 12px;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;

        }


        .user-info span {

            display: block;

            color: #68778b;

            font-size: 10px;

            margin-top: 2px;

        }


        /* =====================================================
           MAIN
        ===================================================== */

        .main {

            margin-left: 255px;

            width: calc(100% - 255px);

            min-height: 100vh;

            padding: 25px 30px 40px;

        }


        /* =====================================================
           TOPBAR
        ===================================================== */

        .topbar {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 30px;

        }


        .page-title h1 {

            font-family: 'Space Grotesk', sans-serif;

            font-size: 26px;

            letter-spacing: -.5px;

        }


        .page-title p {

            color: var(--muted);

            font-size: 12px;

            margin-top: 5px;

        }


        .top-actions {

            display: flex;

            align-items: center;

            gap: 12px;

        }


        .icon-button {

            width: 42px;

            height: 42px;

            border-radius: 12px;

            border: 1px solid var(--border);

            background: rgba(255,255,255,.035);

            color: #8d9aad;

            display: flex;

            align-items: center;

            justify-content: center;

            cursor: pointer;

            transition: .25s;

        }


        .icon-button:hover {

            color: white;

            border-color: rgba(245,181,44,.35);

            transform: translateY(-2px);

        }


        .icon-button svg {

            width: 18px;

        }


        /* =====================================================
           HERO
        ===================================================== */

        .hero {

            position: relative;

            overflow: hidden;

            min-height: 190px;

            border: 1px solid rgba(245,181,44,.16);

            border-radius: 20px;

            padding: 32px;

            margin-bottom: 22px;

            background:

                linear-gradient(
                    110deg,
                    rgba(245,181,44,.12),
                    rgba(15,27,42,.72) 45%,
                    rgba(8,16,27,.85)
                );

            box-shadow:
                0 20px 70px rgba(0,0,0,.25);

        }


        .hero::before {

            content: '';

            position: absolute;

            width: 260px;

            height: 260px;

            border-radius: 50%;

            right: 40px;

            top: -130px;

            background: rgba(245,181,44,.09);

            filter: blur(25px);

        }


        .hero-content {

            position: relative;

            z-index: 2;

            max-width: 620px;

        }


        .hero-label {

            display: inline-flex;

            align-items: center;

            gap: 7px;

            color: var(--gold-light);

            font-size: 10px;

            font-weight: 700;

            letter-spacing: 1.4px;

            margin-bottom: 13px;

        }


        .live-dot {

            width: 7px;

            height: 7px;

            border-radius: 50%;

            background: #22c55e;

            box-shadow: 0 0 12px #22c55e;

            animation: pulse 2s infinite;

        }


        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: .5;
                transform: scale(.7);
            }

        }


        .hero h2 {

            font-family: 'Space Grotesk', sans-serif;

            font-size: 30px;

            margin-bottom: 8px;

        }


        .hero p {

            color: #91a0b4;

            font-size: 13px;

            line-height: 1.7;

        }


        /* floating cube */

        .hero-cube {

            position: absolute;

            right: 100px;

            top: 45px;

            width: 95px;

            height: 95px;

            border: 1px solid rgba(245,181,44,.3);

            border-radius: 22px;

            transform: rotate(20deg);

            display: flex;

            align-items: center;

            justify-content: center;

            color: var(--gold);

            background: rgba(245,181,44,.035);

            box-shadow:
                inset 0 0 30px rgba(245,181,44,.04),
                0 0 40px rgba(245,181,44,.05);

            animation: cubeFloat 5s ease-in-out infinite;

        }


        .hero-cube svg {

            width: 42px;

            height: 42px;

            transform: rotate(-20deg);

        }


        @keyframes cubeFloat {

            0%,
            100% {
                transform: rotate(20deg) translateY(0);
            }

            50% {
                transform: rotate(25deg) translateY(-10px);
            }

        }


        /* =====================================================
           STATISTICS
        ===================================================== */

        .stats {

            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 16px;

            margin-bottom: 22px;

        }


        .stat-card {

            position: relative;

            overflow: hidden;

            padding: 20px;

            min-height: 135px;

            border-radius: 16px;

            border: 1px solid var(--border);

            background: var(--card);

            backdrop-filter: blur(15px);

            transition: .3s ease;

        }


        .stat-card:hover {

            transform: translateY(-5px);

            border-color: rgba(245,181,44,.22);

            box-shadow:
                0 15px 40px rgba(0,0,0,.2);

        }


        .stat-card::after {

            content: '';

            position: absolute;

            width: 100px;

            height: 100px;

            right: -50px;

            bottom: -50px;

            border-radius: 50%;

            background: rgba(245,181,44,.05);

        }


        .stat-top {

            display: flex;

            justify-content: space-between;

            align-items: flex-start;

        }


        .stat-icon {

            width: 42px;

            height: 42px;

            border-radius: 12px;

            display: flex;

            align-items: center;

            justify-content: center;

        }


        .stat-icon svg {
            width: 20px;
        }


        .icon-gold {

            color: var(--gold-light);

            background: rgba(245,181,44,.10);

        }


        .icon-blue {

            color: #67e8f9;

            background: rgba(34,211,238,.10);

        }


        .icon-green {

            color: #86efac;

            background: rgba(34,197,94,.10);

        }


        .icon-purple {

            color: #c4b5fd;

            background: rgba(139,92,246,.10);

        }


        .stat-number {

            font-family: 'Space Grotesk', sans-serif;

            font-size: 28px;

            font-weight: 700;

            margin-top: 14px;

        }


        .stat-label {

            color: var(--muted);

            font-size: 11px;

            margin-top: 3px;

        }


        .stat-change {

            color: #4ade80;

            font-size: 10px;

            font-weight: 700;

        }


        /* =====================================================
           CONTENT GRID
        ===================================================== */

        .content-grid {

            display: grid;

            grid-template-columns:
                minmax(0, 1.7fr)
                minmax(300px, .9fr);

            gap: 20px;

        }


        .panel {

            border: 1px solid var(--border);

            border-radius: 18px;

            background: var(--card);

            backdrop-filter: blur(15px);

            overflow: hidden;

        }


        .panel-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 20px 22px;

            border-bottom: 1px solid var(--border);

        }


        .panel-header h3 {

            font-family: 'Space Grotesk', sans-serif;

            font-size: 15px;

        }


        .panel-header span {

            color: #68778b;

            font-size: 10px;

        }


        /* =====================================================
           CHART
        ===================================================== */

        .chart {

            height: 290px;

            padding: 25px;

            display: flex;

            align-items: flex-end;

            gap: 20px;

        }


        .chart-y {

            height: 220px;

            display: flex;

            flex-direction: column;

            justify-content: space-between;

            color: #526174;

            font-size: 9px;

        }


        .chart-area {

            flex: 1;

            height: 220px;

            position: relative;

            border-bottom: 1px solid #263548;

            border-left: 1px solid #263548;

            display: flex;

            align-items: flex-end;

            justify-content: space-around;

            background-image:

                linear-gradient(
                    rgba(255,255,255,.035) 1px,
                    transparent 1px
                );

            background-size: 100% 55px;

        }


        .bar {

            width: 8%;

            max-width: 42px;

            border-radius: 8px 8px 2px 2px;

            background:

                linear-gradient(
                    to top,
                    var(--gold-dark),
                    var(--gold-light)
                );

            box-shadow:
                0 0 20px rgba(245,181,44,.12);

            transform-origin: bottom;

            animation: barGrow 1s ease backwards;

            transition: .25s;

        }


        .bar:hover {

            filter: brightness(1.25);

            transform: scaleX(1.08);

        }


        @keyframes barGrow {

            from {
                transform: scaleY(0);
            }

            to {
                transform: scaleY(1);
            }

        }


        .chart-labels {

            display: flex;

            justify-content: space-around;

            margin-top: 9px;

            color: #58677b;

            font-size: 9px;

        }


        /* =====================================================
           ACTIVITY
        ===================================================== */

        .activity-list {

            padding: 8px 20px 15px;

        }


        .activity {

            display: flex;

            align-items: center;

            gap: 12px;

            padding: 14px 0;

            border-bottom: 1px solid rgba(148,163,184,.07);

        }


        .activity:last-child {
            border-bottom: none;
        }


        .activity-icon {

            width: 36px;

            height: 36px;

            flex-shrink: 0;

            border-radius: 10px;

            display: flex;

            align-items: center;

            justify-content: center;

        }


        .activity-icon svg {
            width: 16px;
        }


        .activity-info {

            min-width: 0;

            flex: 1;

        }


        .activity-info strong {

            display: block;

            font-size: 11px;

            font-weight: 600;

        }


        .activity-info span {

            display: block;

            color: #627186;

            font-size: 9px;

            margin-top: 4px;

        }


        .activity-time {

            color: #59687b;

            font-size: 9px;

        }


        /* =====================================================
           QUICK ACTION
        ===================================================== */

        .quick-actions {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 12px;

            margin-top: 20px;

        }


        .quick {

            display: flex;

            align-items: center;

            gap: 11px;

            padding: 15px;

            text-decoration: none;

            color: white;

            border-radius: 13px;

            border: 1px solid var(--border);

            background: rgba(255,255,255,.025);

            transition: .25s;

        }


        .quick:hover {

            transform: translateY(-3px);

            border-color: rgba(245,181,44,.25);

            background: rgba(245,181,44,.05);

        }


        .quick-icon {

            width: 38px;

            height: 38px;

            border-radius: 10px;

            display: flex;

            align-items: center;

            justify-content: center;

            color: var(--gold-light);

            background: rgba(245,181,44,.08);

        }


        .quick-icon svg {
            width: 18px;
        }


        .quick span {

            font-size: 10px;

            font-weight: 600;

        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 1100px) {

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .hero-cube {
                right: 50px;
            }

        }


        @media (max-width: 800px) {

            .sidebar {

                width: 70px;

                padding: 15px 10px;

            }


            .brand {

                justify-content: center;

                padding-left: 0;

                padding-right: 0;

            }


            .brand-text,
            .nav-title,
            .nav-link span,
            .sidebar-user {

                display: none;

            }


            .nav-link {

                justify-content: center;

                padding: 13px;

            }


            .nav-link.active::before {
                display: none;
            }


            .main {

                margin-left: 70px;

                width: calc(100% - 70px);

                padding: 20px 15px;

            }


            .hero {
                padding: 25px;
            }


            .hero-cube {
                opacity: .3;
                right: -10px;
            }


            .quick-actions {
                grid-template-columns: 1fr;
            }

        }


        @media (max-width: 550px) {

            .stats {
                grid-template-columns: 1fr;
            }


            .topbar {

                align-items: flex-start;

            }


            .page-title h1 {
                font-size: 22px;
            }


            .hero h2 {
                font-size: 24px;
            }


            .chart {
                padding: 15px;
            }

        }

    </style>

</head>


<body>


<!-- =========================================================
     BACKGROUND
========================================================= -->

<div class="background">

    <div class="orb orb-1"></div>

    <div class="orb orb-2"></div>

    <div class="orb orb-3"></div>

</div>


<div class="app">


<!-- =========================================================
     SIDEBAR
========================================================= -->

<aside class="sidebar">


    <!-- BRAND -->

    <div class="brand">

        <div class="brand-icon">

            <i data-lucide="boxes"></i>

        </div>

        <div class="brand-text">

            <strong>Peminjaman Lab</strong>

            <span>RPL & TKJ · SMK</span>

        </div>

    </div>


    <!-- MAIN MENU -->

    <div class="nav-title">
        MENU UTAMA
    </div>


    <nav class="nav">

        <a
            href="dashboard.php"
            class="nav-link active"
        >

            <i data-lucide="layout-dashboard"></i>

            <span>Dashboard</span>

        </a>


        <a
            href="barang.php"
            class="nav-link"
        >

            <i data-lucide="package"></i>

            <span>Data Barang</span>

        </a>


        <a
            href="peminjaman.php"
            class="nav-link"
        >

            <i data-lucide="clipboard-list"></i>

            <span>Peminjaman</span>

        </a>


        <a
            href="pengembalian.php"
            class="nav-link"
        >

            <i data-lucide="package-check"></i>

            <span>Pengembalian</span>

        </a>


        <a
            href="laporan.php"
            class="nav-link"
        >

            <i data-lucide="chart-no-axes-combined"></i>

            <span>Laporan</span>

        </a>

    </nav>


    <div class="nav-title">
        SISTEM
    </div>


    <nav class="nav">

        <a
            href="#"
            class="nav-link"
        >

            <i data-lucide="settings"></i>

            <span>Pengaturan</span>

        </a>

    </nav>


    <!-- BOTTOM -->

    <div class="sidebar-bottom">

        <div class="sidebar-user">

            <div class="sidebar-user-top">

                <div class="avatar">

                    <?= strtoupper(substr($nama, 0, 1)) ?>

                </div>


                <div class="user-info">

                    <strong>
                        <?= htmlspecialchars($nama) ?>
                    </strong>

                    <span>
                        Administrator
                    </span>

                </div>

            </div>

        </div>


        <a
            href="logout.php"
            class="nav-link"
        >

            <i data-lucide="log-out"></i>

            <span>Keluar</span>

        </a>

    </div>

</aside>


<!-- =========================================================
     MAIN
========================================================= -->

<main class="main">


    <!-- TOPBAR -->

    <header class="topbar">

        <div class="page-title">

            <h1>
                Dashboard
            </h1>

            <p>
                Ringkasan sistem peminjaman laboratorium
            </p>

        </div>


        <div class="top-actions">

            <button
                class="icon-button"
                title="Notifikasi"
            >

                <i data-lucide="bell"></i>

            </button>


            <button
                class="icon-button"
                title="Profil"
            >

                <i data-lucide="user-round"></i>

            </button>

        </div>

    </header>


    <!-- =====================================================
         HERO
    ====================================================== -->

    <section class="hero">

        <div class="hero-content">

            <div class="hero-label">

                <span class="live-dot"></span>

                SISTEM AKTIF

            </div>


            <h2>
                Selamat datang,
                <?= htmlspecialchars($nama) ?> 👋
            </h2>


            <p>
                Pantau aktivitas peminjaman barang laboratorium
                dengan mudah melalui dashboard administrator.
            </p>

        </div>


        <div class="hero-cube">

            <i data-lucide="box"></i>

        </div>

    </section>


    <!-- =====================================================
         STATISTICS
    ====================================================== -->

    <section class="stats">


        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon icon-gold">

                    <i data-lucide="package"></i>

                </div>

                <span class="stat-change">
                    +8%
                </span>

            </div>


            <div class="stat-number">
                24
            </div>

            <div class="stat-label">
                Total Barang
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon icon-blue">

                    <i data-lucide="clipboard-list"></i>

                </div>

                <span class="stat-change">
                    +12%
                </span>

            </div>


            <div class="stat-number">
                12
            </div>

            <div class="stat-label">
                Sedang Dipinjam
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon icon-green">

                    <i data-lucide="package-check"></i>

                </div>

                <span class="stat-change">
                    +5%
                </span>

            </div>


            <div class="stat-number">
                8
            </div>

            <div class="stat-label">
                Sudah Dikembalikan
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-top">

                <div class="stat-icon icon-purple">

                    <i data-lucide="users"></i>

                </div>

                <span class="stat-change">
                    +3%
                </span>

            </div>


            <div class="stat-number">
                18
            </div>

            <div class="stat-label">
                Pengguna Aktif
            </div>

        </div>


    </section>


    <!-- =====================================================
         CONTENT
    ====================================================== -->

    <section class="content-grid">


        <!-- CHART -->

        <div class="panel">

            <div class="panel-header">

                <div>

                    <h3>
                        Statistik Peminjaman
                    </h3>

                    <span>
                        Aktivitas 7 hari terakhir
                    </span>

                </div>

                <span>
                    2026
                </span>

            </div>


            <div class="chart">

                <div class="chart-y">

                    <span>20</span>

                    <span>15</span>

                    <span>10</span>

                    <span>5</span>

                    <span>0</span>

                </div>


                <div style="flex:1;">

                    <div class="chart-area">

                        <div
                            class="bar"
                            style="height:45%; animation-delay:.05s;"
                        ></div>

                        <div
                            class="bar"
                            style="height:70%; animation-delay:.1s;"
                        ></div>

                        <div
                            class="bar"
                            style="height:55%; animation-delay:.15s;"
                        ></div>

                        <div
                            class="bar"
                            style="height:85%; animation-delay:.2s;"
                        ></div>

                        <div
                            class="bar"
                            style="height:65%; animation-delay:.25s;"
                        ></div>

                        <div
                            class="bar"
                            style="height:95%; animation-delay:.3s;"
                        ></div>

                        <div
                            class="bar"
                            style="height:75%; animation-delay:.35s;"
                        ></div>

                    </div>


                    <div class="chart-labels">

                        <span>Sen</span>

                        <span>Sel</span>

                        <span>Rab</span>

                        <span>Kam</span>

                        <span>Jum</span>

                        <span>Sab</span>

                        <span>Min</span>

                    </div>

                </div>

            </div>

        </div>


        <!-- ACTIVITY -->

        <div class="panel">

            <div class="panel-header">

                <div>

                    <h3>
                        Aktivitas Terbaru
                    </h3>

                    <span>
                        Aktivitas sistem
                    </span>

                </div>

                <i
                    data-lucide="activity"
                    style="width:17px;color:#f5b52c;"
                ></i>

            </div>


            <div class="activity-list">


                <div class="activity">

                    <div
                        class="activity-icon icon-blue"
                    >

                        <i data-lucide="clipboard-plus"></i>

                    </div>


                    <div class="activity-info">

                        <strong>
                            Peminjaman baru
                        </strong>

                        <span>
                            Laptop Lenovo dipinjam
                        </span>

                    </div>


                    <div class="activity-time">
                        5m
                    </div>

                </div>


                <div class="activity">

                    <div
                        class="activity-icon icon-green"
                    >

                        <i data-lucide="package-check"></i>

                    </div>


                    <div class="activity-info">

                        <strong>
                            Barang dikembalikan
                        </strong>

                        <span>
                            Proyektor Epson
                        </span>

                    </div>


                    <div class="activity-time">
                        20m
                    </div>

                </div>


                <div class="activity">

                    <div
                        class="activity-icon icon-gold"
                    >

                        <i data-lucide="package-plus"></i>

                    </div>


                    <div class="activity-info">

                        <strong>
                            Barang ditambahkan
                        </strong>

                        <span>
                            2 unit laptop baru
                        </span>

                    </div>


                    <div class="activity-time">
                        1j
                    </div>

                </div>


                <div class="activity">

                    <div
                        class="activity-icon icon-purple"
                    >

                        <i data-lucide="user-plus"></i>

                    </div>


                    <div class="activity-info">

                        <strong>
                            Pengguna baru
                        </strong>

                        <span>
                            Akun siswa terdaftar
                        </span>

                    </div>


                    <div class="activity-time">
                        2j
                    </div>

                </div>


            </div>

        </div>

    </section>


    <!-- =====================================================
         QUICK ACTION
    ====================================================== -->

    <section class="quick-actions">


        <a
            href="barang.php"
            class="quick"
        >

            <div class="quick-icon">

                <i data-lucide="package-plus"></i>

            </div>

            <span>
                Tambah Barang
            </span>

        </a>


        <a
            href="peminjaman.php"
            class="quick"
        >

            <div class="quick-icon">

                <i data-lucide="clipboard-plus"></i>

            </div>

            <span>
                Peminjaman Baru
            </span>

        </a>


        <a
            href="laporan.php"
            class="quick"
        >

            <div class="quick-icon">

                <i data-lucide="file-chart-column"></i>

            </div>

            <span>
                Lihat Laporan
            </span>

        </a>


    </section>


</main>

</div>


<script>

lucide.createIcons();

</script>


</body>

</html>