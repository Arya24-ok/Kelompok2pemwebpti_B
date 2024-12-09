<?php
// Include koneksi database
include 'connect.php'; // Pastikan path file ini sesuai dengan lokasi file connect.php Anda

// Pastikan sesi dimulai hanya jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inisialisasi pesan error/sukses
$errorMessage = "";
$successMessage = "";

// Pastikan pengguna sudah login, jika belum arahkan ke halaman login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$customer_username = $_SESSION['username']; // Menangkap username dari sesi

// Jika form pemesanan disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : null;
    $customer_address = isset($_POST['customer_address']) ? trim($_POST['customer_address']) : null;
    $user_phone = isset($_POST['user_phone']) ? trim($_POST['user_phone']) : null;
    $product_name = isset($_POST['product_name']) ? trim($_POST['product_name']) : null;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0.0;
    $payment_proof = isset($_FILES['payment_proof']) ? $_FILES['payment_proof'] : null;

    if (!$customer_name || !$customer_address || !$user_phone || !$product_name || $quantity <= 0 || $price <= 0) {
        $errorMessage = "Semua field harus diisi dengan benar.";
    } else {
        // Start transaction to ensure data consistency
        $conn->beginTransaction();
        
        try {
            // Proses upload bukti pembayaran
            $payment_proof_name = null;
            if ($payment_proof && $payment_proof['error'] == 0) {
                $upload_dir = 'uploads/';
                $payment_proof_name = $upload_dir . basename($payment_proof['name']);
                move_uploaded_file($payment_proof['tmp_name'], $payment_proof_name);
            }

            $total_price = $quantity * $price;
            $payment_status = 'Belum Dibayar';

            // Insert order into orders table
            $orderSql = "INSERT INTO orders (customer_name, customer_address, user_phone, product_name, quantity, price, total_price, payment_proof, payment_status, customer_username) 
                         VALUES (:customer_name, :customer_address, :user_phone, :product_name, :quantity, :price, :total_price, :payment_proof, :payment_status, :customer_username)";
            $orderStmt = $conn->prepare($orderSql);
            $orderStmt->bindParam(':customer_name', $customer_name);
            $orderStmt->bindParam(':customer_address', $customer_address);
            $orderStmt->bindParam(':user_phone', $user_phone);
            $orderStmt->bindParam(':product_name', $product_name);
            $orderStmt->bindParam(':quantity', $quantity);
            $orderStmt->bindParam(':price', $price);
            $orderStmt->bindParam(':total_price', $total_price);
            $orderStmt->bindParam(':payment_proof', $payment_proof_name);
            $orderStmt->bindParam(':payment_status', $payment_status);
            $orderStmt->bindParam(':customer_username', $customer_username); // Bind username
            $orderStmt->execute();

            // Reduce stock in products table
            $stockSql = "UPDATE products SET stock = stock - :quantity WHERE name = :product_name AND stock >= :quantity";
            $stockStmt = $conn->prepare($stockSql);
            $stockStmt->bindParam(':quantity', $quantity);
            $stockStmt->bindParam(':product_name', $product_name);
            $stockStmt->execute();

            if ($stockStmt->rowCount() > 0) {
                // Commit transaction
                $conn->commit();
                $successMessage = "Pesanan berhasil dibuat.";
            } else {
                // Rollback transaction if stock is insufficient
                $conn->rollBack();
                $errorMessage = "Stok produk tidak mencukupi.";
            }
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollBack();
            $errorMessage = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}

// Query to fetch products from the database
$sql = "SELECT * FROM products";
$stmt = $conn->prepare($sql);
$stmt->execute();

$rowCount = $stmt->rowCount();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logo.png" type="image/x-icon">
    <title>Dashboard User - Buah Segar</title>
    <style>
   /* Global Styles */
body {
    font-family: 'Roboto', sans-serif;
    margin: 0;
    padding: 0;
    background: linear-gradient(to bottom, #f9f9f9, #e8f5e9);
    color: #333;
    line-height: 1.6;
}

header {
    background-image: url('header.jpg'); /* Ganti dengan path yang sesuai ke file header.jpg */
    background-size: cover; /* Agar gambar mengisi seluruh area header */
    background-position: center; /* Menyusun gambar agar berada di tengah */
    padding: 40px;
    text-align: center;
    position: sticky; /* Membuat header tetap terlihat saat di-scroll */
    top: 0; /* Menempel di atas halaman */
    z-index: 1000; /* Pastikan header berada di atas elemen lainnya */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Menambahkan bayangan pada header */
}

/* Untuk judul h1 */
header h1 {
    font-size: 36px; /* Ukuran font */
    color: white; /* Warna teks agar kontras dengan gambar latar belakang */
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3); /* Bayangan pada teks agar lebih menonjol */
    margin: 0; /* Menghilangkan margin default */
    font-weight: bold;
}



/* Navigation Bar */
nav {
    position: fixed;
    top: 80px; /* Menjaga navbar agar tidak tertutup header */
    right: 0;
    display: flex;
    flex-direction: row; /* Menyusun tombol secara horizontal */
    align-items: center;
    gap: 10px; /* Jarak antar tombol */
    background-color: transparent; /* Navbar tanpa latar belakang */
    z-index: 1000; /* Pastikan navbar memiliki z-index lebih tinggi */
}

/* Style untuk setiap tombol */
nav button {
    width: 150px;
    padding: 10px;
    background-color: white; /* Mengubah warna latar belakang tombol menjadi putih */
    color: black; /* Mengubah warna teks menjadi hijau (warna tombol sebelumnya) */
    border: 2px solid black; /* Menambahkan border berwarna hijau */
    border-radius: 5px; /* Membuat sudut tombol sedikit melengkung */
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Menambahkan bayangan halus pada tombol */
}

/* Efek saat tombol di-hover */
nav button:hover {
    background-color: #f0f0f0; /* Warna latar belakang tombol berubah saat di-hover */
    transform: scale(1.05);
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2); /* Bayangan lebih kuat saat hover */
}

