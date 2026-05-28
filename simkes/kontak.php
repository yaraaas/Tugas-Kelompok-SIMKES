<?php
include 'config/koneksi.php';

// Auto-create tabel pesan jika belum ada
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS pesan (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nama   VARCHAR(100) NOT NULL,
    no_hp  VARCHAR(20)  NOT NULL,
    pesan  TEXT         NOT NULL,
    waktu  DATETIME     NOT NULL
)");

// Auto-tambah kolom no_hp jika belum ada (untuk tabel lama)
$cek_kolom = mysqli_query($conn, "SHOW COLUMNS FROM pesan LIKE 'no_hp'");
if (mysqli_num_rows($cek_kolom) === 0) {
    mysqli_query($conn, "ALTER TABLE pesan ADD COLUMN no_hp VARCHAR(20) NOT NULL DEFAULT '' AFTER nama");
}

$sukses = '';
$error  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = trim($_POST['nama'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');

    if ($nama === '' || $no_hp === '' || $pesan === '') {
        $error = 'Semua kolom wajib diisi.';
    } else {
        $nama_esc  = mysqli_real_escape_string($conn, $nama);
        $no_hp_esc = mysqli_real_escape_string($conn, $no_hp);
        $pesan_esc = mysqli_real_escape_string($conn, $pesan);
        $query = "INSERT INTO pesan (nama, no_hp, pesan, waktu) VALUES ('$nama_esc', '$no_hp_esc', '$pesan_esc', NOW())";
        if (mysqli_query($conn, $query)) {
            $sukses = 'Pesan berhasil dikirim! Kami akan segera menghubungi Anda.';
        } else {
            $error = 'Gagal mengirim pesan: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kontak Kami - SIMKES Krapyak</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        :root {
            --cloud-blue: #EDF4FA;
            --powder-sky: #CFE3F1;
            --calm-ocean: #8FB6D8;
            --dusty-denim: #5F86A6;
            --midnight-blue: #243A5E;
            --white: #FFFFFF;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--white);
            color: var(--midnight-blue);
        }

        .custom-navbar {
            background-color: var(--white) !important;
            padding: 15px 0;
            border-bottom: 2px solid var(--cloud-blue);
        }

        .navbar-brand { color: var(--midnight-blue) !important; font-weight: 700; }

        .page-header {
            background: linear-gradient(135deg, var(--cloud-blue) 0%, var(--white) 100%);
            padding: 80px 0 50px;
            text-align: center;
        }

        .contact-card {
            border: none;
            border-radius: 20px;
            background: var(--white);
            box-shadow: 0 10px 30px rgba(36, 58, 94, 0.05);
            padding: 30px;
            height: 100%;
            transition: 0.3s;
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            background-color: var(--cloud-blue);
            color: var(--midnight-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .form-card {
            border: none;
            border-radius: 25px;
            background: var(--white);
            box-shadow: 0 15px 40px rgba(36, 58, 94, 0.08);
            padding: 40px;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 20px;
            border: 2px solid var(--cloud-blue);
        }

        .btn-send {
            background-color: var(--midnight-blue);
            color: white !important;
            border-radius: 12px;
            padding: 15px 30px;
            font-weight: 600;
            border: none;
            width: 100%;
            transition: 0.3s;
        }

        .btn-send:hover { background-color: var(--dusty-denim); }

        .btn-back-home {
            color: var(--dusty-denim);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: 0.3s;
            display: inline-block;
            margin-top: 20px;
        }

        .btn-back-home:hover { color: var(--midnight-blue); }

        .map-box {
            border-radius: 25px;
            overflow: hidden;
            border: 5px solid var(--white);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light custom-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">SIMKES <span style="color: var(--calm-ocean);">KRAPYAK</span></a>
    </div>
</nav>

<header class="page-header">
    <div class="container">
        <h2>Kontak Kami</h2>
        <p class="text-muted">Kami siap membantu kebutuhan kesehatan Anda.</p>
    </div>
</header>

<div class="container mb-5" style="margin-top: -30px;">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="contact-card">
                <div class="icon-circle"><i class="bi bi-geo-alt"></i></div>
                <h5>Alamat</h5>
                <p class="text-muted">Jl. KH. Ali Maksum, Krapyak Kulon, Panggungharjo, Kec. Sewon, Kabupaten Bantul, Daerah Istimewa Yogyakarta.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="contact-card">
                <a href="https://wa.me/628888225949" target="_blank" rel="noopener noreferrer" class="d-inline-block mb-3" style="color: inherit; text-decoration: none;">
                    <div class="icon-circle"><i class="bi bi-telephone"></i></div>
                </a>
                <h5>Telepon</h5>
                <p class="text-muted"><a href="tel:+628888225949" class="text-muted text-decoration-none">+62 888-8225-949</a></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="contact-card">
                <div class="icon-circle"><i class="bi bi-envelope"></i></div>
                <h5>Email</h5>
                <p class="text-muted"><a href="mailto:simkes@desa.id" class="text-muted text-decoration-none">simkes@desa.id</a></p>
            </div>
        </div>
    </div>

    <div class="row mt-5 g-5">
        <div class="col-lg-6">
            <div class="form-card text-center">
                <h4 class="mb-4 fw-bold text-start">Kirim Pesan</h4>
                <?php if ($sukses): ?>
                    <div class="alert alert-success rounded-3"><?= htmlspecialchars($sukses) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger rounded-3"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="kontak.php" method="POST" class="text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama Anda" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nomor HP</label>
                        <input type="text" name="no_hp" class="form-control" placeholder="Contoh: 08123456789" value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pesan</label>
                        <textarea name="pesan" class="form-control" rows="4" placeholder="Tulis pesan..." required><?= htmlspecialchars($_POST['pesan'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn-send">Kirim Sekarang</button>
                </form>
                
                <a href="index.php" class="btn" style="background-color: var(--midnight-blue); color: white; border-radius: 12px; padding: 8px 20px; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: 0.3s; margin-top: 10px;">← Kembali ke Beranda</a>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="map-box">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15810.15579222452!2d110.3804863!3d-7.8385258!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a57a1496a7985%3A0xb3e1c20188c03e0d!2sKrapyak%2C%20Panggungharjo%2C%20Sewon%2C%20Bantul%20Regency%2C%20Special%20Region%20of%20Yogyakarta!5e0!3m2!1sen!2sid!4v1715420000000!5m2!1sen!2sid" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                <div class="p-3 text-center bg-white">
                    <a href="https://www.google.com/maps/search/?api=1&query=Puskesmas+Krapyak+Bantul" target="_blank" rel="noopener noreferrer" class="text-decoration-none" style="color: var(--midnight-blue); font-weight: 600;">Buka di Google Maps</a>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="text-center py-5" style="background-color: var(--midnight-blue); color: white; border-top-left-radius: 50px; border-top-right-radius: 50px;">
    <div class="container">
        <p class="mb-0">© 2026 <b>SIMKES Krapyak</b>. <br>Tulus melayani untuk masyarakat.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>