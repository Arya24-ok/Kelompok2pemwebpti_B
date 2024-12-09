<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'connect.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // File upload handling
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        if (in_array($fileExt, $allowedExtensions)) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
            }

            $newFileName = uniqid() . '.' . $fileExt;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Insert into database
                $sql = "INSERT INTO products (name, price, stock, image) VALUES (:name, :price, :stock, :image)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':stock', $stock);
                $stmt->bindParam(':image', $destPath);

                if ($stmt->execute()) {
                    $successMessage = "Produk berhasil ditambahkan.";
                } else {
                    $errorMessage = "Terjadi kesalahan saat menyimpan data.";
                }
            } else {
                $errorMessage = "Gagal mengupload file gambar.";
            }
        } else {
            $errorMessage = "Format file tidak didukung. Hanya JPG dan PNG yang diperbolehkan.";
        }
    } else {
        $errorMessage = "Harap pilih file gambar.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <style>
        /* Styles for the page */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
        }

        header {
            background-color: #4CAF50;
            padding: 20px;
            color: white;
            text-align: center;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 10px;
        }

        input[type="text"], input[type="number"], input[type="file"] {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-top: 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        a {
            color: #4CAF50;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<header>
    <h1>Tambah Produk</h1>
</header>

<div class="container">
    <?php if (!empty($successMessage)) : ?>
        <div class="message success">
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php elseif (!empty($errorMessage)) : ?>
        <div class="message error">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="name">Nama Produk</label>
        <input type="text" id="name" name="name" required>

        <label for="price">Harga Produk</label>
        <input type="number" id="price" name="price" required>

        <label for="stock">Stok Produk</label>
        <input type="number" id="stock" name="stock" required>

        <label for="image">Gambar Produk (JPG atau PNG)</label>
        <input type="file" id="image" name="image" accept=".jpg, .jpeg, .png" required>

        <button type="submit">Tambah Produk</button>
    </form>

    <p><a href="admin_dashboard.php">Kembali ke Daftar Produk</a></p>
</div>

</body>
</html>
