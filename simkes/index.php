<?php
include 'config/koneksi.php';

// Ambil daftar poli unik dari jadwal_dokter
$poli_result = mysqli_query($conn, "SELECT DISTINCT poli FROM jadwal_dokter WHERE poli IS NOT NULL AND poli != '' ORDER BY poli ASC");
$daftar_poli = [];
if ($poli_result) {
    while ($p = mysqli_fetch_assoc($poli_result)) {
        $daftar_poli[] = $p['poli'];
    }
}

// Mapping poli ke ikon, deskripsi, dan link halaman detail
$poli_config = [
    'Poli Umum' => [
        'ikon'       => 'bi-clipboard2-pulse',
        'desc'       => 'Layanan kesehatan dasar tulus untuk masyarakat umum.',
        'link'       => 'layanan.php?poli=Poli+Umum',
        'deskripsi'  => 'Layanan kesehatan primer yang tulus untuk seluruh warga Krapyak, mulai dari pemeriksaan fisik hingga konsultasi keluhan harian.',
        'layanan_1'  => 'Pemeriksaan Fisik: Tensi, suhu tubuh, dan konsultasi kesehatan harian.',
        'layanan_2'  => 'Pengobatan Umum: Penanganan flu, batuk, demam, dan infeksi ringan.',
        'layanan_3'  => 'Surat Keterangan: Layanan pembuatan surat keterangan sehat untuk berbagai keperluan.',
    ],
    'Poli Gigi' => [
        'ikon'       => 'bi-emoji-smile',
        'desc'       => 'Pemeriksaan dan perawatan gigi keluarga yang nyaman.',
        'link'       => 'layanan.php?poli=Poli+Gigi',
        'deskripsi'  => 'Wujudkan senyum cerah bersama Poli Gigi SIMKES. Kami hadir dengan peralatan steril dan penanganan lembut untuk meminimalisir rasa takut.',
        'layanan_1'  => 'Pembersihan Karang: Scaling rutin untuk menjaga kesegaran mulut dan gusi.',
        'layanan_2'  => 'Penambalan Gigi: Solusi estetis dan fungsional untuk gigi berlubang.',
        'layanan_3'  => 'Edukasi Gigi Anak: Membiasakan sikat gigi dengan cara yang seru dan menyenangkan.',
    ],
    'KIA' => [
        'ikon'       => 'bi-gender-female',
        'desc'       => 'Kesehatan ibu, bayi, dan tumbuh kembang anak.',
        'link'       => 'layanan.php?poli=KIA',
        'deskripsi'  => 'Pelayanan khusus yang penuh kasih sayang bagi ibu hamil dan balita untuk memastikan tumbuh kembang buah hati terpantau sempurna.',
        'layanan_1'  => 'Pemeriksaan Kehamilan: Pemantauan rutin (ANC) untuk kesehatan ibu dan janin.',
        'layanan_2'  => 'Imunisasi Dasar: Layanan vaksinasi lengkap untuk memperkuat imun si kecil.',
        'layanan_3'  => 'Konsultasi KB: Perencanaan keluarga yang aman, nyaman, dan teredukasi.',
    ],
];

// Fungsi pilih ikon otomatis berdasarkan nama poli
function getPoliIcon($nama) {
    $nama = strtolower($nama);
    if (str_contains($nama, 'gigi'))                        return 'bi-emoji-smile';
    if (str_contains($nama, 'kia') || str_contains($nama, 'ibu') || str_contains($nama, 'anak') || str_contains($nama, 'kandungan')) return 'bi-gender-female';
    if (str_contains($nama, 'mata'))                        return 'bi-eye';
    if (str_contains($nama, 'tht') || str_contains($nama, 'telinga') || str_contains($nama, 'hidung')) return 'bi-ear';
    if (str_contains($nama, 'jantung'))                     return 'bi-heart-pulse';
    if (str_contains($nama, 'kulit'))                       return 'bi-person-arms-up';
    if (str_contains($nama, 'saraf') || str_contains($nama, 'neuro')) return 'bi-activity';
    if (str_contains($nama, 'paru') || str_contains($nama, 'pernafasan')) return 'bi-wind';
    if (str_contains($nama, 'bedah') || str_contains($nama, 'operasi')) return 'bi-scissors';
    if (str_contains($nama, 'jiwa') || str_contains($nama, 'psikiatri') || str_contains($nama, 'psikologi')) return 'bi-brain';
    if (str_contains($nama, 'lansia') || str_contains($nama, 'geriatri')) return 'bi-person-cane';
    if (str_contains($nama, 'gizi') || str_contains($nama, 'nutrisi')) return 'bi-egg-fried';
    if (str_contains($nama, 'umum'))                        return 'bi-clipboard2-pulse';
    return 'bi-hospital';  // default
}

