<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data produk berdasarkan ID
    $sql = "SELECT * FROM products WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "Produk tidak ditemukan.";
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_POST['existing_image']; // Gambar lama jika tidak diunggah gambar baru

    // Periksa apakah ada file yang diunggah
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/"; // Folder untuk menyimpan gambar
        $targetFile = $targetDir . basename($_FILES['image']['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Validasi tipe file
        $allowedTypes = ['jpg', 'jpeg', 'png'];
        if (!in_array($imageFileType, $allowedTypes)) {
            echo "Hanya file JPG, JPEG, dan PNG yang diperbolehkan.";
            exit;
        }

        // Pindahkan file ke folder target
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = $targetFile; // Update jalur gambar
        } else {
            echo "Gagal mengunggah gambar.";
            exit;
        }
    }

    // Update data produk
    $sql = "UPDATE products SET name = :name, price = :price, stock = :stock, image = :image WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':stock', $stock);
    $stmt->bindParam(':image', $image);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        header("Location: admin.php");
        exit;
    } else {
        echo "Gagal mengupdate produk.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logo.png" type="image/x-icon">
    <title>Edit Produk</title>
    <style>
        /* Reset default styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 600px;
        }

        h1 {
            text-align: center;
            font-size: 2rem;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        label {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            color: #555;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            color: #333;
            outline: none;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="file"]:focus {
            border-color: #4CAF50;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        .back-button {
            background-color: #f44336;
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #e53935;
        }

        .image-preview {
            max-width: 200px;
            margin-top: 20px;
        }

        .image-preview img {
            width: 100%;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
        }

        .form-footer p {
            color: #777;
        }
        
        .form-footer a {
            color: #4CAF50;
            text-decoration: none;
        }
        
        .form-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Produk</h1>
        <form action="edit.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
            <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($product['image']); ?>">

            <label for="name">Nama Produk:</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

            <label for="price">Harga Produk:</label>
            <input type="number" name="price" id="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>

            <label for="stock">Stok Produk:</label>
            <input type="number" name="stock" id="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>

            <label for="image">Unggah Gambar (JPG/PNG):</label>
            <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png">

            <div class="image-preview">
                <p>Gambar saat ini:</p>
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Gambar Produk">
            </div>

            <div class="button-container">
                <button type="submit">Simpan Perubahan</button>
                <a href="admin_dashboard.php"><button type="button" class="back-button">Kembali ke Dashboard</button></a>
            </div>
        </form>

        <div class="form-footer">
            <p>Perhatian: Pastikan gambar yang diunggah berukuran tidak terlalu besar untuk kenyamanan penggunaan.</p>
        </div>
    </div>
</body>
</html>