/* Efek saat tombol aktif */
nav button:active {
    background-color: #e0e0e0; /* Warna latar belakang tombol sedikit lebih gelap saat di-klik */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); /* Bayangan lebih kuat saat tombol aktif */
}

.search-form {
    display: flex;
    align-items: center;
    margin-right: 500px; /* Jarak kanan agar form tidak terlalu dekat dengan tombol */
}

.search-form input {
    padding: 5px;
    font-size: 14px;
    margin-right: 10px; /* Jarak antar input dan tombol cari */
}

.search-form button {
    padding: 5px;
    font-size: 14px;
}

/* Section Tentang Frutopia */
.about {
    padding: 60px;
    background-image: url('about.jpg');
    color: #fff;
    text-align: center;
    border-radius: 20px;
    margin: 20px auto;
    max-width: 90%;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

/* Sub-heading Tentang Frutopia */
.heading_ok {
    font-size: 2.5em;
    font-weight: bold;
    color: #FFD700; /* Warna emas */
    margin-bottom: 20px;
    text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.4);
}

/* Main Heading */
.ok0 {
    font-size: 3em;
    color: #fff;
    margin-bottom: 30px;
    text-shadow: 3px 3px 10px rgba(0, 0, 0, 0.5);
}

/* Kontainer Utama */
.ok1 {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

/* Kontainer Isi */
.ok2 {
    max-width: 800px;
    background: rgba(255, 255, 255, 0.1);
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(8px);
}

/* Judul dalam Kontainer */
.ok2 h3 {
    font-size: 2em;
    font-weight: bold;
    color: #FFD700; /* Warna emas */
    margin-bottom: 20px;
    text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.4);
}

/* Paragraf dalam Kontainer */
.ok2 p {
    font-size: 1.2em;
    line-height: 1.8;
    margin-bottom: 15px;
    color: black;
    text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.3);
}

/* Product Grid */
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    padding: 20px;
    max-width: 1200px;
    margin: auto;
}

