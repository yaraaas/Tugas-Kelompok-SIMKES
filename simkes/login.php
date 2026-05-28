<?php
include 'config/koneksi.php';
session_start();

// Jika sudah login (baik admin atau pasien), langsung alihkan ke index.php
if (isset($_SESSION['role'])) {
    header('Location: index.php');
    exit;
}

if (isset($_POST['login'])) {
    $username_input = trim($_POST['username'] ?? '');
    $password_input = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ? OR nik = ? LIMIT 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ss', $username_input, $username_input);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    } else {
        $data = false;
    }

    if ($data) {
        $stored = isset($data['password']) ? $data['password'] : '';
        $ok = false;
        if ($stored !== '') {
            if (password_verify($password_input, $stored) || $password_input === $stored) {
                $ok = true;
            }
        }

        if ($ok) {
            if ($data['role'] == 'admin' || $data['role'] == 'pasien') {
                $_SESSION['id_user']  = $data['id_user'];
                $_SESSION['nama']     = $data['nama'];
                $_SESSION['nik']      = $data['nik'];
                $_SESSION['username'] = $data['username'];
                $_SESSION['role']     = $data['role'];

                if ($_SESSION['role'] == 'admin') {
                    echo "<script>alert('Selamat Datang Admin!'); window.location.href='index.php';</script>";
                } else {
                    echo "<script>alert('Login Pasien Berhasil!'); window.location.href='index.php';</script>";
                }
                exit;
            }
            $error = "Akses ditolak untuk akun ini.";
        } else {
            $error = "Username/NIK atau Password Anda salah!";
        }
    } else {
        $error = "Username/NIK atau Password Anda salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMKES Krapyak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #cce2f5 0%, #FFFFFF 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-login {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-login p-4">
                <div class="text-center mb-4">
                    <h3 class="fw-bold" style="color: #243A5E;">SIMKES <span style="color: #81afd8;">KRAPYAK</span></h3>
                    <p class="text-muted small">Silakan masuk menggunakan akun Anda</p>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger p-2 small text-center"><?= $error; ?></div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Username atau NIK</label>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan Username / NIK" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <button type="submit" name="login" class="btn text-white w-100 py-2 rounded-pill mt-3" style="background-color: #243A5E; font-weight: 600;">Masuk</button>
                </form>
                
                <div class="text-center mt-3">
                    <a href="index.php" class="small text-muted text-decoration-none">← Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>