// Auto-seed data default ke poli_info jika belum ada
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS poli_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_poli VARCHAR(100) NOT NULL UNIQUE,
    deskripsi TEXT, layanan_1 VARCHAR(255),
    layanan_2 VARCHAR(255), layanan_3 VARCHAR(255)
)");
foreach ($poli_config as $np => $cfg) {
    $stmt = mysqli_prepare($conn, "INSERT IGNORE INTO poli_info (nama_poli, deskripsi, layanan_1, layanan_2, layanan_3) VALUES (?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, 'sssss', $np, $cfg['deskripsi'], $cfg['layanan_1'], $cfg['layanan_2'], $cfg['layanan_3']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMKES Masyarakat Desa Krapyak</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        :root {
            --cloud-blue: #EDF4FA;
            --powder-sky: #CFE3F1;
            --calm-ocean: #8FB6D8;
            --dusty-denim: #50799b;
            --midnight-blue: #243A5E;
            --white: #FFFFFF;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--white);
            color: var(--midnight-blue);
            overflow-x: hidden;
        }

        .custom-navbar {
            background-color: var(--white) !important;
            padding: 15px 0;
            border-bottom: 2px solid var(--cloud-blue);
            box-shadow: 0 2px 10px rgba(36, 58, 94, 0.05);
        }

        .navbar-brand {
            color: var(--midnight-blue) !important;
            font-size: 1.3rem;
            letter-spacing: 1px;
        }

        /* Variasi Dropdown Menu */
        .dropdown-menu {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(36, 58, 94, 0.1);
            background-color: var(--white);
            padding: 10px;
            margin-top: 10px;
        }

        .dropdown-item {
            border-radius: 10px;
            color: var(--dusty-denim);
            padding: 10px 20px;
            transition: all 0.3s;
        }

        .dropdown-item:hover {
            background-color: var(--cloud-blue);
            color: var(--midnight-blue);
            transform: translateX(5px);
        }

        .nav-link {
            color: var(--dusty-denim) !important;
            font-weight: 400;
            transition: 0.3s;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--midnight-blue) !important;
            font-weight: 600;
        }

        /* Carousel Section */
        .carousel-item {
            height: 65vh;
            min-height: 450px;
            background-color: var(--midnight-blue);
        }
        
        .carousel-image {
            height: 100%;
            width: 100%;
            object-fit: cover;
            filter: brightness(65%);
        }

        .carousel-caption {
            bottom: 25%;
            text-shadow: 2px 2px 15px rgba(0,0,0,0.6);
        }

        .btn-booking {
            background-color: var(--midnight-blue);
            color: var(--white) !important;
            font-weight: 600;
            border-radius: 12px;
            padding: 16px 45px;
            border: none;
            box-shadow: 0 10px 20px rgba(36, 58, 94, 0.2);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-booking:hover {
            transform: translateY(-3px);
            background-color: var(--dusty-denim);
            box-shadow: 0 15px 30px rgba(36, 58, 94, 0.3);
        }

        .section-title {
            font-weight: 700;
            color: var(--midnight-blue);
            margin-bottom: 50px;
        }

        /* Card Layanan Baru (Aesthetic) */
        .card-layanan {
            border: none;
            border-radius: 25px;
            background: var(--white);
            transition: all 0.4s ease;
            padding: 40px !important;
            box-shadow: 0 10px 30px rgba(36, 58, 94, 0.05);
        }

        .card-layanan:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(36, 58, 94, 0.1);
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background-color: var(--cloud-blue);
            color: var(--midnight-blue);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin: 0 auto 25px;
            transition: 0.3s;
        }

        .card-layanan:hover .icon-box {
            background-color: var(--calm-ocean);
            color: white;
        }

        footer {
            background-color: var(--midnight-blue);
            padding: 50px 0 !important;
            color: var(--cloud-blue);
            font-size: 0.9rem;
            border-top-left-radius: 50px;
            border-top-right-radius: 50px;
            margin-top: 80px;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-light custom-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            SIMKES <span style="color: var(--calm-ocean);">KRAPYAK</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="index.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="tentang.php">Tentang</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarLayanan" role="button" data-bs-toggle="dropdown">Layanan</a>
                    <ul class="dropdown-menu border-0 shadow">
                        <?php foreach ($daftar_poli as $p):
                            $link = isset($poli_config[$p]) ? $poli_config[$p]['link'] : 'booking.php';
                        ?>
                        <li><a class="dropdown-item" href="<?= htmlspecialchars($link) ?>"><?= htmlspecialchars($p) ?></a></li>
                        <?php endforeach; ?>
                        <?php if (empty($daftar_poli)): ?>
                        <li><a class="dropdown-item text-muted" href="#">Belum ada layanan</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="jadwal.php">Jadwal</a></li>
                <li class="nav-item"><a class="nav-link" href="booking.php">Daftar Antrean</a></li>
                <li class="nav-item"><a class="nav-link" href="kontak.php">Kontak</a></li>
            </ul>
        </div>
    </div>
</nav>

<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=1350&q=80" class="carousel-image" alt="Puskesmas">
            <div class="carousel-caption">
                <h1 class="fw-bold">Selamat Datang di SIMKES</h1>
                <p>Melayani kesehatan masyarakat Desa Krapyak dengan sepenuh hati.</p>
                <a href="booking.php" class="btn-booking">Daftar Antrean Sekarang</a>
            </div>
        </div>
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1504813184591-01572f98c85f?auto=format&fit=crop&w=1350&q=80" class="carousel-image" alt="Tenaga Medis">
            <div class="carousel-caption">
                <h1 class="fw-bold">Layanan Kesehatan Prima</h1>
                <p>Dokter dan tenaga medis ahli siap membantu kesehatan keluarga Anda.</p>
                <a href="jadwal.php" class="btn-booking" style="background-color: var(--calm-ocean);">Cek Jadwal Dokter</a>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<section class="py-5">
    <div class="container text-center mt-5">
        <h2 class="section-title">Layanan Utama</h2>
        <div class="row mt-4 g-4">
            <?php foreach ($daftar_poli as $p):
                $cfg  = $poli_config[$p] ?? [
                    'ikon' => getPoliIcon($p),
                    'desc' => 'Layanan kesehatan ' . $p . ' untuk masyarakat.',
                    'link' => 'layanan.php?poli=' . urlencode($p),
                ];
            ?>
            <div class="col-md-4">
                <div class="card card-layanan">
                    <div class="icon-box">
                        <i class="bi <?= htmlspecialchars($cfg['ikon']) ?>"></i>
                    </div>
                    <h4 class="fw-bold"><?= htmlspecialchars($p) ?></h4>
                    <p class="text-muted"><?= htmlspecialchars($cfg['desc']) ?></p>
                    <a href="layanan.php?poli=<?= urlencode($p) ?>" class="btn btn-sm btn-outline-primary rounded-pill mt-2">Lihat Detail</a>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($daftar_poli)): ?>
            <div class="col-12 text-center text-muted py-4">Belum ada layanan tersedia.</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<footer class="text-center">
    <div class="container">
        <p class="mb-0">© 2026 <b>SIMKES Desa Krapyak</b>. <br>Tulus Melayani, Sehat Bersama.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>