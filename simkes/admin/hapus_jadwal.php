<?php
include '../config/koneksi.php';
session_start();

// Proteksi: Hanya admin yang boleh masuk
if (!isset($_SESSION['admin']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
    header("Location: login.php");
    exit;
}

// Cek apakah parameter ID dikirimkan
if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $id = (int) $_GET['id'];
    $redirectPage = 'jadwal.php';
    $delete_stmt = mysqli_prepare($conn, "DELETE FROM jadwal_dokter WHERE id = ?");
    if ($delete_stmt) {
        mysqli_stmt_bind_param($delete_stmt, 'i', $id);
        if (mysqli_stmt_execute($delete_stmt)) {
            mysqli_stmt_close($delete_stmt);
            echo "<script>
                    alert('Jadwal dokter berhasil dihapus!');
                    window.location.href='{$redirectPage}';
                  </script>";
            exit;
        }
        $error = mysqli_stmt_error($delete_stmt);
        mysqli_stmt_close($delete_stmt);
    } else {
        $error = mysqli_error($conn);
    }

    echo "<script>
            alert('Gagal menghapus data: " . addslashes($error) . "');
            window.location.href='{$redirectPage}';
          </script>";
    exit;
} else {
    header("Location: jadwal.php");
    exit;
}
?>