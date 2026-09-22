# Sistem Peminjaman Barang Laboratorium

Aplikasi web PHP untuk studi kasus jobsheet "Perancangan ERD dan UI/UX —
Aplikasi Peminjaman Laptop / Barang Laboratorium". **Tidak memakai
database sama sekali** — seluruh data (barang, siswa, peminjaman,
pengembalian) di-hardcode sebagai array PHP di `includes/data.php` dan
disimpan selama sesi berjalan di `$_SESSION`, lengkap dengan relasi
antar-entitas seperti pada ERD.

## Relasi data (mengikuti ERD jobsheet)

```
SISWA (1) ───< PEMINJAMAN >─── (1) BARANG
                    │ 1
                    ▼ 1
              PENGEMBALIAN
```

- `peminjaman.id_siswa`  → foreign key ke `siswa.id`
- `peminjaman.id_barang` → foreign key ke `barang.id`
- `pengembalian.id_peminjaman` → foreign key ke `peminjaman.id` (1-ke-1)
- Menambah peminjaman otomatis mengurangi `stok` barang terkait; mencatat
  pengembalian otomatis menambah stok kembali dan menutup status
  peminjaman menjadi "Selesai".

## Menjalankan

Butuh PHP terpasang (PHP 8+ disarankan). Dari folder proyek ini:

```bash
php -S localhost:8000
```

Lalu buka `http://localhost:8000` di browser. Bisa juga ditaruh langsung
di folder `htdocs` XAMPP/Laragon tanpa konfigurasi tambahan — tidak perlu
membuat database apa pun.

## Login

- Username: `admin`
- Password: `admin123`

Kredensial ini di-hardcode di `login.php`.

## Struktur berkas

```
index.php            Pengalih ke login/dashboard
login.php             Halaman masuk
logout.php            Keluar sesi
dashboard.php          Ringkasan & statistik
barang.php             CRUD data barang
siswa.php               CRUD data siswa
peminjaman.php          Catat transaksi peminjaman (relasi ke siswa & barang)
pengembalian.php        Catat pengembalian (relasi ke peminjaman)
laporan.php              Rekap gabungan + cetak
includes/config.php      Bootstrap sesi & helper
includes/data.php        "Database" hardcoded + fungsi relasi
includes/header.php      Sidebar & topbar bersama
includes/footer.php      Penutup halaman
assets/style.css         Desain visual (tema gelap "lab console")
assets/script.js         Interaksi: sidebar mobile, jam, animasi angka
```

## Reset data

Karena data disimpan di sesi, keluar (logout) lalu masuk kembali tidak
mereset data — hanya menutup sesi PHP browser (atau hapus cookie
`PHPSESSID`) yang akan mengembalikan data ke kondisi awal di
`includes/data.php`.
