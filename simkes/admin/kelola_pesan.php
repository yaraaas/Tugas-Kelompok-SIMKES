<?php
include '../config/koneksi.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    if (!isset($_SESSION['admin'])) {
        header("Location: login.php");
        exit;
    }
}

// Auto-create tabel pesan jika belum ada
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS pesan (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nama   VARCHAR(100) NOT NULL,
    no_hp  VARCHAR(20)  NOT NULL,
    pesan  TEXT         NOT NULL,
    waktu  DATETIME     NOT NULL
)");

// Fungsi format tanggal bahasa Indonesia
function tgl_indo($datetime) {
    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $d = date('j', strtotime($datetime));
    $m = (int) date('n', strtotime($datetime));
    $y = date('Y', strtotime($datetime));
    $t = date('H:i', strtotime($datetime));
    return "$d {$bulan[$m]} $y, $t";
}

// Hapus pesan jika diminta
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM pesan WHERE id = $id");
    header("Location: kelola_pesan.php?deleted=1");
    exit;
}

// Ambil semua pesan, terbaru di atas
$query = mysqli_query($conn, "SELECT * FROM pesan ORDER BY waktu DESC");
$total = mysqli_num_rows($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Pesan Masuk - SIMKES Admin</title>
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
            background-color: #f4f7fa;
            color: var(--midnight-blue);
        }

        .sidebar {
            background-color: var(--midnight-blue);
            min-height: 100vh;
            color: white;
            padding-top: 30px;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
        }

        .sidebar h4 { font-weight: 700; letter-spacing: 1px; }

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

        .main-content { padding: 40px; }

        .table-card {
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

        .badge-total {
            background-color: var(--cloud-blue);
            color: var(--midnight-blue);
            font-size: 0.85rem;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
        }

        .pesan-text {
            max-width: 350px;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .btn-hapus {
            font-size: 0.8rem;
            padding: 5px 12px;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 sidebar px-0">
            <h4 class="text-center mb-4 text-white">SIMKES <span style="color: var(--calm-ocean);">KRAPYAK</span></h4>
            <hr style="border-color: rgba(255,255,255,0.1)">
            <a href="index.php">📋 Data Booking</a>
            <a href="jadwal.php">👨‍⚕️ Jadwal Dokter</a>
            <a href="kelola_poli.php">🏥 Info Poli</a>
            <a href="kelola_pesan.php" class="active">✉️ Pesan Masuk</a>
            <a href="../logout.php">🚪 Keluar Dashboard</a>
        </div>

        <div class="col-md-9 col-lg-10 main-content">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold m-0">Pesan Masuk</h2>
                    <p class="text-muted mb-0">Rekap pesan dari halaman Kontak.</p>
                </div>
                <span class="badge-total"><?= $total ?> Pesan</span>
            </div>

            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                    ✅ Pesan berhasil dihapus.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card table-card p-4">
                <?php if ($total > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:50px">#</th>
                                <th>Nama Pengirim</th>
                                <th>No HP</th>
                                <th>Isi Pesan</th>
                                <th>Waktu Kirim</th>
                                <th class="text-center" style="width:110px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($query)): ?>
                            <tr>
                                <td class="text-muted"><?= $no++ ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($row['nama']) ?></td>
                                <td class="text-muted small">
                                    <?php
                                        $nomor = preg_replace('/\D+/', '', $row['no_hp'] ?? '');
                                        if (strpos($nomor, '0') === 0) $nomor = '62' . substr($nomor, 1);
                                    ?>
                                    <a href="https://wa.me/<?= $nomor ?>" target="_blank" style="color: #25D366; font-weight: 600; text-decoration: none;">
                                        <?= htmlspecialchars($row['no_hp'] ?? '-') ?>
                                    </a>
                                </td>
                                <td>
                                    <div class="pesan-text text-muted"><?= nl2br(htmlspecialchars($row['pesan'])) ?></div>
                                </td>
                                <td class="text-muted small">
                                    📅 <?= tgl_indo($row['waktu']) ?>
                                </td>
                                <td class="text-center">
                                    <a href="kelola_pesan.php?hapus=<?= $row['id'] ?>"
                                       class="btn btn-danger btn-hapus"
                                       onclick="return confirm('Hapus pesan dari <?= htmlspecialchars($row['nama']) ?>?')">
                                        🗑️ Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <div style="font-size: 3rem;">📭</div>
                        <p class="mt-3 mb-0">Belum ada pesan masuk.</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
