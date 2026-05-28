<?php
session_start();
include '../config/koneksi.php';

// Kalau admin sudah login, langsung lempar ke dashboard utama
if (isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT * FROM admin WHERE username = ? LIMIT 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $username);
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
            if (password_verify($password, $stored) || $password === $stored) {
                $ok = true;
            }
        }

        if ($ok) {
            $_SESSION['admin'] = $data['username'];
            $_SESSION['role'] = 'admin';
            $_SESSION['nama_admin'] = isset($data['nama']) ? $data['nama'] : 'Admin Krapyak';

            echo "<script>alert('Login Berhasil! Selamat datang, Admin.'); window.location.href='index.php';</script>";
            exit;
        }
    }

    $error = "Username atau Password salah!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SIMKES Krapyak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* Palette Gradasi Biru Muda Cerah & Segar */
            --bg-gradient-start: #e0eafc;
            --bg-gradient-end: #cfdef3;
            --midnight-blue: #243A5E;
            --dusty-denim: #5F86A6;
            --soft-blue: #EDF4FA;
        }

        body {
            font-family: 'Poppins', sans-serif;
            /* Background Gradasi Biru Muda */
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
            position: relative;
            overflow: hidden;
        }

        /* Dekorasi Lingkaran Halus */
        body::before, body::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            background: rgba(36, 58, 94, 0.03);
            z-index: 1;
        }
        body::before { width: 300px; height: 300px; top: -50px; left: -50px; }
        body::after { width: 400px; height: 400px; bottom: -100px; right: -100px; }

        .card-login {
            border: none;
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(36, 58, 94, 0.08);
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            overflow: hidden;
            z-index: 2;
        }

        .login-header {
            background-color: var(--midnight-blue);
            color: #ffffff;
            padding: 35px 30px;
            text-align: center;
            position: relative;
        }

        .login-header h4 {
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 1px;
            font-size: 1.35rem;
        }

        .login-header p {
            font-size: 0.85rem;
            color: #CFE3F1;
            margin-bottom: 0;
            opacity: 0.9;
        }

        .form-body {
            padding: 40px 35px;
        }

        label {
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--dusty-denim);
            margin-bottom: 8px;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            border-radius: 12px;
            border: 2px solid var(--soft-blue);
            padding: 13px 16px;
            color: var(--midnight-blue);
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s ease;
            background-color: #fafbfc;
        }

        .form-control:focus {
            border-color: #8FB6D8;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(143, 182, 216, 0.15);
            outline: none;
        }

        .btn-login {
            background: var(--midnight-blue);
            color: #ffffff;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            transition: all 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 5px 15px rgba(36, 58, 94, 0.15);
        }

        .btn-login:hover {
            background: #1a2a44;
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(36, 58, 94, 0.25);
        }

        .btn-back {
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--dusty-denim);
            transition: color 0.3s;
        }

        .btn-back:hover {
            color: var(--midnight-blue);
        }

        .alert-custom {
            background-color: #fff1f1;
            border: 1px solid #fccfcf;
            color: #dc3545;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
            padding: 12px;
        }
    </style>
</head>
<body>

<div class="card-login">
    <div class="login-header">
        <h4>SIMKES KRAPYAK</h4>
        <p>Sistem Informasi Manajemen Kesehatan</p>
    </div>
    
    <form method="POST" class="form-body">
        <?php if (isset($error)) : ?>
            <div class="alert alert-custom text-center mb-4 shadow-sm">⚠️ <?= $error; ?></div>
        <?php endif; ?>

        <div class="mb-4">
            <label>Username Admin</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autocomplete="off">
        </div>
        
        <div class="mb-4">
            <label>Password Akun</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
        </div>
        
        <button type="submit" name="login" class="btn btn-login w-100">Masuk Dashboard</button>
        
        <div class="text-center mt-4">
            <a href="../index.php" class="btn-back">← Kembali ke Website Utama</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>