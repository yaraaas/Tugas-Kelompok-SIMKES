<?php
include 'config/koneksi.php';

// Ambil NIK dari URL
if (!isset($_GET['nik']) || empty($_GET['nik'])) {
    header('Location: booking.php');
    exit;
}

$nik = trim($_GET['nik']);
$stmt = mysqli_prepare($conn, "SELECT * FROM booking WHERE nik = ? ORDER BY id_booking DESC LIMIT 1");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 's', $nik);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} else {
    $data = false;
}

if (!$data) {
    header('Location: booking.php');
    exit;
}

function formatTanggalIndonesia($tanggal)
{
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
        12 => 'Desember',
    ];
    $ts = strtotime($tanggal);
    return date('d', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tiket Antrean - SIMKES Krapyak</title>

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
            background: linear-gradient(135deg, var(--cloud-blue) 0%, var(--white) 100%);
            min-height: 100vh;
            color: var(--midnight-blue);
        }

        .custom-navbar {
            background-color: var(--white) !important;
            padding: 15px 0;
            border-bottom: 2px solid var(--cloud-blue);
        }

        .navbar-brand {
            color: var(--midnight-blue) !important;
            font-weight: 700;
        }

        /* Card Tiket */
        .card-tiket {
            border: none;
            border-radius: 30px;
            background: var(--white);
            box-shadow: 0 15px 35px rgba(36, 58, 94, 0.08);
            margin-top: 50px;
            margin-bottom: 50px;
            overflow: hidden;
        }

        /* Header tiket */
        .tiket-header {
            background: var(--midnight-blue);
            color: var(--white);
            text-align: center;
            padding: 30px 20px 20px;
        }

        .tiket-header .label-kecil {
            font-size: 0.75rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            opacity: 0.7;
            margin-bottom: 5px;
        }

        .tiket-header h5 {
            font-weight: 700;
            font-size: 1.1rem;
            margin: 0;
        }

        /* Nomor Antrean besar */
        .nomor-antrean-box {
            background: var(--calm-ocean);
            text-align: center;
            padding: 25px 20px;
        }

        .nomor-antrean-box .label {
            font-size: 0.8rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--midnight-blue);
            font-weight: 600;
            opacity: 0.8;
        }

        .nomor-antrean-box .angka {
            font-size: 5rem;
            font-weight: 700;
            color: var(--midnight-blue);
            line-height: 1;
        }

        /* Garis putus tiket */
        .tiket-divider {
            display: flex;
            align-items: center;
            padding: 0 20px;
            margin: 0;
        }

        .tiket-divider::before,
        .tiket-divider::after {
            content: '';
            flex: 1;
            border-top: 2px dashed var(--cloud-blue);
        }

        .tiket-divider .circle {
            width: 20px;
            height: 20px;
            background: var(--cloud-blue);
            border-radius: 50%;
            margin: 0 10px;
            flex-shrink: 0;
        }

        /* Detail info */
        .tiket-body {
            padding: 25px 30px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--cloud-blue);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-row .info-label {
            font-size: 0.78rem;
            color: var(--dusty-denim);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-row .info-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--midnight-blue);
            text-align: right;
        }

        /* Badge status */
        .badge-status {
            background-color: var(--powder-sky);
            color: var(--midnight-blue);
            font-size: 0.78rem;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 20px;
            text-transform: capitalize;
        }

        /* Footer tiket */
        .tiket-footer {
            background: var(--cloud-blue);
            text-align: center;
            padding: 18px 20px;
            font-size: 0.78rem;
            color: var(--dusty-denim);
        }

        /* Tombol */
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

        .btn-booking {
            background-color: var(--midnight-blue);
            color: var(--white) !important;
            font-weight: 600;
            border-radius: 18px;
            padding: 14px 24px;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            letter-spacing: 0.02em;
        }

        .btn-full {
            width: 100%;
            min-width: 260px;
        }

        .btn-half {
            width: 50%;
            min-width: 140px;
            max-width: 220px;
        }

        .btn-booking:hover {
            background-color: #1f4169;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(36, 58, 94, 0.25);
        }

        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .card-tiket { box-shadow: none; border: 1px solid #ccc; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light custom-navbar no-print">
    <div class="container">
        <a class="navbar-brand" href="index.php">SIMKES <span style="color: var(--calm-ocean);">KRAPYAK</span></a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <!-- KARTU TIKET -->
            <div class="card-tiket">

                <!-- Header -->
                <div class="tiket-header">
                    <p class="label-kecil">Tiket Antrean Resmi</p>
                    <h5>SIMKES KRAPYAK</h5>
                    <p style="font-size:0.8rem; opacity:0.6; margin-top:5px; margin-bottom:0;">
                        Melayani dengan sepenuh hati
                    </p>
                </div>

                <!-- Nomor Antrean -->
                <div class="nomor-antrean-box">
                    <p class="label">Nomor Antrean</p>
                    <div class="angka"><?= str_pad($data['nomor_antrian'], 3, '0', STR_PAD_LEFT) ?></div>
                    <p style="font-size:0.8rem; color:var(--midnight-blue); margin:5px 0 0; font-weight:600;">
                        <?= htmlspecialchars($data['poli']) ?>
                    </p>
                </div>

                <!-- Garis putus -->
                <div class="tiket-divider">
                    <div class="circle"></div>
                </div>

                <!-- Detail Info -->
                <div class="tiket-body">
                    <div class="info-row">
                        <span class="info-label">Nama</span>
                        <span class="info-value"><?= htmlspecialchars($data['nama']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">NIK</span>
                        <span class="info-value"><?= htmlspecialchars($data['nik']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">No. HP</span>
                        <span class="info-value"><?= htmlspecialchars($data['no_hp']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tanggal</span>
                        <span class="info-value">
                            <?= formatTanggalIndonesia($data['tanggal']) ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Poli</span>
                        <span class="info-value"><?= htmlspecialchars($data['poli']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Keluhan</span>
                        <span class="info-value" style="max-width:180px;">
                            <?= htmlspecialchars($data['keluhan']) ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="badge-status"><?= htmlspecialchars($data['status']) ?></span>
                    </div>
                </div>

                <!-- Footer tiket -->
                <div class="tiket-footer">
                    📍 Harap tiba 15 menit sebelum jadwal. <br>
                    Tunjukkan tiket ini kepada petugas.
                </div>

            </div>
            <!-- END KARTU TIKET -->

            <!-- Tombol aksi -->
            <div class="d-flex flex-column align-items-center gap-2 mb-5 no-print">
                <button onclick="window.print()" class="btn-booking btn-full">
                    🖨️ Cetak / Simpan Tiket
                </button>
                <a href="index.php" class="btn-booking btn-back btn-half">
                    Kembali ke Beranda
                </a>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>