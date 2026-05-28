<?php
include 'config/koneksi.php';

// Deteksi nama primary key agar tidak error jika nama kolom berbeda
$pkField = 'id';
$colResult = mysqli_query($conn, "SHOW COLUMNS FROM jadwal_dokter");
if ($colResult) {
    while ($col = mysqli_fetch_assoc($colResult)) {
        if (in_array($col['Field'], ['id_jadwal', 'id_jadwal_dokter'], true)) {
            $pkField = $col['Field'];
            break;
        }
    }
}
$query = mysqli_query($conn, "SELECT * FROM jadwal_dokter ORDER BY $pkField ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jadwal Dokter - SIMKES Krapyak</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --cloud-blue: #cce2f5;
            --powder-sky: #a8cce6;
            --calm-ocean: #81afd8;
            --dusty-denim: #5F86A6;
            --midnight-blue: #243A5E;
            --white: #FFFFFF;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--white);
            color: var(--midnight-blue);
            line-height: 1.8;
        }

        /* Navbar */
        .custom-navbar {
            background-color: var(--white) !important;
            padding: 15px 0;
            border-bottom: 2px solid var(--cloud-blue);
        }

        .navbar-brand {
            color: var(--midnight-blue) !important;
            font-weight: 700;
        }

        /* Header Section */
        .page-header {
            background: linear-gradient(135deg, var(--cloud-blue) 0%, var(--white) 100%);
            padding: 70px 0 40px;
            text-align: center;
        }

        .page-header h2 {
            font-weight: 700;
            font-size: 2.2rem;
            margin-bottom: 10px;
        }

        /* Table Styling */
        .table-container {
            margin-top: -30px;
            padding-bottom: 80px;
        }

        .card-table {
            border: none;
            border-radius: 25px;
            background: var(--white);
            box-shadow: 0 15px 40px rgba(36, 58, 94, 0.08);
            overflow: hidden; /* Biar sudut tabel ikut tumpul */
        }

        .table {
            margin-bottom: 0;
        }

        .table thead {
            background-color: var(--midnight-blue);
            color: white;
        }

        .table thead th {
            padding: 20px;
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }

        .table tbody td {
            padding: 20px;
            vertical-align: middle;
            border-color: var(--cloud-blue);
            color: var(--dusty-denim);
        }

        .table tbody tr:hover {
            background-color: var(--cloud-blue);
            transition: 0.3s;
        }

        .badge-spesialis {
            background-color: var(--powder-sky);
            color: var(--midnight-blue);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-jadwal {
            background-color: var(--cloud-blue);
            color: var(--dusty-denim);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            border: 1px solid var(--powder-sky);
        }

        .btn-booking-small {
            background-color: var(--midnight-blue);
            color: white !important;
            border-radius: 10px;
            padding: 8px 20px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-booking-small:hover {
            background-color: var(--calm-ocean);
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
        <h2>Jadwal Dokter</h2>
        <p class="text-muted">Temukan waktu pelayanan yang sesuai untuk Anda.</p>
    </div>
</header>

<div class="container table-container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card card-table">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Dokter</th>
                                <th>Spesialisasi</th>
                                <th>Waktu Praktik</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$query || mysqli_num_rows($query) === 0): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Jadwal dokter belum tersedia. Silakan hubungi admin.</td>
                                </tr>
                            <?php else: ?>
                                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold" style="color: var(--midnight-blue);">
                                                <?= htmlspecialchars($row['nama_dokter'] ?? $row['dokter'] ?? 'Tanpa Nama'); ?>
                                            </div>
                                        </td>
                                        <td><span class="badge-spesialis"><?= htmlspecialchars($row['poli'] ?? '-'); ?></span></td>
                                        <td><span class="badge-jadwal"><?= htmlspecialchars($row['waktu_praktik'] ?? $row['jadwal'] ?? 'Belum diatur'); ?></span></td>
                                        <td><a href="booking.php" class="btn-booking-small">Daftar Antrean</a></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <p class="small text-muted italic">*Jadwal dapat berubah sewaktu-waktu sesuai kebijakan puskesmas desa.</p>
                <a href="index.php" class="btn" style="background-color: var(--midnight-blue); color: white; border-radius: 12px; padding: 10px 25px; font-weight: 600; text-decoration: none; transition: 0.3s;">← Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>