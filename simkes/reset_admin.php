<?php
// Skrip reset password admin sementara
// Keamanan: hanya boleh diakses dari localhost
if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    die('Akses ditolak. Jalankan skrip ini secara lokal.');
}

include 'config/koneksi.php';

$username = isset($_GET['user']) ? $_GET['user'] : 'admin';
$newpass = isset($_GET['pass']) ? $_GET['pass'] : 'AdminBaru123';

$esc_user = mysqli_real_escape_string($conn, $username);
$hash = password_hash($newpass, PASSWORD_DEFAULT);

$update = mysqli_query($conn, "UPDATE admin SET password = '$hash' WHERE username = '$esc_user'");

if ($update) {
    if (mysqli_affected_rows($conn) > 0) {
        echo "Password berhasil diubah untuk user: <strong>$username</strong>.<br>";
        echo "Password baru: <strong>$newpass</strong>\n";
    } else {
        // Jika tidak ada baris terupdate, coba buat akun baru
        $insert = mysqli_query($conn, "INSERT INTO admin (username, password, nama) VALUES ('$esc_user', '$hash', 'Admin')");
        if ($insert) {
            echo "Akun baru dibuat: <strong>$username</strong> dengan password: <strong>$newpass</strong>.<br>";
        } else {
            echo 'Tidak ada perubahan. Error: ' . mysqli_error($conn);
        }
    }
} else {
    echo 'Query gagal: ' . mysqli_error($conn);
}

echo '<hr>Setelah berhasil, HAPUS file ini dari server untuk keamanan.';

?>
