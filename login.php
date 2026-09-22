<?php
session_start();

/*
|--------------------------------------------------------------------------
| AKUN LOGIN
|--------------------------------------------------------------------------
| Karena aplikasi tidak menggunakan database,
| akun disimpan langsung di dalam kode.
*/

$users = [
    'adminlab' => [
        'nama'     => 'Admin Lab',
        'password' => 'admin123',
        'role'     => 'admin'
    ],

    'petugaslab' => [
        'nama'     => 'Petugas Lab',
        'password' => 'petugas123',
        'role'     => 'petugas'
    ],

    'siswa' => [
        'nama'     => 'Siswa',
        'password' => 'siswa123',
        'role'     => 'siswa'
    ]
];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? '';

    if ($username === '' || $password === '' || $role === '') {

        $error = 'Semua data login wajib diisi.';

    } elseif (
        isset($users[$username]) &&
        $users[$username]['password'] === $password &&
        $users[$username]['role'] === $role
    ) {

        /*
        |--------------------------------------------------------------------------
        | LOGIN BERHASIL
        |--------------------------------------------------------------------------
        */

        session_regenerate_id(true);

        $_SESSION['login']    = true;
        $_SESSION['username'] = $username;
        $_SESSION['nama']     = $users[$username]['nama'];
        $_SESSION['role']     = $users[$username]['role'];

        header('Location: ' . ($users[$username]['role'] === 'siswa' ? 'peminjaman.php' : 'dashboard.php'));
        exit;

    } else {

        $error = 'Username, password, atau akses tidak sesuai.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login — Peminjaman Lab</title>

<link rel="preconnect" href="https://fonts.googleapis.com">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

<style>

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    background:
        radial-gradient(circle at 10% 20%, rgba(245,158,11,.14), transparent 30%),
        radial-gradient(circle at 90% 80%, rgba(20,184,166,.12), transparent 30%),
        #050b13;
    color: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

/* GRID */

body::before {
    content: "";
    position: fixed;
    inset: 0;

    background-image:
        linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);

    background-size: 42px 42px;
    pointer-events: none;
}

/* ORB */

.orb {
    position: fixed;
    width: 350px;
    height: 350px;
    border-radius: 50%;
    filter: blur(100px);
    opacity: .18;
    animation: float 8s ease-in-out infinite;
}

.orb-1 {
    background: #f59e0b;
    top: -120px;
    left: -100px;
}

.orb-2 {
    background: #06b6d4;
    right: -120px;
    bottom: -100px;
    animation-delay: 2s;
}

.orb-3 {
    background: #8b5cf6;
    left: 40%;
    bottom: -180px;
    animation-delay: 4s;
}

@keyframes float {
    0%,100% {
        transform: translateY(0) scale(1);
    }

    50% {
        transform: translateY(-30px) scale(1.08);
    }
}

/* LOGIN CARD */

.login-card {
    position: relative;
    z-index: 5;

    width: min(440px, calc(100% - 32px));

    background: rgba(13, 24, 37, .88);
    border: 1px solid rgba(148,163,184,.18);

    border-radius: 26px;

    padding: 38px;

    box-shadow:
        0 30px 80px rgba(0,0,0,.45),
        inset 0 1px rgba(255,255,255,.04);

    backdrop-filter: blur(20px);

    animation: cardIn .7s ease;
}

