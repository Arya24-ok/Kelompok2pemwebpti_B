<?php
require 'connect.php'; // Koneksi ke database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi sederhana
    if (strlen($password) < 8) {
        $error = "Password harus minimal 8 karakter!";
    } elseif ($password !== $confirm_password) {
        $error = "Password dan Konfirmasi Password tidak cocok!";
    } else {
        // Periksa apakah username sudah ada
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error = "Username sudah terdaftar!";
        } else {
            // Masukkan pengguna baru
            $hashed_password = md5($password); // Hash password dengan MD5
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, 'user')");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->execute();

            header("Location: login.php"); // Redirect langsung ke halaman login
            exit(); // Pastikan script berhenti setelah redirect
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logo.png" type="image/x-icon">
    <title>Register</title>
    <style>
    body {
        font-family: sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background-color: #f4f4f4;
        background-image: url(background.jpg);
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    form {
        background-color: #fff;
        padding: 30px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        width: 300px;
    }

    label {
        display: block;
        margin-bottom: 5px;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 3px;
        box-sizing: border-box;
    }

    button[type="submit"] {
        background-color: #4CAF50;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        width: 100%;
    }

    button[type="submit"]:hover {
        background-color: #45a049;
    }

    a {
        color: blue;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    .register-link {
        margin-top: 15px;
        text-align: center;
    }

    p {
        color: red;
        text-align: center;
        font-size: 14px;
    }
    </style>
</head>
<body>
    <form method="POST" action="register.php">
        <h2>Daftar Akun</h2> <!-- Title for the registration page -->
        
        <?php if (isset($error)) echo "<p>$error</p>"; ?>
        <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

        <label>Username:</label><br>
        <input type="text" name="username" required><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br>

        <label>Konfirmasi Password:</label><br>
        <input type="password" name="confirm_password" required><br><br>

        <button type="submit">Register</button><br>

        <p class="register-link">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </form>
</body>
</html>
