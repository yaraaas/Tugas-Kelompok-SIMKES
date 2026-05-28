<?php
session_start();
session_unset();
session_destroy();
echo "<script>alert('Anda telah keluar sistem.'); window.location.href='admin/login.php';</script>";
exit;
?>