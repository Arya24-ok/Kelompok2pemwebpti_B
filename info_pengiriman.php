<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'connect.php';

// Query untuk mengambil data pengiriman dari database
$sql = "SELECT id, order_id, customer_name, customer_address, product_name, quantity, total_price, user_phone, order_date, shipping_status 
        FROM shipments 
        ORDER BY order_date DESC";
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
    <title>Info Pengiriman - Admin Panel</title>
    <style>
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

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #f4f4f4;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        nav button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }

        .shipment-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .shipment-table th, .shipment-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .shipment-table th {
            background-color: #4CAF50;
            color: white;
        }

        .shipment-table td:last-child {
            text-align: center;
        }

        footer {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 10px 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<header>
    <h1>Info Pengiriman - Admin Panel</h1>
</header>

<nav>
    <button onclick="window.location.href='logout.php';">Logout</button>
    <button onclick="window.location.href='admin_dashboard.php';">Dashboard Produk</button>
    <button onclick="window.location.href='info_pemesanan.php';">Info Pemesanan</button>
</nav>

<!-- Tabel Pengiriman -->
<table class="shipment-table">
    <thead>
        <tr>
            <th>ID Pengiriman</th>
            <th>ID Pesanan</th>
            <th>Nama Pembeli</th>
            <th>Alamat Pembeli</th>
            <th>Produk</th>
            <th>Jumlah</th>
            <th>Total Harga</th>
            <th>Nomor Telepon</th>
            <th>Tanggal Pengiriman</th>
            <th>Status Pengiriman</th>
        </tr>
    </thead>
    <tbody>
    <?php
    if ($rowCount > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["order_id"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["customer_name"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["customer_address"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["product_name"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["quantity"]) . "</td>";
            echo "<td>Rp " . number_format($row["total_price"], 0, ',', '.') . "</td>";
            echo "<td>" . htmlspecialchars($row["user_phone"]) . "</td>";
            echo "<td>" . date("d-m-Y H:i", strtotime($row["order_date"])) . "</td>";
            echo "<td style='color: blue;'>" . htmlspecialchars($row["shipping_status"]) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='10' style='text-align: center;'>Tidak ada pengiriman.</td></tr>";
    }
    ?>
    </tbody>
</table>

<footer>
    <p>© 2024 Buah Segar. All Rights Reserved.</p>
</footer>

</body>
</html>
