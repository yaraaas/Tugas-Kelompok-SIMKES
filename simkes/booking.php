<?php
date_default_timezone_set('Asia/Jakarta');
include 'config/koneksi.php';

// Ambil daftar poli unik dari tabel jadwal_dokter
$poli_result = mysqli_query($conn, "SELECT DISTINCT poli FROM jadwal_dokter WHERE poli IS NOT NULL AND poli != '' ORDER BY poli ASC");
$daftar_poli = [];
if ($poli_result) {
    while ($p = mysqli_fetch_assoc($poli_result)) {
        $daftar_poli[] = $p['poli'];
    }
}

$old = [
    'nama' => '',
    'nik' => '',
    'no_hp' => '',
    'tanggal' => '',
    'poli' => '',
    'keluhan' => ''
];
$error = '';

if (isset($_POST['booking'])) {
    $nama    = trim($_POST['nama'] ?? '');
    $nik     = trim($_POST['nik'] ?? '');
    $no_hp   = trim($_POST['no_hp'] ?? '');
    $tanggal = trim($_POST['tanggal'] ?? '');
    $poli    = trim($_POST['poli'] ?? '');
    $keluhan = trim($_POST['keluhan'] ?? '');

    $old = [
        'nama' => $nama,
        'nik' => $nik,
        'no_hp' => $no_hp,
        'tanggal' => $tanggal,
        'poli' => $poli,
        'keluhan' => $keluhan
    ];

    if ($nama === '' || $nik === '' || $no_hp === '' || $tanggal === '' || $poli === '' || $keluhan === '') {
        $error = 'Lengkapi semua data yang wajib diisi.';
    } elseif (!preg_match('/^[1-9][0-9]{15}$/', $nik)) {
        $error = 'NIK harus 16 digit dan tidak boleh diawali angka 0.';
    } else {
        $rawPhone = trim($_POST['no_hp'] ?? '');
        $cleanPhone = preg_replace('/\D+/', '', $rawPhone);
        if (strpos($cleanPhone, '62') === 0) {
            $cleanPhone = '0' . substr($cleanPhone, 2);
        }
        if (!preg_match('/^08[0-9]{8,11}$/', $cleanPhone)) {
            $error = 'Nomor HP harus dimulai dengan 08 atau +628 dan berisi 10-13 angka.';
        } else {
            $dateObj = DateTime::createFromFormat('Y-m-d', $tanggal);
            $today = new DateTime('today');
            if (!$dateObj || $dateObj->format('Y-m-d') !== $tanggal) {
                $error = 'Tanggal kunjungan tidak valid.';
            } elseif ($dateObj < $today) {
                $error = 'Tanggal kunjungan harus hari ini atau di masa mendatang.';
            } else {
                $tanggal_db = $dateObj->format('Y-m-d');
            }
        }
    }

    if ($error === '') {
        $scheduleCheck = mysqli_prepare($conn, "SELECT COUNT(*) AS available FROM jadwal_dokter WHERE poli = ?");
        if ($scheduleCheck) {
            mysqli_stmt_bind_param($scheduleCheck, 's', $poli);
            mysqli_stmt_execute($scheduleCheck);
            mysqli_stmt_bind_result($scheduleCheck, $available);
            mysqli_stmt_fetch($scheduleCheck);
            mysqli_stmt_close($scheduleCheck);
            if ((int) $available === 0) {
                $error = 'Pilih jadwal poli yang tersedia.';
            }
        } else {
            $error = 'Gagal memeriksa jadwal poli.';
        }
    }

    if ($error === '') {
        $no_hp = $cleanPhone;

        $stmt = mysqli_prepare($conn, "SELECT COALESCE(MAX(nomor_antrian), 0) AS max_no FROM booking WHERE tanggal = ? AND poli = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ss', $tanggal_db, $poli);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $max_no);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            $nomor_final = ((int) $max_no) + 1;

            $insert = mysqli_prepare($conn, "INSERT INTO booking (id_booking, nama, nik, no_hp, tanggal, poli, keluhan, nomor_antrian, status) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, 'menunggu')");
            if ($insert) {
                mysqli_stmt_bind_param($insert, 'ssssssi', $nama, $nik, $no_hp, $tanggal_db, $poli, $keluhan, $nomor_final);
                if (mysqli_stmt_execute($insert)) {
                    mysqli_stmt_close($insert);
                    echo "<script>
                            alert('Pendaftaran antrean berhasil! Nomor antrean Anda: $nomor_final');
                            window.location.href='tiket.php?nik=" . urlencode($nik) . "';
                          </script>";
                    exit;
                }
                $error = mysqli_stmt_error($insert);
                mysqli_stmt_close($insert);
            } else {
                $error = 'Gagal mempersiapkan kueri antrean.';
            }
        } else {
            $error = 'Gagal mempersiapkan query antrean.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran Antrean - SIMKES Krapyak</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Perkecil kalender Flatpickr */
        .flatpickr-calendar {
            width: 308px !important;
            font-size: 0.85rem !important;
        }
        .flatpickr-day {
            height: 36px !important;
            line-height: 36px !important;
            max-width: 36px !important;
            flex-basis: 14.2857% !important;
        }
        .flatpickr-months .flatpickr-month {
            height: 36px !important;
        }
        .flatpickr-current-month {
            font-size: 0.9rem !important;
            padding-top: 6px !important;
        }
        .flatpickr-weekday {
            font-size: 0.78rem !important;
            flex-basis: 14.2857% !important;
        }
        .flatpickr-prev-month, .flatpickr-next-month {
            padding: 6px !important;
        }
    </style>

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
            background: linear-gradient(135deg, var(--cloud-blue) 0%, var(--white) 100%);
            min-height: 100vh;
            color: var(--midnight-blue);
        }

        /* Navbar sesuai index */
        .custom-navbar {
            background-color: var(--white) !important;
            padding: 15px 0;
            border-bottom: 2px solid var(--cloud-blue);
        }

        .navbar-brand {
            color: var(--midnight-blue) !important;
            font-weight: 700;
        }

        /* Card Style */
        .card-booking {
            border: none;
            border-radius: 30px;
            background: var(--white);
            box-shadow: 0 15px 35px rgba(36, 58, 94, 0.08);
            margin-top: 50px;
            margin-bottom: 50px;
        }

        .card-header-custom {
            text-align: center;
            padding-bottom: 20px;
        }

        .card-header-custom h3 {
            font-weight: 700;
            color: var(--midnight-blue);
        }

        .card-header-custom p {
            font-size: 0.9rem;
            color: var(--dusty-denim);
        }

        /* Form Styling */
        label {
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 8px;
            color: var(--dusty-denim);
            margin-left: 5px;
        }

        .form-control {
            border-radius: 12px;
            border: 2px solid var(--cloud-blue);
            padding: 12px 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--calm-ocean);
            box-shadow: 0 0 0 0.25rem rgba(143, 182, 216, 0.25);
            outline: none;
        }

        /* Button Styling */
        .btn-booking {
            background-color: var(--midnight-blue);
            color: var(--white) !important;
            font-weight: 600;
            border-radius: 12px;
            padding: 14px;
            border: none;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .btn-booking:hover {
            background-color: var(--dusty-denim);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(36, 58, 94, 0.2);
        }

        .btn-back {
            color: var(--dusty-denim);
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 20px;
            transition: 0.3s;
        }

        .btn-back:hover {
            color: var(--midnight-blue);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light custom-navbar">
    <div class="container">
        <a class="navbar-brand" href="index.php">SIMKES <span style="color: var(--calm-ocean);">KRAPYAK</span></a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            <div class="card card-booking p-4 p-md-5">
                <a href="index.php" class="btn" style="background-color: var(--midnight-blue); color: white; border-radius: 12px; padding: 8px 20px; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: 0.3s; margin-top: 10px;">← Kembali ke Beranda</a>
                
                <div class="card-header-custom" style="margin-top: 20px;">
                    <h3>Pendaftaran Antrean</h3>
                    <p>Tak perlu antre lama, cukup pesan dari rumah untuk kenyamanan bersama.</p>
                </div>

                <form method="POST">
                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="Masukkan nama sesuai KTP" value="<?= htmlspecialchars($old['nama']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>NIK</label>
                        <input type="text" name="nik" class="form-control" placeholder="16 digit NIK" value="<?= htmlspecialchars($old['nik']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>No HP (WhatsApp)</label>
                        <input type="text" name="no_hp" class="form-control" placeholder="Contoh: 081234567890 atau +6281234567890" value="<?= htmlspecialchars($old['no_hp']) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Tanggal Kunjungan</label>
                            <input type="text" name="tanggal" id="tanggal" class="form-control" placeholder="Pilih tanggal..." value="<?= htmlspecialchars($old['tanggal']) ?>" autocomplete="off" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Pilih Poli</label>
                            <select name="poli" class="form-control" required>
                                <option value="" disabled <?= $old['poli'] === '' ? 'selected' : '' ?>>Pilih...</option>
                                <?php foreach ($daftar_poli as $p): ?>
                                    <option value="<?= htmlspecialchars($p) ?>" <?= $old['poli'] === $p ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($p) ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php if (empty($daftar_poli)): ?>
                                    <option disabled>Belum ada poli tersedia</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label>Keluhan Singkat</label>
                        <textarea name="keluhan" class="form-control" rows="3" placeholder="Ceritakan keluhan Anda..." required><?= htmlspecialchars($old['keluhan']) ?></textarea>
                    </div>

                    <button type="submit" name="booking" class="btn btn-booking w-100">
                        Daftar Antrean Sekarang
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
    flatpickr("#tanggal", {
        locale: "id",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "j F Y",
        minDate: "today",
        disableMobile: true
    });
</script>

</body>
</html>