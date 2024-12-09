<?php
// Memasukkan file koneksi database
include 'connect.php'; // pastikan path-nya benar

// Memeriksa apakah data telah dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $masalah = $_POST['masalah'];
    $masukan = $_POST['masukan'];

    try {
        // Query untuk memasukkan data ke dalam tabel 'keluhan' menggunakan PDO
        $sql = "INSERT INTO keluhan (nama, masalah, masukan) VALUES (:nama, :masalah, :masukan)";
        $stmt = $conn->prepare($sql);
        
        // Mengikat parameter untuk mencegah SQL injection
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':masalah', $masalah);
        $stmt->bindParam(':masukan', $masukan);
        
        // Menjalankan query
        $stmt->execute();
        
        echo "<p style='color: green;'>Keluhan Anda berhasil dikirim.</p>";
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Terjadi kesalahan: " . $e->getMessage() . "</p>";
    }
}

// Menutup koneksi (PDO tidak membutuhkan penutupan koneksi eksplisit seperti MySQLi)
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Keluhan</title>
    <style>
/* CSS untuk memperindah tampilan */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f5f7fa;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    flex-direction: column;
}

.container {
    background-color: #ffffff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 600px;
    text-align: center;
}

h2 {
    color: #333;
    margin-bottom: 15px;
    font-size: 26px;
}

label {
    font-size: 14px;
    color: #555;
    margin-bottom: 8px;
    display: block;
    text-align: left;
}

input[type="text"],
textarea {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.3s, box-shadow 0.3s;
}

input[type="text"]:focus,
textarea:focus {
    border-color: #4CAF50;
    box-shadow: 0 0 8px rgba(76, 175, 80, 0.2);
    outline: none;
}

textarea {
    resize: vertical;
    height: 120px;
}

input[type="submit"] {
    background-color: #4CAF50;
    color: white;
    padding: 14px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    width: 100%;
    transition: background-color 0.3s ease, box-shadow 0.3s;
}

input[type="submit"]:hover {
    background-color: #45a049;
    box-shadow: 0 4px 8px rgba(76, 175, 80, 0.3);
}

.form-group {
    margin-bottom: 15px;
    text-align: left;
}

.back-button {
    display: inline-block;
    background-color: #f44336;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    text-decoration: none;
    margin-top: 20px;
    width: 100%;
    text-align: center;
    box-sizing: border-box;
    transition: all 0.3s ease-in-out;
}

.back-button:hover {
    background-color: #d32f2f;
    box-shadow: 0 4px 8px rgba(211, 47, 47, 0.3);
    transform: translateY(-2px);
}

.back-button:active {
    background-color: #c62828;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transform: translateY(0);
}

/* Responsiveness for smaller screens */
@media (max-width: 768px) {
    .container {
        padding: 20px;
    }

    h2 {
        font-size: 22px;
    }
}

    </style>
</head>
<body>

    <div class="container">
        <h2>Kirimkan Keluhan Anda</h2>
        <!-- Formulir untuk mengirim data ke halaman ini -->
        <form action="" method="post">
            <div class="form-group">
                <label for="nama">Nama:</label>
                <input type="text" id="nama" name="nama" required>
            </div>

            <div class="form-group">
                <label for="masalah">Masalah:</label>
                <textarea id="masalah" name="masalah" required></textarea>
            </div>

            <div class="form-group">
                <label for="masukan">Masukan:</label>
                <textarea id="masukan" name="masukan" required></textarea>
            </div>

            <input type="submit" value="Kirim Keluhan">
        </form>

        <!-- Button to go back to the user dashboard -->
        <a href="user_dashboard.php" class="back-button">Kembali ke Dashboard</a>
    </div>

</body>
</html>