@keyframes cardIn {

    from {
        opacity: 0;
        transform: translateY(30px) scale(.97);
    }

    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* LOGO */

.logo {
    width: 66px;
    height: 66px;

    margin: 0 auto 18px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 18px;

    background: linear-gradient(
        135deg,
        #ffc857,
        #e89b05
    );

    color: #111827;

    font-size: 23px;
    font-weight: 800;

    box-shadow:
        0 12px 30px rgba(245,158,11,.25);
}

.header {
    text-align: center;
    margin-bottom: 28px;
}

.header h1 {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 28px;
    margin-bottom: 7px;
}

.header p {
    color: #718096;
    font-size: 13px;
}

/* ERROR */

.error {
    padding: 13px 15px;
    margin-bottom: 20px;

    border-radius: 10px;

    background: rgba(239,68,68,.12);
    border: 1px solid rgba(239,68,68,.28);

    color: #fca5a5;

    font-size: 13px;
}

/* LABEL */

.field {
    margin-bottom: 17px;
}

.field label {
    display: block;

    margin-bottom: 8px;

    color: #94a3b8;

    font-size: 12px;
    font-weight: 700;
}

/* INPUT */

.input {
    width: 100%;

    padding: 14px 15px;

    border-radius: 11px;

    border: 1px solid #26364a;

    background: #08111d;

    color: white;

    outline: none;

    font-size: 14px;

    transition: .25s;
}

.input:focus {
    border-color: #f5b632;

    box-shadow:
        0 0 0 3px rgba(245,158,11,.09);
}

/* ROLE */

.roles {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 9px;
}

.role-option input {
    display: none;
}

.role-option label {

    min-height: 92px;

    padding: 12px 7px;

    border-radius: 13px;

    border: 1px solid #26364a;

    background: #08111d;

    display: flex;
    flex-direction: column;

    align-items: center;
    justify-content: center;

    gap: 7px;

    cursor: pointer;

    color: #8190a5;

    font-size: 11px;
    font-weight: 700;

    text-align: center;

    transition: .25s;
}

.role-icon {
    font-size: 25px;
}

.role-option input:checked + label {
    border-color: #f5b632;

    background:
        linear-gradient(
            145deg,
            rgba(245,158,11,.16),
            rgba(245,158,11,.05)
        );

    color: #ffc857;

    box-shadow:
        0 0 20px rgba(245,158,11,.08);
}

.role-option label:hover {
    transform: translateY(-2px);
    border-color: rgba(245,158,11,.5);
}

/* BUTTON */

button[type="submit"] {

    width: 100%;

    margin-top: 7px;

    border: none;

    padding: 15px;

    border-radius: 12px;

    background:
        linear-gradient(
            135deg,
            #ffc857,
            #e99c08
        );

    color: #111827;

    font-size: 14px;
    font-weight: 800;

    cursor: pointer;

    transition: .25s;

    box-shadow:
        0 12px 25px rgba(245,158,11,.18);
}

button[type="submit"]:hover {
    transform: translateY(-2px);

    box-shadow:
        0 16px 30px rgba(245,158,11,.25);
}

button[type="submit"]:active {
    transform: translateY(0);
}

/* FOOTER */

.footer {
    text-align: center;

    margin-top: 23px;

    color: #64748b;

    font-size: 12px;
}

.footer a {
    color: #fbbf24;
    text-decoration: none;
    font-weight: 700;
}

.footer a:hover {
    text-decoration: underline;
}

.copyright {
    text-align: center;

    margin-top: 25px;
    padding-top: 18px;

    border-top: 1px solid rgba(148,163,184,.1);

    color: #475569;

    font-size: 10px;
}

@media(max-width: 500px) {

    .login-card {
        padding: 28px 22px;
    }

    .roles {
        grid-template-columns: 1fr;
    }

    .role-option label {
        min-height: 65px;
        flex-direction: row;
        justify-content: flex-start;
        padding-left: 20px;
    }

}

</style>

</head>

<body>

<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>

<div class="login-card">

    <div class="logo">
        LB
    </div>

    <div class="header">

        <h1>Peminjaman Lab</h1>

        <p>
            Sistem Informasi Laboratorium
        </p>

    </div>

    <?php if ($error): ?>

        <div class="error">
            ⚠️ <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <form method="POST">

        <div class="field">

            <label>
                AKSES LOGIN
            </label>

            <div class="roles">

                <div class="role-option">

                    <input
                        type="radio"
                        id="admin"
                        name="role"
                        value="admin"
                        required
                    >

                    <label for="admin">

                        <span class="role-icon">👑</span>

                        <span>Admin Lab</span>

                    </label>

                </div>


                <div class="role-option">

                    <input
                        type="radio"
                        id="petugas"
                        name="role"
                        value="petugas"
                    >

                    <label for="petugas">

                        <span class="role-icon">🛠️</span>

                        <span>Petugas Lab</span>

                    </label>

                </div>


                <div class="role-option">

                    <input
                        type="radio"
                        id="siswa"
                        name="role"
                        value="siswa"
                    >

                    <label for="siswa">

                        <span class="role-icon">🎓</span>

                        <span>Siswa</span>

                    </label>

                </div>

            </div>

        </div>


        <div class="field">

            <label for="username">
                USERNAME
            </label>

            <input
                class="input"
                type="text"
                id="username"
                name="username"
                placeholder="Masukkan username"
                autocomplete="username"
                required
            >

        </div>


        <div class="field">

            <label for="password">
                PASSWORD
            </label>

            <input
                class="input"
                type="password"
                id="password"
                name="password"
                placeholder="Masukkan password"
                autocomplete="current-password"
                required
            >

        </div>


        <button type="submit">
            Masuk ke Sistem →
        </button>

    </form>


    <div class="footer">

        Belum mempunyai akun?

        <a href="register.php">
            Daftar sekarang
        </a>

    </div>


    <div class="copyright">

        © <?= date('Y') ?> Peminjaman Lab ·
        Sistem Informasi Laboratorium

    </div>

</div>

</body>

</html>