.product-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}

.product-card:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
}

.product-card img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    margin-bottom: 15px;
    transition: transform 0.3s ease-in-out;
}

.product-card img:hover {
    transform: scale(1.1);
}

.product-card h3 {
    font-size: 1.4em;
    color: #333;
    margin-bottom: 10px;
}

.product-card p {
    font-size: 1em;
    color: #777;
    margin-bottom: 20px;
}

/* Tooltip Effect for Price */
.product-card p:hover::after {
    content: "Harga belum termasuk pajak";
    position: absolute;
    background-color: #333;
    color: #fff;
    padding: 5px 10px;
    font-size: 12px;
    border-radius: 5px;
    top: -30px;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    z-index: 10;
    opacity: 1;
    transition: opacity 0.3s ease-in-out;
}

/* Card Flipping Effect */
.product-card:hover .flip-card-inner {
    transform: rotateY(180deg);
}

.flip-card-inner {
    position: relative;
    width: 100%;
    height: 100%;
    transition: transform 0.6s;
    transform-style: preserve-3d;
}

.flip-card-back {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    background-color: #f4f4f4;
    transform: rotateY(180deg);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 10px;
}

.order-btn {
    background: linear-gradient(to right, #007BFF, #0056b3);
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s ease;
}

.order-btn:hover {
    background: linear-gradient(to right, #0056b3, #004080);
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Order Form Modal */
.order-form {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8); /* Lebih gelap untuk fokus */
    z-index: 1000;
    justify-content: center;
    align-items: center;
    animation: fadeIn 0.4s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.order-form .form-container {
    background: #ffffff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3); /* Bayangan lebih lembut */
    width: 90%;
    max-width: 450px;
    animation: slideIn 0.5s ease-in-out;
    text-align: center;
}

@keyframes slideIn {
    from {
        transform: translateY(-30%);
    }
    to {
        transform: translateY(0);
    }
}

.order-form h2 {
    font-size: 24px;
    margin-bottom: 20px;
    color: #333;
    font-weight: bold;
}

.order-form input,
.order-form select,
.order-form textarea {
    width: 100%;
    padding: 14px;
    margin: 15px 0;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 16px;
    transition: border-color 0.3s ease;
    box-sizing: border-box;
}

.order-form input:focus,
.order-form select:focus,
.order-form textarea:focus {
    border-color: #4caf50;
    outline: none;
    box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
}

.order-form textarea {
    resize: none;
    height: 100px;
}

.order-form button {
    background-color: #4caf50;
    color: white;
    border: none;
    padding: 14px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    width: 100%;
    transition: background-color 0.3s ease;
}

.order-form button:hover {
    background-color: #45a049;
    box-shadow: 0 4px 8px rgba(76, 175, 80, 0.2);
}

.order-form .close-button {
    position: absolute;
    top: 20px;
    right: 20px;
    background: transparent;
    border: none;
    font-size: 18px;
    font-weight: bold;
    color: #333;
    cursor: pointer;
    transition: color 0.3s ease;
}

.order-form .close-button:hover {
    color: #4caf50;
}

/* Responsiveness */
@media (max-width: 768px) {
    .order-form .form-container {
        padding: 20px;
    }

    .order-form h2 {
        font-size: 20px;
    }

    .order-form input,
    .order-form select,
    .order-form textarea {
        font-size: 14px;
    }

    .order-form button {
        font-size: 14px;
    }
}

/* Deskripsi Usaha */
/* Deskripsi Usaha */
section h2 {
    font-size: 2.5em;
    text-align: center;
    color: #4CAF50;
    margin-bottom: 20px;
    text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2);
    font-weight: bold;
}

section p {
    font-size: 1.2em;
    line-height: 1.8;
    color: #333;
    max-width: 800px;
    margin: 0 auto 20px; /* Rata tengah dengan auto margin */
    text-align: center; /* Rata tengah teks */
    background-color: #f9f9f9;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-top: 5px solid #4CAF50;
    font-family: "Arial", sans-serif;
}

/* Hover effect untuk deskripsi usaha */
section p:hover {
    transform: scale(1.02);
    transition: all 0.3s ease-in-out;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    border-top: 5px solid #218838; /* Aksen hijau lebih gelap */
}

/* Map Section */
.map {
    margin: 40px auto;
    padding: 20px;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 800px;
    text-align: center;
}

.map h3 {
    font-size: 1.8em;
    margin-bottom: 10px;
    color: #4CAF50;
    text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.2);
}

.map p {
    font-size: 1.2em;
    color: #666;
    margin-bottom: 20px;
}

.map iframe {
    width: 100%;
    height: 400px;
    border: none;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Tambahkan hover effect pada Map */
.map iframe:hover {
    transform: scale(1.02);
    transition: all 0.3s ease-in-out;
}

/* Footer */
footer {
    background: linear-gradient(135deg, #000000, #2c2c2c); /* Gradasi hitam estetik */
    color: white; /* Warna teks */
    text-align: center; /* Pusatkan teks */
    padding: 15px 20px; /* Ruang dalam */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5); /* Bayangan pada footer */
    position: relative; /* Agar efek bayangan terlihat */
}

footer p {
    margin: 0;
    font-size: 14px;
    font-family: 'Arial', sans-serif; /* Pilih font */
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7); /* Efek teks timbul */
    font-weight: bold; /* Teks lebih tebal */
}

footer .social-media {
    display: flex;
    justify-content: center;
    gap: 15px; /* Jarak antar ikon */
    margin-top: 10px;
}

footer .social-media img {
    width: 30px;
    height: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.4); /* Bayangan pada gambar */
    border-radius: 50%; /* Gambar berbentuk lingkaran */
    transition: transform 0.3s, box-shadow 0.3s;
}

