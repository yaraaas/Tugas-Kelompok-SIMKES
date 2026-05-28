<?php
session_start();
// Waktu lokal Indonesia (Jakarta)
date_default_timezone_set('Asia/Jakarta');

function formatTanggalIndonesia($tanggal) {
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
        12 => 'Desember'
    ];
    $ts = strtotime($tanggal);
    return date('d', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
}

// Kunci keamanan: Kalau belum login, lempar balik ke login.php
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include '../config/koneksi.php';

// 1. PROSES HAPUS DATA
if (isset($_GET['hapus']) && ctype_digit($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $delete = mysqli_prepare($conn, "DELETE FROM booking WHERE id_booking = ?");
    if ($delete) {
        mysqli_stmt_bind_param($delete, 'i', $id);
        if (mysqli_stmt_execute($delete)) {
            mysqli_stmt_close($delete);
            echo "<script>alert('Data pendaftaran berhasil dihapus!'); window.location.href='index.php';</script>";
            exit;
        }
        mysqli_stmt_close($delete);
    }
}

// 2. HITUNG RINGKASAN DATA UNTUK KARTU INFORMASI
$total_pasien = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking"))['total'];
$total_menunggu = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE status = 'menunggu'"))['total'];
$total_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM booking WHERE status = 'selesai'"))['total'];

// 3. AMBIL SEMUA DATA BOOKING
$query = mysqli_query($conn, "SELECT * FROM booking ORDER BY tanggal DESC, poli ASC, nomor_antrian ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIMKES Krapyak</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
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
            background-color: #f4f7fa;
            color: var(--midnight-blue);
        }

        /* Sidebar Style */
        .sidebar {
            background-color: var(--midnight-blue);
            min-height: 100vh;
            color: white;
            padding-top: 30px;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
        }
        
        .sidebar h4 {
            font-weight: 700;
            letter-spacing: 1px;
        }

        .sidebar a {
            color: var(--powder-sky);
            text-decoration: none;
            display: block;
            padding: 14px 25px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .sidebar a:hover, .sidebar a.active {
            color: var(--white);
            background-color: rgba(255, 255, 255, 0.05);
            border-left: 5px solid var(--calm-ocean);
        }

        /* Main Content */
        .main-content { padding: 40px; }

        /* Info Cards */
        .card-stat {
            border: none;
            border-radius: 18px;
            padding: 20px;
            background: var(--white);
            box-shadow: 0 10px 25px rgba(36, 58, 94, 0.03);
            transition: transform 0.3s;
        }

        .card-stat:hover { transform: translateY(-5px); }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* Table Card */
        .card-table {
            border: none;
            border-radius: 20px;
            background: var(--white);
            box-shadow: 0 15px 35px rgba(36, 58, 94, 0.04);
            overflow: hidden;
        }

        .table thead {
            background-color: var(--midnight-blue);
            color: var(--white);
        }

        .table th, .table td { padding: 15px; vertical-align: middle; }

        .badge-waiting {
            background-color: #fff3cd; color: #856404;
            padding: 6px 12px; border-radius: 30px; font-weight: 600; font-size: 0.75rem; white-space: nowrap;
        }

        .badge-success-custom {
            background-color: #d4edda; color: #155724;
            padding: 6px 12px; border-radius: 30px; font-weight: 600; font-size: 0.75rem; white-space: nowrap;
        }

        .btn-action { border-radius: 8px; padding: 6px 14px; font-weight: 600; font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 sidebar px-0">
            <h4 class="text-center mb-4 text-white">SIMKES <span style="color: var(--calm-ocean);">KRAPYAK</span></h4>
            <hr style="border-color: rgba(255,255,255,0.1)">
            <a href="index.php" class="active">📋 Data Booking</a>
            <a href="jadwal.php">👨‍⚕️ Jadwal Dokter</a>
            <a href="kelola_poli.php">🏥 Info Poli</a>
            <a href="kelola_pesan.php">✉️ Pesan Masuk</a>
            <a href="../logout.php">🚪 Keluar Dashboard</a>
        </div>

        <div class="col-md-9 col-lg-10 main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold m-0">Panel Kendali Admin</h2>
                    <p class="text-muted mb-0">Kelola antrean dan pendaftaran pasien dengan mudah.</p>
                </div>
                <div class="bg-white px-3 py-2 rounded-3 shadow-sm fw-bold">
                    📅 <?= formatTanggalIndonesia(date('Y-m-d')); ?>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card-stat d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small fw-bold">TOTAL PENDAFTAR</p>
                            <h3 class="fw-bold m-0"><?= $total_pasien; ?> <span class="text-muted small fs-6">Orang</span></h3>
                        </div>
                        <div class="stat-icon" style="background-color: #e2ecf7; color: var(--midnight-blue);">👥</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-stat d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small fw-bold">PASIEN MENUNGGU</p>
                            <h3 class="fw-bold m-0" style="color: #b27d00;"><?= $total_menunggu; ?> <span class="text-muted small fs-6">Orang</span></h3>
                        </div>
                        <div class="stat-icon" style="background-color: #fff3cd; color: #856404;">⏳</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-stat d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small fw-bold">SELESAI DIPERIKSA</p>
                            <h3 class="fw-bold m-0" style="color: #198754;"><?= $total_selesai; ?> <span class="text-muted small fs-6">Pasien</span></h3>
                        </div>
                        <div class="stat-icon" style="background-color: #d4edda; color: #155724;">✅</div>
                    </div>
                </div>
            </div>

            <div class="card card-table p-4">
                <h5 class="fw-bold mb-3">Daftar Urutan Antrean Pasien</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead>
                            <tr>
                                <th>No Antrean</th>
                                <th>Nama Lengkap</th>
                                <th>NIK</th>
                                <th>No HP</th>
                                <th>Keluhan Singkat</th>
                                <th>Tujuan Poli</th>
                                <th>Tanggal Periksa</th>
                                <th>Status</th>
                                <th class="text-center">Aksi Operasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($query) == 0) : ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Belum ada pasien yang terdaftar.</td>
                                </tr>
                            <?php endif; ?>
                            
                            <?php while ($row = mysqli_fetch_assoc($query)) : ?>
                            <tr>
                                <td>
                                    <span class="badge bg-primary fs-6 px-3 py-2" style="background-color: var(--midnight-blue) !important;">
                                        #<?= sprintf("%03d", $row['nomor_antrian']); ?>
                                    </span>
                                </td>
                                <td class="fw-bold"><?= htmlspecialchars($row['nama']); ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($row['nik']); ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($row['no_hp']); ?></td>
                                <td class="text-muted small" style="max-width: 220px; white-space: normal;">
                                    <?= htmlspecialchars($row['keluhan']); ?>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: var(--powder-sky); color: var(--midnight-blue); font-weight: 600;">
                                        <?= htmlspecialchars($row['poli']); ?>
                                    </span>
                                </td>
                                <td><?= formatTanggalIndonesia($row['tanggal']); ?></td>
                                <td>
                                    <?php if ($row['status'] == 'menunggu') : ?>
                                        <span class="badge-waiting">⏳ Menunggu</span>
                                    <?php elseif ($row['status'] == 'selesai') : ?>
                                        <span class="badge-success-custom">✅ Selesai</span>
                                    <?php else : ?>
                                        <span class="badge-waiting"><?= htmlspecialchars(ucfirst($row['status'])) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="edit.php?id=<?= $row['id_booking']; ?>" class="btn btn-warning btn-action text-dark me-1">📝 Edit</a>
                                    <a href="index.php?hapus=<?= $row['id_booking']; ?>" class="btn btn-danger btn-action text-white" onclick="return confirm('Apakah Anda yakin ingin menghapus antrean ini?')">🗑️ Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>