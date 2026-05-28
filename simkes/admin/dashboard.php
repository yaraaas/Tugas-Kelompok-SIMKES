<?php
include '../config/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

function formatTanggalIndonesia($tanggal)
{
    $bulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];
    $ts = strtotime($tanggal);
    return date('d', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
}

session_start();

// Proteksi: Jika belum login atau BUKAN admin, tendang kembali ke halaman login admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// 1. Hitung Total Pasien Terdaftar
$query_pasien = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'pasien'");
$data_pasien = mysqli_fetch_assoc($query_pasien);

// 2. Hitung Total Booking Hari Ini
$hari_ini = date('Y-m-d');
$query_booking = mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE tanggal = '$hari_ini'");
$data_booking = mysqli_fetch_assoc($query_booking);

// 3. Hitung Antrean yang Masih Menunggu
$query_tunggu = mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE status = 'menunggu'");
$data_tunggu = mysqli_fetch_assoc($query_tunggu);

// 4. Ambil 5 Aktivitas Booking Terbaru
$query_terbaru = mysqli_query($conn, "SELECT * FROM booking ORDER BY id_booking DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIMKES Krapyak</title>
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
            background-color: #F8FAFC;
            color: var(--midnight-blue);
        }

        /* Sidebar Styling */
        .sidebar {
            height: 100vh;
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--midnight-blue);
            padding-top: 20px;
            color: white;
        }

        .sidebar .nav-link {
            color: #A0AEC0;
            padding: 12px 20px;
            margin: 5px 15px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: var(--dusty-denim);
            color: white;
        }

        /* Main Content Styling */
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }

        .card-stats {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            transition: transform 0.3s;
        }

        .card-stats:hover {
            transform: translateY(-5px);
        }

        .table-responsive {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="px-4 mb-4">
        <h5 class="fw-bold text-white mb-0">SIMKES <span style="color: var(--calm-ocean);">ADMIN</span></h5>
        <small class="text-muted">Panel Kendali Klinik</small>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link active" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="kelola_booking.php"><i class="bi bi-clipboard-check me-2"></i> Data Antrean</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="kelola_jadwal.php"><i class="bi bi-calendar3 me-2"></i> Jadwal Dokter</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="kelola_pesan.php"><i class="bi bi-envelope me-2"></i> Pesan Masuk</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../index.php"><i class="bi bi-house-door me-2"></i> Lihat Beranda</a>
        </li>
        <li class="nav-item mt-4">
            <a class="nav-link text-danger" href="../logout.php" onclick="return confirm('Yakin ingin keluar?')"><i class="bi bi-box-arrow-left me-2"></i> Keluar</a>
        </li>
    </ul>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Selamat Datang, <?= htmlspecialchars($_SESSION['nama']); ?>! 👋</h4>
            <p class="text-muted small mb-0">Hari ini tanggal: <?= formatTanggalIndonesia(date('Y-m-d')); ?></p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card card-stats p-3 bg-white border-start border-primary border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1 fw-bold">TOTAL PASIEN</p>
                        <h3 class="fw-bold mb-0"><?= $data_pasien['total']; ?></h3>
                    </div>
                    <div class="fs-1 text-primary opacity-50"><i class="bi bi-people"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stats p-3 bg-white border-start border-warning border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1 fw-bold">ANTREAN HARI INI</p>
                        <h3 class="fw-bold mb-0"><?= $data_booking['total']; ?></h3>
                    </div>
                    <div class="fs-1 text-warning opacity-50"><i class="bi bi-calendar-check"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stats p-3 bg-white border-start border-danger border-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1 fw-bold">ANTREAN MENUNGGU</p>
                        <h3 class="fw-bold mb-0"><?= $data_tunggu['total']; ?></h3>
                    </div>
                    <div class="fs-1 text-danger opacity-50"><i class="bi bi-hourglass-split"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i> Pendaftar Antrean Terbaru</h5>
            <div class="table-responsive border-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Antrean</th>
                            <th>Nama Pasien</th>
                            <th>Poli Tujuan</th>
                            <th>Tanggal Periksa</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_terbaru) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($query_terbaru)): ?>
                                <tr>
                                    <td><span class="badge bg-secondary p-2 rounded-circle"><?= $row['nomor_antrian']; ?></span></td>
                                    <td class="fw-bold"><?= htmlspecialchars($row['nama']); ?></td>
                                    <td><?= htmlspecialchars($row['poli']); ?></td>
                                    <td><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'menunggu'): ?>
                                            <span class="badge bg-warning text-dark text-capitalize"><?= $row['status']; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success text-capitalize"><?= $row['status']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada data antrean yang masuk.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>