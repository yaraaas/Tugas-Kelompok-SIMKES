<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tentang SIMKES - Desa Krapyak</title>

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

        /* Navbar Konsisten */
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
        .about-header {
            background: linear-gradient(135deg, var(--cloud-blue) 0%, var(--white) 100%);
            padding: 80px 0 50px;
            text-align: center;
        }

        .about-header h2 {
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        /* Content Card */
        .content-section {
            margin-top: -30px;
            padding-bottom: 80px;
        }

        .card-about {
            border: none;
            border-radius: 30px;
            background: var(--white);
            box-shadow: 0 15px 40px rgba(36, 58, 94, 0.06);
            padding: 40px;
        }

        .highlight-text {
            color: var(--dusty-denim);
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        /* List Styling */
        .feature-list {
            list-style: none;
            padding-left: 0;
        }

        .feature-list li {
            padding: 12px 0;
            border-bottom: 1px solid var(--cloud-blue);
            display: flex;
            align-items: center;
        }

        .feature-list li::before {
            content: '✓';
            display: inline-block;
            width: 25px;
            height: 25px;
            background-color: var(--powder-sky);
            color: var(--midnight-blue);
            border-radius: 50%;
            text-align: center;
            line-height: 25px;
            font-size: 12px;
            font-weight: bold;
            margin-right: 15px;
        }

        .btn-back-home {
            background-color: var(--midnight-blue);
            color: white !important;
            border-radius: 50px;
            padding: 12px 30px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            margin-top: 30px;
            transition: 0.3s;
        }

        .btn-back-home:hover {
            background-color: var(--dusty-denim);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light custom-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">SIMKES <span style="color: var(--calm-ocean);">KRAPYAK</span></a>
    </div>
</nav>

<header class="about-header">
    <div class="container">
        <h2>Tentang Kami</h2>
        <p class="text-muted">Dedikasi untuk senyum sehat warga Desa Krapyak.</p>
    </div>
</header>

<div class="container content-section">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-about">
                <p class="highlight-text">
                    <b>SIMKES Desa Krapyak</b> hadir sebagai wujud cinta dan kepedulian kami terhadap kesehatan seluruh lapisan masyarakat. Kami percaya bahwa pelayanan kesehatan yang berkualitas harus bisa diakses dengan mudah, nyaman, dan cepat oleh siapa pun.
                </p>
                
                <p>
                    Melalui sistem ini, kami berupaya menghapus jarak dan waktu antrean yang panjang. Kini, warga Krapyak dapat merencanakan kunjungan kesehatan keluarga hanya dalam beberapa klik dari rumah, sehingga waktu berharga Anda bisa lebih banyak dihabiskan bersama orang tercinta.
                </p>

                <h5 class="mt-4 mb-3" style="font-weight: 700; color: var(--midnight-blue);">Kemudahan untuk Anda:</h5>
                <ul class="feature-list">
                    <li><b>Daftar Antrean Online: </b> Pesan nomor urut tanpa harus datang subuh.</li>
                    <li><b>Data Pasien Terintegrasi: </b>  Pelayanan lebih tepat sasaran dengan data digital.</li>
                    <li><b>Informasi Jadwal Dokter: </b>  Cek waktu praktik dokter kapan saja.</li>
                    <li><b>Pantau Riwayat Kesehatan:</b>  Catatan kesehatan keluarga tersimpan aman.</li>
                </ul>

                <div class="text-center">
                    <a href="index.php" class="btn-back-home">Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="text-center py-4 mt-5" style="background-color: var(--cloud-blue); color: var(--dusty-denim); font-size: 0.8rem;">
    © 2026 SIMKES Desa Krapyak • Tulus Melayani, Sehat Bersama.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>