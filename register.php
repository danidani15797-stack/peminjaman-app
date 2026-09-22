<?php
session_start();

/*
|--------------------------------------------------------------------------
| REGISTER TANPA DATABASE
|--------------------------------------------------------------------------
*/

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama     = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Validasi
    if ($nama === '' || $username === '' || $password === '' || $confirm === '') {

        $error = 'Semua field wajib diisi.';

    } elseif (strlen($password) < 6) {

        $error = 'Password minimal 6 karakter.';

    } elseif ($password !== $confirm) {

        $error = 'Konfirmasi password tidak sama.';

    } else {

        /*
        |--------------------------------------------------------------------------
        | Simpan akun ke SESSION
        |--------------------------------------------------------------------------
        */

        $_SESSION['registered_user'] = [
            'nama' => $nama,
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $success = 'Akun berhasil dibuat. Silakan masuk ke halaman login.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Daftar Akun — Peminjaman Lab</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="assets/style.css">

</head>

<body class="auth-page">

<div class="auth-background">

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="grid-overlay"></div>

    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>
    <div class="floating-shape shape-3"></div>

</div>


<main class="auth-wrapper">

<section class="register-card">

    <!-- BRAND -->

    <div class="auth-brand">

        <div class="auth-logo">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path d="M4 6.5 12 3l8 3.5v11L12 21l-8-3.5v-11Z"/>
                <path d="M4 6.5 12 10l8-3.5"/>
                <path d="M12 10v11"/>
            </svg>

        </div>

        <div>

            <div class="auth-brand-name">
                Peminjaman Lab
            </div>

            <div class="auth-brand-sub">
                RPL & TKJ · SMK
            </div>

        </div>

    </div>


    <!-- HEADER -->

    <div class="auth-header">

        <div class="auth-eyebrow">

            <span class="status-dot"></span>

            SISTEM INFORMASI LABORATORIUM

        </div>

        <h1>
            Buat akun baru
        </h1>

        <p>
            Daftarkan akun administrator untuk mengelola
            peminjaman barang laboratorium.
        </p>

    </div>


    <!-- ERROR -->

    <?php if ($error): ?>

        <div class="auth-alert auth-alert-error">

            <span>⚠</span>

            <span>
                <?= htmlspecialchars($error) ?>
            </span>

        </div>

    <?php endif; ?>


    <!-- SUCCESS -->

    <?php if ($success): ?>

        <div class="auth-alert auth-alert-success">

            <span>✓</span>

            <span>
                <?= htmlspecialchars($success) ?>
            </span>

        </div>

    <?php endif; ?>


    <!-- FORM -->

    <form method="POST" class="auth-form" autocomplete="off">

        <!-- NAMA -->

        <div class="auth-field">

            <label for="nama">
                Nama Lengkap
            </label>

            <div class="input-wrap">

                <span class="input-icon">
                    👤
                </span>

                <input
                    type="text"
                    id="nama"
                    name="nama"
                    placeholder="Masukkan nama lengkap"
                    required
                >

            </div>

        </div>


        <!-- USERNAME -->

        <div class="auth-field">

            <label for="username">
                Username
            </label>

            <div class="input-wrap">

                <span class="input-icon">
                    👤
                </span>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Masukkan username"
                    required
                >

            </div>

        </div>


        <!-- PASSWORD -->

        <div class="auth-field">

            <label for="password">
                Password
            </label>

            <div class="input-wrap">

                <span class="input-icon">
                    🔒
                </span>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Minimal 6 karakter"
                    required
                >

                <button
                    type="button"
                    class="password-toggle"
                    onclick="togglePassword('password', this)"
                >
                    👁
                </button>

            </div>

        </div>


        <!-- CONFIRM -->

        <div class="auth-field">

            <label for="confirm_password">
                Konfirmasi Password
            </label>

            <div class="input-wrap">

                <span class="input-icon">
                    🔒
                </span>

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    placeholder="Ulangi password"
                    required
                >

                <button
                    type="button"
                    class="password-toggle"
                    onclick="togglePassword('confirm_password', this)"
                >
                    👁
                </button>

            </div>

        </div>


        <!-- BUTTON -->

        <button
            type="submit"
            class="auth-submit"
        >

            <span>
                Buat Akun
            </span>

            <span>
                →
            </span>

        </button>

    </form>


    <!-- FOOTER -->

    <div class="auth-footer">

        <span>
            Sudah mempunyai akun?
        </span>

        <a href="login.php">
            Masuk sekarang
        </a>

    </div>


    <div class="auth-copyright">

        © <?= date('Y') ?>
        Peminjaman Lab · Sistem Informasi Laboratorium

    </div>

</section>

</main>


<script>

function togglePassword(id, button) {

    const input = document.getElementById(id);

    if (input.type === 'password') {

        input.type = 'text';
        button.innerHTML = '🙈';

    } else {

        input.type = 'password';
        button.innerHTML = '👁';

    }

}

</script>

</body>
</html>