footer .social-media img:hover {
    transform: scale(1.2) rotate(15deg); /* Efek pembesaran dan rotasi */
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.6); /* Bayangan lebih besar saat hover */
}

/* Responsive Design */
@media (max-width: 768px) {
    .product-grid {
        grid-template-columns: 1fr;
    }

    nav {
        flex-direction: column;
    }

    nav button {
        margin-bottom: 10px;
    }
}

    </style>

    </style>
</head>
<body>

<header>
    <h1>FRUTOPIA</h1>
</header>

<nav>
  <form class="search-form">
    <input 
        type="text" 
        id="searchInput" 
        placeholder="Cari Produk..." 
        onkeyup="filterProducts()" 
    />
    <button type="button" onclick="filterProducts()">Cari</button>
  </form>

  <button onclick="window.location.href='logout.php';">Logout</button>
  <button onclick="window.location.href='pesanan_user.php';">Pesanan Saya</button>
  <button onclick="window.location.href='kontak.php';">Masukan</button>
</nav>

<div class="product-grid" id="productGrid">
    <?php
    if ($rowCount > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<div class='product-card' data-name='" . htmlspecialchars($row['name']) . "'>";
            echo "<img src='" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "'>";
            echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
            echo "<p>Stock per KG: " . number_format($row['stock']) . "</p>";
            echo "<p>Harga per KG: " . number_format($row['price'], 2) . "</p>";
            echo "<button class='order-btn' onclick='showOrderForm(" . htmlspecialchars(json_encode($row)) . ")'>Pesan</button>";
            echo "</div>";
        }
    } else {
        echo "<p>Tidak ada produk.</p>";
    }
    ?>
</div>

