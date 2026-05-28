<?php
include '../config/koneksi.php';
session_start();

// Proteksi: Hanya admin yang boleh masuk
if (!isset($_SESSION['admin']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $dokter = mysqli_real_escape_string($conn, $_POST['dokter']);
    $poli   = mysqli_real_escape_string($conn, $_POST['poli']);
    $jadwal = mysqli_real_escape_string($conn, $_POST['jadwal']);

    $stmt = mysqli_prepare($conn, "INSERT INTO jadwal_dokter (nama_dokter, poli, waktu_praktik) VALUES (?, ?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'sss', $dokter, $poli, $jadwal);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            echo "<script>
                    alert('Data dokter baru berhasil ditambahkan!');
                    window.location.href='jadwal.php';
                  </script>";
            exit;
        }
        $errorMessage = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $errorMessage = mysqli_error($conn);
    }

    echo "<script>alert('Gagal menambahkan data: " . addslashes($errorMessage) . "');</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jadwal Dokter - SIMKES Krapyak</title>
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
                <h3 class="fw-bold mt-2 mb-0">Tambah Tenaga Medis Baru</h3>
            </div>

            <div class="card card-form">
                <form action="" method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Dokter / Bidan</label>
                        <input type="text" name="dokter" class="form-control" placeholder="Contoh: dr. Andi Wijaya" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Poli / Spesialis</label>
                        <input type="text" name="poli" class="form-control" placeholder="Contoh: Umum / Gigi / KIA" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Hari & Jam Praktik</label>
                        <input type="text" name="jadwal" class="form-control" placeholder="Contoh: Senin - Jumat (08:00 - 14:00)" required>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" name="simpan" class="btn btn-save py-2.5">
                            <i class="bi bi-plus-circle me-1"></i> Tambahkan ke Jadwal
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

</body>
</html>