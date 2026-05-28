<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include '../config/koneksi.php';

// Auto-create tabel poli_info
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS poli_info (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nama_poli   VARCHAR(100) NOT NULL UNIQUE,
    deskripsi   TEXT,
    layanan_1   VARCHAR(255),
    layanan_2   VARCHAR(255),
    layanan_3   VARCHAR(255)
)");

// Ambil daftar poli dari jadwal_dokter
$poli_result = mysqli_query($conn, "SELECT DISTINCT poli FROM jadwal_dokter WHERE poli IS NOT NULL AND poli != '' ORDER BY poli ASC");
$daftar_poli = [];
if ($poli_result) {
    while ($p = mysqli_fetch_assoc($poli_result)) {
        $daftar_poli[] = $p['poli'];
    }
}

// Proses simpan
$sukses = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama_poli'])) {
    $np  = trim($_POST['nama_poli'] ?? '');
    $des = trim($_POST['deskripsi'] ?? '');
    $l1  = trim($_POST['layanan_1'] ?? '');
    $l2  = trim($_POST['layanan_2'] ?? '');
    $l3  = trim($_POST['layanan_3'] ?? '');

    $stmt = mysqli_prepare($conn, "INSERT INTO poli_info (nama_poli, deskripsi, layanan_1, layanan_2, layanan_3)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE deskripsi=VALUES(deskripsi), layanan_1=VALUES(layanan_1), layanan_2=VALUES(layanan_2), layanan_3=VALUES(layanan_3)");
    mysqli_stmt_bind_param($stmt, 'sssss', $np, $des, $l1, $l2, $l3);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $sukses = "Info poli <b>$np</b> berhasil disimpan!";
}

// Ambil semua info poli yang sudah diisi
$info_map = [];
$all_info = mysqli_query($conn, "SELECT * FROM poli_info");
while ($r = mysqli_fetch_assoc($all_info)) {
    $info_map[$r['nama_poli']] = $r;
}

// Poli yang sedang diedit
$edit_poli = $_GET['edit'] ?? ($daftar_poli[0] ?? '');
$edit_data = $info_map[$edit_poli] ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Info Poli - SIMKES Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --cloud-blue:#EDF4FA; --powder-sky:#CFE3F1; --calm-ocean:#8FB6D8; --dusty-denim:#5F86A6; --midnight-blue:#243A5E; --white:#FFFFFF; }
        body { font-family:'Poppins',sans-serif; background-color:#f4f7fa; color:var(--midnight-blue); }
        .sidebar { background-color:var(--midnight-blue); min-height:100vh; padding-top:30px; }
        .sidebar h4 { font-weight:700; }
        .sidebar a { color:var(--powder-sky); text-decoration:none; display:block; padding:14px 25px; font-weight:600; transition:0.3s; }
        .sidebar a:hover, .sidebar a.active { color:var(--white); background-color:rgba(255,255,255,0.05); border-left:5px solid var(--calm-ocean); }
        .main-content { padding:40px; }
        .card-form { border:none; border-radius:20px; background:var(--white); box-shadow:0 10px 30px rgba(36,58,94,0.05); padding:30px; }
        .poli-tab { cursor:pointer; padding:10px 18px; border-radius:10px; font-weight:600; font-size:0.9rem; color:var(--dusty-denim); transition:0.2s; border:2px solid transparent; }
        .poli-tab:hover, .poli-tab.active { background:var(--midnight-blue); color:white; }
        .form-control { border-radius:10px; border:2px solid var(--cloud-blue); padding:10px 15px; }
        .form-control:focus { border-color:var(--calm-ocean); box-shadow:none; }
        .btn-save { background:var(--midnight-blue); color:white; border:none; border-radius:10px; padding:10px 30px; font-weight:600; transition:0.2s; }
        .btn-save:hover { background:#1a2a44; color:white; }
        .badge-filled { background:var(--cloud-blue); color:var(--midnight-blue); font-size:0.75rem; padding:3px 10px; border-radius:20px; font-weight:600; }
        .badge-empty { background:#fff3cd; color:#856404; font-size:0.75rem; padding:3px 10px; border-radius:20px; font-weight:600; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 sidebar px-0">
            <h4 class="text-center mb-4 text-white px-3">SIMKES <span style="color:var(--calm-ocean);">ADMIN</span></h4>
            <hr style="border-color:rgba(255,255,255,0.1)">
            <a href="index.php">📋 Data Booking</a>
            <a href="jadwal.php">👨‍⚕️ Jadwal Dokter</a>
            <a href="kelola_poli.php" class="active">🏥 Info Poli</a>
            <a href="kelola_pesan.php">✉️ Pesan Masuk</a>
            <a href="../logout.php">🚪 Keluar Dashboard</a>
        </div>

        <div class="col-md-9 col-lg-10 main-content">
            <h2 class="fw-bold mb-1">Info Poli</h2>
            <p class="text-muted mb-4">Isi deskripsi dan layanan unggulan untuk setiap poli yang tampil di halaman user.</p>

            <?php if ($sukses): ?>
            <div class="alert alert-success rounded-3 mb-4"><?= $sukses ?></div>
            <?php endif; ?>

            <?php if (empty($daftar_poli)): ?>
            <div class="alert alert-warning rounded-3">Belum ada poli di jadwal dokter. Tambah jadwal dokter terlebih dahulu.</div>
            <?php else: ?>

            <!-- Tab pilih poli -->
            <div class="d-flex flex-wrap gap-2 mb-4">
                <?php foreach ($daftar_poli as $p): ?>
                <a href="kelola_poli.php?edit=<?= urlencode($p) ?>" class="poli-tab <?= $p === $edit_poli ? 'active' : '' ?>">
                    <?= htmlspecialchars($p) ?>
                    <?php if (isset($info_map[$p])): ?>
                        <span class="badge-filled ms-1">✓</span>
                    <?php else: ?>
                        <span class="badge-empty ms-1">Kosong</span>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
            </div>

            <!-- Form edit -->
            <div class="card-form">
                <h5 class="fw-bold mb-4">Edit Info: <?= htmlspecialchars($edit_poli) ?></h5>
                <form method="POST">
                    <input type="hidden" name="nama_poli" value="<?= htmlspecialchars($edit_poli) ?>">
                    <div class="mb-3">
                        <label class="fw-bold small mb-1">Deskripsi Singkat</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Contoh: Layanan kesehatan primer untuk seluruh warga..."><?= htmlspecialchars($edit_data['deskripsi'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small mb-1">Layanan Unggulan 1</label>
                        <input type="text" name="layanan_1" class="form-control" placeholder="Contoh: Pemeriksaan Fisik: Tensi, suhu tubuh..." value="<?= htmlspecialchars($edit_data['layanan_1'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small mb-1">Layanan Unggulan 2</label>
                        <input type="text" name="layanan_2" class="form-control" placeholder="Contoh: Pengobatan Umum: Flu, batuk, demam..." value="<?= htmlspecialchars($edit_data['layanan_2'] ?? '') ?>">
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold small mb-1">Layanan Unggulan 3</label>
                        <input type="text" name="layanan_3" class="form-control" placeholder="Contoh: Surat Keterangan Sehat..." value="<?= htmlspecialchars($edit_data['layanan_3'] ?? '') ?>">
                    </div>
                    <button type="submit" class="btn-save">💾 Simpan Info Poli</button>
                    <a href="../layanan.php?poli=<?= urlencode($edit_poli) ?>&from=admin" target="_blank" class="btn btn-outline-secondary ms-2" style="border-radius:10px;padding:10px 20px;font-weight:600;">👁️ Preview</a>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
