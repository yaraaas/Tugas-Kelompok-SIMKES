<?php
include '../config/koneksi.php';
session_start();

// Proteksi: Hanya admin yang boleh masuk
if (!isset($_SESSION['admin']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
    header("Location: login.php");
    exit;
}

// Validasi parameter ID di URL
if (!isset($_GET['id']) || empty($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: jadwal.php");
    exit;
}

$id = (int) $_GET['id'];

$columnResult = mysqli_query($conn, "SHOW COLUMNS FROM jadwal_dokter");
$columns = [];
if ($columnResult) {
    while ($col = mysqli_fetch_assoc($columnResult)) {
        $columns[] = $col['Field'];
    }
}
$hasColumn = fn($name) => in_array($name, $columns, true);
$timeField = $hasColumn('waktu_praktik') ? 'waktu_praktik' : ($hasColumn('jadwal') ? 'jadwal' : ($hasColumn('jam_praktik') ? 'jam_praktik' : ($hasColumn('waktu') ? 'waktu' : null)));
if (!$timeField) {
    // Jika kolom tidak ditemukan, coba tambahkan kolom baru `waktu_praktik` secara otomatis.
    // Ini membuat form edit dapat menyimpan jadwal tanpa memaksa user edit DB manual.
    $alterSql = "ALTER TABLE jadwal_dokter ADD COLUMN waktu_praktik VARCHAR(255) NOT NULL DEFAULT ''";
    if (@mysqli_query($conn, $alterSql)) {
        $timeField = 'waktu_praktik';
        $columns[] = 'waktu_praktik';
    } else {
        die('Kolom waktu/jadwal praktik tidak ditemukan pada tabel jadwal_dokter dan penambahan kolom gagal: ' . mysqli_error($conn));
    }
}

$stmt = mysqli_prepare($conn, "SELECT * FROM jadwal_dokter WHERE id = ?");
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
    header("Location: jadwal.php");
    exit;
}

// Proses Update data ketika tombol simpan diklik
if (isset($_POST['update'])) {
    $val_dokter = mysqli_real_escape_string($conn, $_POST['dokter_input'] ?? '');
    $val_poli   = mysqli_real_escape_string($conn, $_POST['poli_input'] ?? '');
    $val_jadwal = mysqli_real_escape_string($conn, $_POST['jadwal_input'] ?? '');

    $stmt = mysqli_prepare($conn, "UPDATE jadwal_dokter SET nama_dokter = ?, poli = ?, {$timeField} = ? WHERE id = ?");
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'sssi', $val_dokter, $val_poli, $val_jadwal, $id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            echo "<script>
                    alert('Jadwal dokter berhasil diperbarui!');
                    window.location.href='jadwal.php';
                  </script>";
            exit;
        }
        $errorMessage = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $errorMessage = mysqli_error($conn);
    }

    echo "<script>alert('Gagal memperbarui: " . addslashes($errorMessage) . "');</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jadwal Dokter - SIMKES Krapyak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --midnight-blue: #243A5E;
            --dusty-denim: #50799b;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F8FAFC;
            color: var(--midnight-blue);
        }
        .card-form {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(36, 58, 94, 0.05);
            background: white;
            padding: 30px;
        }
        .btn-save {
            background-color: var(--midnight-blue);
            color: white;
            font-weight: 600;
            border-radius: 10px;
            transition: 0.3s;
        }
        .btn-save:hover {
            background-color: var(--dusty-denim);
            color: white;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <div class="mb-4">
                <a href="jadwal.php" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Jadwal
                </a>
                <h3 class="fw-bold mt-2 mb-0">Edit Jadwal Praktik</h3>
            </div>

            <div class="card card-form">
                <form action="" method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Dokter / Bidan</label>
                        <input type="text" name="dokter_input" class="form-control" 
                               value="<?= htmlspecialchars($data['nama_dokter'] ?? ''); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Poli / Spesialis</label>
                        <input type="text" name="poli_input" class="form-control" 
                               value="<?= htmlspecialchars($data['poli'] ?? ''); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Hari & Jam Praktik</label>
                        <input type="text" name="jadwal_input" class="form-control" 
                                   value="<?= htmlspecialchars($data[$timeField] ?? ''); ?>" required>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" name="update" class="btn btn-save py-2.5">
                            <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

</body>
</html>