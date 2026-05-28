<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Poli Umum - SIMKES Krapyak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --cloud-blue: #cce2f5; --powder-sky: #a8cce6; --calm-ocean: #81afd8; --dusty-denim: #5F86A6; --midnight-blue: #243A5E; --white: #FFFFFF; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--white); color: var(--midnight-blue); line-height: 1.8; }
        .custom-navbar { background-color: var(--white) !important; padding: 15px 0; border-bottom: 2px solid var(--cloud-blue); }
        .about-header { background: linear-gradient(135deg, var(--cloud-blue) 0%, var(--white) 100%); padding: 80px 0 50px; text-align: center; }
        .card-about { border: none; border-radius: 30px; background: var(--white); box-shadow: 0 15px 40px rgba(36, 58, 94, 0.06); padding: 40px; margin-top: -30px; }
        .feature-list { list-style: none; padding-left: 0; }
        .feature-list li { padding: 12px 0; border-bottom: 1px solid var(--cloud-blue); display: flex; align-items: center; }
        .feature-list li::before { content: '🩺'; margin-right: 15px; }
        .btn-booking-now { background-color: var(--midnight-blue); color: white !important; border-radius: 50px; padding: 15px 40px; text-decoration: none; font-weight: 600; display: inline-block; margin-top: 30px; transition: 0.3s; }
        .btn-booking-now:hover { background-color: var(--dusty-denim); transform: translateY(-3px); }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light custom-navbar"><div class="container"><a class="navbar-brand fw-bold" href="index.php">SIMKES <span style="color: var(--calm-ocean);">KRAPYAK</span></a></div></nav>
<header class="about-header"><div class="container"><h2>KIA (Ibu & Anak)</h2><p class="text-muted">Mendampingi langkah awal generasi emas Krapyak.</p></div></header>
<div class="container mb-5"><div class="row justify-content-center"><div class="col-lg-8"><div class="card card-about">
<p>Pelayanan khusus yang penuh kasih sayang bagi ibu hamil dan balita untuk memastikan tumbuh kembang buah hati terpantau sempurna.</p>
<h5 class="mt-4 mb-3" style="font-weight: 700;">Layanan Unggulan:</h5>
<ul class="feature-list">
    <li><b>Pemeriksaan Kehamilan:</b> Pemantauan rutin (ANC) untuk kesehatan ibu dan janin.</li>
    <li><b>Imunisasi Dasar:</b> Layanan vaksinasi lengkap untuk memperkuat imun si kecil.</li>
    <li><b>Konsultasi KB:</b> Perencanaan keluarga yang aman, nyaman, dan teredukasi.</li>
</ul>
<div class="text-center"><a href="booking.php" class="btn-booking-now">Daftar Antrean KIA</a><br><a href="index.php" class="btn mt-3" style="background-color: var(--midnight-blue); color: white; border-radius: 12px; padding: 8px 20px; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: 0.3s;">← Kembali ke Beranda</a></div>
</div></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>