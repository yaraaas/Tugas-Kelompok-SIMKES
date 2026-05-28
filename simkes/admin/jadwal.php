<?php
session_start();
if (!isset($_SESSION['admin']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
    header("Location: login.php");
    exit;
}
include '../config/koneksi.php';

$columnResult = mysqli_query($conn, "SHOW COLUMNS FROM jadwal_dokter");
$columns = [];
if ($columnResult) {
    while ($col = mysqli_fetch_assoc($columnResult)) {
        $columns[] = $col['Field'];
    }
}
$pkField = in_array('id', $columns, true) ? 'id' : (in_array('id_jadwal', $columns, true) ? 'id_jadwal' : (in_array('id_jadwal_dokter', $columns, true) ? 'id_jadwal_dokter' : 'id'));

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
    <style>
        :root {
            --bg-gradient-start: #e0eafc;
            --bg-gradient-end: #cfdef3;
            --midnight-blue: #243A5E;
            --powder-sky: #CFE3F1;
            --calm-ocean: #8FB6D8;
            --white: #FFFFFF;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
            min-height: 100vh;
            color: var(--midnight-blue);
        }
        .sidebar {
            background-color: var(--midnight-blue);
            min-height: 100vh;
            color: white;
            padding-top: 30px;
            box-shadow: 4px 0 15px rgba(36, 58, 94, 0.1);
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
            background-color: rgba(255, 255, 255, 0.07);
            border-left: 5px solid var(--calm-ocean);
        }
        .main-content { padding: 40px; }
        .card-table {
            border: none;
            border-radius: 24px;
            background: var(--white);
            box-shadow: 0 15px 40px rgba(36, 58, 94, 0.07);
            overflow: hidden;
        }
        .table thead { background-color: var(--midnight-blue); color: var(--white); }
        .table th, .table td { padding: 16px; vertical-align: middle; }
        .form-control-custom {
            border-radius: 8px;
            border: 2px solid #e8f0fe;
            padding: 6px 12px;
            color: var(--midnight-blue);
            font-weight: 500;
        }
        .form-control-custom:focus {
            border-color: var(--calm-ocean);
            box-shadow: none;
            outline: none;
        }
        .btn-save {
            background-color: #198754;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 6px 15px;
            font-weight: 600;
            transition: 0.2s;
        }
        .btn-save:hover { background-color: #146c43; transform: translateY(-1px); }
        .btn-add {
            background-color: #0d6efd;
            color: white;
            border-radius: 8px;
            padding: 10px 18px;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.2s, background-color 0.2s;
        }
        .btn-add:hover {
            background-color: #0b5ed7;
            transform: translateY(-1px);
            color: white;
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
            <a href="jadwal.php" class="active">👨‍⚕️ Jadwal Dokter</a>
            <a href="kelola_poli.php">🏥 Info Poli</a>
            <a href="kelola_pesan.php">✉️ Pesan Masuk</a>
            <a href="../logout.php">🚪 Keluar Dashboard</a>
        </div>

        <div class="col-md-9 col-lg-10 main-content">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-3">
                <div>
                    <h2 class="fw-bold text-dark m-0">Pengaturan Jadwal Dokter</h2>
                    <p class="text-muted mb-0">Sesuaikan hari dan jam operasional dokter/poli klinik yang tampil di web depan.</p>
                </div>
                <a href="tambah_jadwal.php" class="btn btn-add">+ Tambah Jadwal Dokter & Poli</a>
            </div>

            <div class="card card-table p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Dokter / Bidan</th>
                                <th>Poli / Spesialis</th>
                                <th>Hari & Jam Praktik</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($query) == 0) : ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada data jadwal dokter.</td>
                                </tr>
                            <?php endif; ?>
                            
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($query)) : ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td class="fw-bold text-dark">
                                    <?= htmlspecialchars($row['nama_dokter'] ?? $row['dokter'] ?? $row['nama'] ?? 'Tanpa Nama'); ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['poli'] ?? $row['spesialis'] ?? '-'); ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['waktu_praktik'] ?? $row['jadwal'] ?? $row['jam_praktik'] ?? $row['waktu'] ?? 'Belum diatur'); ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="edit_jadwal.php?id=<?= (int) ($row['id'] ?? $row['id_jadwal'] ?? $row['id_jadwal_dokter'] ?? 0); ?>" class="btn btn-save btn-sm">✏️ Edit</a>
                                        <a href="hapus_jadwal.php?id=<?= (int) ($row['id'] ?? $row['id_jadwal'] ?? $row['id_jadwal_dokter'] ?? 0); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus jadwal ini?');">🗑️ Hapus</a>
                                    </div>
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

</body>
</html>