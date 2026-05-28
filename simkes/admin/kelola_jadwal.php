<?php
include '../config/koneksi.php';
session_start();

// Proteksi: Hanya admin yang boleh masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Deteksi struktur tabel jadwal_dokter
$columnResult = mysqli_query($conn, "SHOW COLUMNS FROM jadwal_dokter");
$columns = [];
if ($columnResult) {
    while ($col = mysqli_fetch_assoc($columnResult)) {
        $columns[] = $col['Field'];
    }
}
$hasColumn = fn($name) => in_array($name, $columns, true);

$pkField     = $hasColumn('id') ? 'id' : ($hasColumn('id_jadwal') ? 'id_jadwal' : null);
$doctorField = $hasColumn('nama_dokter') ? 'nama_dokter' : ($hasColumn('dokter') ? 'dokter' : null);
$poliField   = $hasColumn('poli') ? 'poli' : null;
$timeField   = $hasColumn('jadwal') ? 'jadwal' : ($hasColumn('waktu_praktik') ? 'waktu_praktik' : ($hasColumn('jam_praktik') ? 'jam_praktik' : null));

if (!$pkField || !$doctorField || !$poliField || !$timeField) {
    die('Struktur tabel jadwal_dokter tidak lengkap. Periksa kolom database.');
}

// Ambil semua data jadwal dokter
$query = mysqli_query($conn, "SELECT * FROM jadwal_dokter ORDER BY {$pkField} ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jadwal Dokter - SIMKES Krapyak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --midnight-blue: #243A5E;
            --dusty-denim: #50799b;
            --calm-ocean: #8FB6D8;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F8FAFC;
        }
        .sidebar {
            height: 100vh;
            width: 260px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--midnight-blue);
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: #A0AEC0;
            padding: 12px 20px;
            margin: 5px 15px;
            border-radius: 10px;
            font-weight: 500;
            text-decoration: none;
            display: block;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: var(--dusty-denim);
            color: white;
        }
        .main-content {
            margin-left: 260px;
            padding: 30px;
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
            <a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="kelola_booking.php"><i class="bi bi-clipboard-check me-2"></i> Data Antrean</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="kelola_jadwal.php"><i class="bi bi-calendar3 me-2"></i> Jadwal Dokter</a>
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
            <h4 class="fw-bold mb-0">Kelola Jadwal Praktik</h4>
            <p class="text-muted small mb-0">Atur waktu operasional dokter dan bidan Desa Krapyak</p>
        </div>
        <a href="tambah_jadwal.php" class="btn text-white px-3 fw-600 rounded-pill" style="background-color: var(--midnight-blue);">
            <i class="bi bi-plus-circle me-1"></i> Tambah Dokter
        </a>
    </div>

    <div class="table-responsive border-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Nama Tenaga Medis</th>
                    <th>Poli / Spesialis</th>
                    <th>Hari & Jam Kerja</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                if (mysqli_num_rows($query) > 0): 
                    while($row = mysqli_fetch_assoc($query)): 
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($row[$doctorField] ?? 'Tanpa Nama'); ?></td>
                        
                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row[$poliField] ?? '-'); ?></span></td>
                        
                        <td>
                            <i class="bi bi-clock text-muted me-1"></i> 
                            <?= htmlspecialchars($row[$timeField] ?? 'Belum diatur'); ?>
                        </td>
                        
                        <td class="text-center">
                            <a href="edit_jadwal.php?id=<?= (int) $row[$pkField]; ?>" class="btn btn-sm text-white px-3 rounded-pill me-1" style="background-color: var(--dusty-denim);">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <a href="hapus_jadwal.php?id=<?= (int) $row[$pkField]; ?>" class="btn btn-sm btn-danger px-3 rounded-pill" onclick="return confirm('Yakin ingin menghapus jadwal dokter ini?')">
                                <i class="bi bi-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php 
                    endwhile; 
                else: 
                ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada data jadwal dokter.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>