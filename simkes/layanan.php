<?php
include 'config/koneksi.php';

// Auto-create tabel poli_info
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS poli_info (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nama_poli   VARCHAR(100) NOT NULL UNIQUE,
    deskripsi   TEXT,
    layanan_1   VARCHAR(255),
    layanan_2   VARCHAR(255),
    layanan_3   VARCHAR(255)
)");

// Ambil nama poli dari URL
$nama_poli = trim($_GET['poli'] ?? '');
if ($nama_poli === '') {
    header('Location: index.php');
    exit;
}

// Cek apakah poli ada di jadwal_dokter
$cek = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM jadwal_dokter WHERE poli = ?");
mysqli_stmt_bind_param($cek, 's', $nama_poli);
mysqli_stmt_execute($cek);
$res = mysqli_stmt_get_result($cek);
$ada = mysqli_fetch_assoc($res)['total'];
mysqli_stmt_close($cek);

if ((int)$ada === 0) {
    header('Location: index.php');
    exit;
}

// Ambil info poli
$stmt = mysqli_prepare($conn, "SELECT * FROM poli_info WHERE nama_poli = ?");
mysqli_stmt_bind_param($stmt, 's', $nama_poli);
mysqli_stmt_execute($stmt);
$info = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

// Ambil daftar dokter untuk poli ini
$dok = mysqli_prepare($conn, "SELECT * FROM jadwal_dokter WHERE poli = ? ORDER BY nama_dokter ASC");
mysqli_stmt_bind_param($dok, 's', $nama_poli);
mysqli_stmt_execute($dok);
$dokter_list = mysqli_stmt_get_result($dok);
mysqli_stmt_close($dok);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($nama_poli) ?> - SIMKES Krapyak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --cloud-blue: #cce2f5; --powder-sky: #a8cce6;
            --calm-ocean: #81afd8; --dusty-denim: #5F86A6;
            --midnight-blue: #243A5E; --white: #FFFFFF;
        }
        body { font-family: 'Poppins', sans-serif; background-color: var(--white); color: var(--midnight-blue); line-height: 1.8; }
        .custom-navbar { background-color: var(--white) !important; padding: 15px 0; border-bottom: 2px solid var(--cloud-blue); }
        .about-header { background: linear-gradient(135deg, var(--cloud-blue) 0%, var(--white) 100%); padding: 80px 0 50px; text-align: center; }
        .card-about { border: none; border-radius: 30px; background: var(--white); box-shadow: 0 15px 40px rgba(36,58,94,0.06); padding: 40px; margin-top: -30px; }
        .feature-list { list-style: none; padding-left: 0; }
        .feature-list li { padding: 12px 0; border-bottom: 1px solid var(--cloud-blue); display: flex; align-items: flex-start; }
        .feature-list li::before { content: '🩺'; margin-right: 15px; flex-shrink: 0; }
        .btn-booking-now { background-color: var(--midnight-blue); color: white !important; border-radius: 12px; padding: 12px 35px; text-decoration: none; font-weight: 600; display: inline-block; margin-top: 30px; transition: 0.3s; }
        .btn-booking-now:hover { background-color: var(--dusty-denim); transform: translateY(-3px); }
        .dokter-card { border: none; border-radius: 15px; background: var(--cloud-blue); padding: 15px 20px; margin-bottom: 10px; }
        .no-info { color: var(--dusty-denim); font-style: italic; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light custom-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">SIMKES <span style="color: var(--calm-ocean);">KRAPYAK</span></a>
    </div>
</nav>

<header class="about-header">
    <div class="container">
        <h2><?= htmlspecialchars($nama_poli) ?></h2>
        <p class="text-muted"><?= $info ? htmlspecialchars($info['deskripsi']) : 'Layanan kesehatan ' . htmlspecialchars($nama_poli) . ' untuk masyarakat Krapyak.' ?></p>
    </div>
</header>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-about">

                <?php if ($info && ($info['layanan_1'] || $info['layanan_2'] || $info['layanan_3'])): ?>
                <h5 class="mt-2 mb-3 fw-bold">Layanan Unggulan:</h5>
                <ul class="feature-list">
                    <?php foreach (['layanan_1','layanan_2','layanan_3'] as $lk):
                        if (!empty($info[$lk])): ?>
                        <li><?= nl2br(htmlspecialchars($info[$lk])) ?></li>
                    <?php endif; endforeach; ?>
                </ul>
                <?php else: ?>
                <p class="no-info">Informasi layanan unggulan belum tersedia. Silakan hubungi admin.</p>
                <?php endif; ?>

                <?php if (mysqli_num_rows($dokter_list) > 0): ?>
                <h5 class="mt-4 mb-3 fw-bold">Dokter / Tenaga Medis:</h5>
                <?php while ($d = mysqli_fetch_assoc($dokter_list)): ?>
                <div class="dokter-card d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold"><?= htmlspecialchars($d['nama_dokter'] ?? $d['dokter'] ?? $d['nama'] ?? '-') ?></div>
                        <small class="text-muted"><?= htmlspecialchars($d['waktu_praktik'] ?? $d['jadwal'] ?? '-') ?></small>
                    </div>
                    <span style="background:var(--powder-sky);color:var(--midnight-blue);padding:4px 12px;border-radius:8px;font-size:0.8rem;font-weight:600;">
                        <?= htmlspecialchars($d['poli'] ?? '-') ?>
                    </span>
                </div>
                <?php endwhile; ?>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <a href="booking.php" class="btn-booking-now">Daftar Antrean <?= htmlspecialchars($nama_poli) ?></a><br>
                    <?php if (($_GET['from'] ?? '') === 'admin'): ?>
                        <a href="admin/kelola_poli.php" class="btn mt-3" style="background-color:#50799b;color:white;border-radius:12px;padding:8px 20px;font-size:0.85rem;font-weight:600;text-decoration:none;">← Kembali ke Kelola Poli</a>
                    <?php else: ?>
                        <a href="index.php" class="btn mt-3" style="background-color:var(--midnight-blue);color:white;border-radius:12px;padding:8px 20px;font-size:0.85rem;font-weight:600;text-decoration:none;">← Kembali ke Beranda</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
