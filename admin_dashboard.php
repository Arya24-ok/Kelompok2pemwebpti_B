<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'connect.php';

// Query SQL
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
    <title>Data Produk</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
            background-color: #f8f8f8;
        }

        /* Header */
        header {
            background-image: url('background2.jpg');
            padding: 30px;
            color: white;
            text-align: center;
        }

        header h1 {
            font-size: 36px;
            color: white;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
            font-weight: bold;
        }

        /* Navigation */
        nav {
            position: sticky;
            top: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: white;
            padding: 10px 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .search-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #searchInput {
            width: 300px;
            padding: 8px;
            border: 2px solid black;
            border-radius: 5px;
            font-size: 16px;
            transition: width 0.3s ease;
        }

        #searchInput:focus {
            width: 350px;
        }

        nav button {
            padding: 10px 15px;
            background-color: white;
            color: black;
            border: 1px solid black;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        nav button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        /* Product grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 80px 20px 20px;
            flex-grow: 1;
        }

        .product-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .product-card img {
            max-width: 100%;
            height: 150px;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .product-card h3 {
            font-size: 1.2em;
            margin: 10px 0;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .edit-button, .delete-button {
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .edit-button {
            background-color: #007BFF;
            color: white;
        }

        .edit-button:hover {
            background-color: #0056b3;
        }

        .delete-button {
            background-color: #FF4C4C;
            color: white;
        }

        .delete-button:hover {
            background-color: #e03e3e;
        }

        footer {
            background: linear-gradient(135deg, #000000, #2c2c2c);
            color: white;
            text-align: center;
            padding: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <h1>ADMIN PANEL FRUTOPIA</h1>
    </header>

    <nav>
        <form class="search-form">
            <input type="text" id="searchInput" placeholder="Cari Produk..." onkeyup="filterProducts()" />
            <button type="button" onclick="filterProducts()">Cari</button>
        </form>
        <div>
            <button onclick="window.location.href='logout.php';">Logout</button>
            <button onclick="window.location.href='tambah.php';">Tambah Produk</button>
            <button onclick="window.location.href='info_pesanan.php';">Info Pemesanan</button>
            <button onclick="window.location.href='info_pengiriman.php';">Info Pengiriman</button>
            <button onclick="window.location.href='keluhan.php';">Konsol</button>
        </div>
    </nav>

    <div class="product-grid" id="product-grid">
    <?php
    if ($rowCount > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<div class='product-card' data-id='" . htmlspecialchars($row["id"]) . "' data-name='" . strtolower(htmlspecialchars($row["name"])) . "'>";
            echo "<img src='" . htmlspecialchars($row["image"]) . "' alt='Gambar Produk'>";
            echo "<h3>" . htmlspecialchars($row["name"]) . "</h3>";
            echo "<p>Stok Per KG: " . htmlspecialchars($row["stock"]) . "</p>";
            echo "<p>Harga Per KG: Rp " . number_format($row["price"], 0, ',', '.') . "</p>";
            echo "<div class='action-buttons'>";
            echo "<form action='edit.php' method='GET' style='display:inline;'>";
            echo "<input type='hidden' name='id' value='" . htmlspecialchars($row["id"]) . "'>";
            echo "<button type='submit' class='edit-button'>Edit</button>";
            echo "</form>";
            echo "<button class='delete-button' onclick='deleteProduct(" . htmlspecialchars($row["id"]) . ")'>Hapus</button>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p>Tidak ada produk tersedia.</p>";
    }
    ?>
    </div>

    <footer>
        <p>© 2024 Frutopia. All Rights Reserved.</p>
    </footer>

    <script>
        function filterProducts() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const productCards = document.querySelectorAll('.product-card');

            productCards.forEach(card => {
                const productName = card.getAttribute('data-name');
                if (productName.includes(searchInput)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function deleteProduct(productId) {
            if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
                fetch('delete_product.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: productId }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Produk berhasil dihapus!');
                        const productCard = document.querySelector(`.product-card[data-id="${productId}"]`);
                        if (productCard) productCard.remove();
                    } else {
                        alert('Gagal menghapus produk: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus produk.');
                });
            }
        }
    </script>
</body>
</html>
