<?php
session_start();
// Kunci keamanan admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include '../config/koneksi.php';

// Ambil daftar poli dari jadwal_dokter
$poli_result = mysqli_query($conn, "SELECT DISTINCT poli FROM jadwal_dokter WHERE poli IS NOT NULL AND poli != '' ORDER BY poli ASC");
$daftar_poli = [];
if ($poli_result) {
    while ($p = mysqli_fetch_assoc($poli_result)) {
        $daftar_poli[] = $p['poli'];
    }
}

// Ambil data pasien berdasarkan ID yang mau diedit
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$id = (int) $_GET['id'];
$stmt = mysqli_prepare($conn, "SELECT * FROM booking WHERE id_booking = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} else {
    $data = false;
}

// Jika data tidak ditemukan
if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='index.php';</script>";
    exit;
}

// Proses Update Data saat tombol ditekan
if (isset($_POST['update'])) {
    $nama = trim(mysqli_real_escape_string($conn, $_POST['nama'] ?? ''));
    $nik = trim(mysqli_real_escape_string($conn, $_POST['nik'] ?? ''));
    $no_hp = trim(mysqli_real_escape_string($conn, $_POST['no_hp'] ?? ''));
    $tanggal = trim($_POST['tanggal'] ?? '');
    $poli = trim($_POST['poli'] ?? '');
    $keluhan = trim(mysqli_real_escape_string($conn, $_POST['keluhan'] ?? ''));
    $status = $_POST['status'] ?? 'menunggu';
    if (!in_array($status, ['menunggu', 'selesai'], true)) {
        $status = 'menunggu';
    }

    $update = mysqli_prepare($conn, "UPDATE booking SET nama = ?, nik = ?, no_hp = ?, tanggal = ?, poli = ?, keluhan = ?, status = ? WHERE id_booking = ?");
    if ($update) {
        mysqli_stmt_bind_param($update, 'sssssssi', $nama, $nik, $no_hp, $tanggal, $poli, $keluhan, $status, $id);
        if (mysqli_stmt_execute($update)) {
            mysqli_stmt_close($update);
            echo "<script>alert('Data pasien berhasil diperbarui!'); window.location.href='index.php';</script>";
            exit;
        }
        $error = mysqli_stmt_error($update);
        mysqli_stmt_close($update);
        echo "<script>alert('Gagal memperbarui data: " . addslashes($error) . "');</script>";
    } else {
        echo "<script>alert('Gagal mempersiapkan query update.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Pasien - SIMKES Krapyak</title>
    
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

        .sidebar a:hover {
            color: var(--white);
            background-color: rgba(255, 255, 255, 0.07);
        }

        .main-content { padding: 40px; }

        /* Form Card Style */
        .card-form {
            border: none;
            border-radius: 24px;
            background: var(--white);
            box-shadow: 0 15px 40px rgba(36, 58, 94, 0.07);
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--midnight-blue);
            margin-bottom: 8px;
        }

        .form-control-custom {
            border-radius: 12px;
            border: 2px solid #e8f0fe;
            padding: 10px 15px;
            color: var(--midnight-blue);
            font-weight: 500;
            transition: all 0.3s;
        }

        .form-control-custom:focus {
            border-color: var(--calm-ocean);
            box-shadow: 0 0 0 4px rgba(143, 182, 216, 0.15);
            outline: none;
        }

        .btn-custom-save {
            background-color: var(--midnight-blue);
            color: white;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 600;
            border: none;
            transition: 0.2s;
        }

        .btn-custom-save:hover {
            background-color: #1a2a44;
            transform: translateY(-2px);
        }

        .btn-custom-cancel {
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 600;
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
            <a href="konten.php">📰 Konten Beranda</a>
            <a href="kelola_poli.php">🏥 Info Poli</a>
            <a href="kelola_pesan.php">✉️ Pesan Masuk</a>
            <a href="../booking.php" target="_blank">🌐 Buka Form Pasien</a>
            <a href="../logout.php">🚪 Keluar Dashboard</a>
        </div>

        <div class="col-md-9 col-lg-10 main-content">
            <div class="mb-4">
                <h2 class="fw-bold m-0 text-dark">Ubah Informasi Pendaftaran</h2>
                <p class="text-muted mb-0">Update status periksa atau data berkas milik pasien di bawah ini.</p>
            </div>

            <div class="card card-form p-5">
                <form method="POST">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap Pasien</label>
                            <input type="text" name="nama" class="form-control form-control-custom" value="<?= htmlspecialchars($data['nama']); ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Nomor Induk Kependudukan (NIK)</label>
                            <input type="text" name="nik" class="form-control form-control-custom" value="<?= htmlspecialchars($data['nik']); ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nomor WhatsApp / HP</label>
                            <input type="text" name="no_hp" class="form-control form-control-custom" value="<?= htmlspecialchars($data['no_hp']); ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tanggal Rencana Berobat</label>
                            <input type="date" name="tanggal" class="form-control form-control-custom" value="<?= $data['tanggal']; ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Poliklinik Tujuan</label>
                            <select name="poli" class="form-select form-control-custom" required>
                                <?php foreach ($daftar_poli as $p): ?>
                                    <option value="<?= htmlspecialchars($p) ?>" <?= $data['poli'] === $p ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p) ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php if (empty($daftar_poli)): ?>
                                    <option value="<?= htmlspecialchars($data['poli']) ?>" selected><?= htmlspecialchars($data['poli']) ?></option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status Penanganan Pasien</label>
                            <select name="status" class="form-select form-control-custom" style="font-weight: 600;" required>
                                <option value="menunggu" <?= $data['status'] == 'menunggu' ? 'selected' : ''; ?> style="color: #856404;">⏳ Menunggu Antrean</option>
                                <option value="selesai" <?= $data['status'] == 'selesai' ? 'selected' : ''; ?> style="color: #155724;">✅ Selesai Diperiksa</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Keluhan atau Gejala Sakit</label>
                            <textarea name="keluhan" class="form-control form-control-custom" rows="4" required><?= htmlspecialchars($data['keluhan']); ?></textarea>
                        </div>

                        <div class="col-12 d-flex justify-content-end align-items-center mt-5">
                            <a href="index.php" class="btn btn-light btn-custom-cancel text-muted me-2">Batal</a>
                            <button type="submit" name="update" class="btn btn-custom-save">💾 Simpan Perubahan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>