<script>
    function filterProducts() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const productCards = document.querySelectorAll('.product-card');

        productCards.forEach(card => {
            const productName = card.getAttribute('data-name').toLowerCase();
            if (productName.includes(searchInput)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>

<?php if ($errorMessage): ?>
    <div style="color: red; padding: 10px;"><?= htmlspecialchars($errorMessage) ?></div>
<?php elseif ($successMessage): ?>
    <div style="color: green; padding: 10px;"><?= htmlspecialchars($successMessage) ?></div>
<?php endif; ?>



<div class="product-grid">
    <?php
    if ($rowCount > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<div class='product-card'>";
            echo "<img src='" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "'>";
            echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
            echo "<p>Stock per KG: " . number_format($row['stock']) . "</p>";
            echo "<p>Harga per KG: " . number_format($row['price'], 2) . "</p>";
            echo "<button class='order-btn' onclick='showOrderForm(" . htmlspecialchars(json_encode($row)) . ")'>Pesan</button>";
            echo "</div>";
        }
    } else {
        echo "<p>Tidak ada produk.</p>";
    }
    ?>
</div>

<section class="about" id="tentang">
        <h3 class="heading_ok"> Tentang Frutopia </h3>
        <h1 class="ok0"> Kenapa Harus Frutopia </h1>

        <div class="ok1">
            <div class="ok2">
                <h3>Surga Buah Segar untuk Kesehatan Anda</h3>
                <p>Selamat datang di Frutopia, toko buah online terpercaya yang menyediakan berbagai macam buah segar, berkualitas tinggi, dan 100% alami.
                    Di Frutopia, kami berkomitmen untuk menghadirkan buah-buahan terbaik langsung dari kebun ke meja Anda.</p>
                <p>Temukan aneka buah lokal dan impor, mulai dari apel, jeruk, hingga nanas. 
                    Nikmati layanan pengiriman yang cepat dan aman hingga ke depan pintu rumah Anda.  
                </p>
            </div>
        </div>
</section>

<section class="map">
    <h3>Lokasi Toko</h3>
    <p>Jl. Imam Bonjol No.239, Gedong Air, Kec. Tj. Karang Bar., Kota Bandar Lampung, Lampung 35153</p>
    <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15934.602416134944!2d105.1644159!3d-5.4007393!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e40dbf698562843%3A0xddcf2e347e678c1f!2sJl.%20Imam%20Bonjol%20No.239%2C%20Gedong%20Air%2C%20Tj.%20Karang%20Bar.%2C%20Bandar%20Lampung%2C%20Lampung%2035153!5e0!3m2!1sen!2sid!4v1679871234567!5m2!1sen!2sid" 
        width="600" 
        height="450" 
        style="border:0;" 
        allowfullscreen="" 
        loading="lazy">
    </iframe>
</section>

<!-- Order form modal -->
<div id="orderForm" class="order-form">
    <div class="form-container">
        <h3>Form Pesanan</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_name" id="product_name">
            <input type="hidden" name="price" id="price">
            <label for="customer_name">Nama Lengkap:</label>
            <input type="text" name="customer_name" required>
            <label for="customer_address">Alamat:</label>
            <input type="text" name="customer_address" required>
            <label for="user_phone">Telepon:</label>
            <input type="text" name="user_phone" required>
            <label for="quantity">Jumlah:</label>
            <input type="number" name="quantity" required>
            <p>Nomor Dana:085841934647</p>
            <label for="payment_proof">Bukti Pembayaran:</label>
            <input type="file" name="payment_proof" required>
            <button type="submit">Pesan Sekarang</button>
        </form>
    </div>
</div>

<footer>
    <p>&copy; 2024 Kelompok 2</p>
    <section class="social-media">
    <a href="https://www.instagram.com/aryaansori/"><img src="IG.png" alt="Instagram"></a>
    <a href="https://wa.me/6285841934647"><img src="wa.png" alt="WhatsApp"></a>
    <a href="https://www.facebook.com/arya.p.ansori/"><img src="facebook.png" alt="Facebook"></a>
    </section>
</footer>

<script>
    function showOrderForm(product) {
        document.getElementById("orderForm").style.display = "flex";
        document.getElementById("product_name").value = product.name;
        document.getElementById("price").value = product.price;
    }

    // Close the order form if clicked outside of the form
    window.onclick = function(event) {
        if (event.target === document.getElementById("orderForm")) {
            document.getElementById("orderForm").style.display = "none";
        }
    }
</script>

</body>
</html>  


