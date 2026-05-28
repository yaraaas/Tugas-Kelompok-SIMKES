<?php
// Skrip debug untuk memeriksa tabel admin (LOCAL ONLY)
if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    die('Akses ditolak. Jalankan skrip ini secara lokal.');
}

include 'config/koneksi.php';

$test = isset($_GET['test']) ? $_GET['test'] : null; // password yang ingin diuji

echo "<h3>Debug tabel admin - database simkes_desa</h3>";

$res = mysqli_query($conn, "SELECT * FROM admin");
if (!$res) {
    die('Query gagal: ' . mysqli_error($conn));
}

echo '<table border="1" cellpadding="6" cellspacing="0">';
echo '<tr><th>username</th><th>nama</th><th>password_hash</th><th>verifikasi'</th></tr>';
while ($row = mysqli_fetch_assoc($res)) {
    $username = htmlspecialchars($row['username']);
    $nama = isset($row['nama']) ? htmlspecialchars($row['nama']) : '';
    $hash = isset($row['password']) ? $row['password'] : '';
    $verified = 'N/A';
    if ($test !== null && $hash !== '') {
        $verified = password_verify($test, $hash) ? 'MATCH' : 'NO';
    }
    echo '<tr>';
    echo '<td>' . $username . '</td>';
    echo '<td>' . $nama . '</td>';
    echo '<td style="max-width:500px;word-break:break-all">' . htmlspecialchars($hash) . '</td>';
    echo '<td>' . $verified . '</td>';
    echo '</tr>';
}
echo '</table>';

echo '<p>Untuk menguji password tertentu, tambahkan parameter <code>?test=kata_sandi</code> di URL.</p>';
echo '<p>Contoh: <a href="debug_admin.php?test=admin123">debug_admin.php?test=admin123</a></p>';

?>
