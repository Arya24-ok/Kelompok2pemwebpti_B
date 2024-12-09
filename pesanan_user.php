<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include 'connect.php';

// Ambil username dari sesi
$username = $_SESSION['username'];

// Query untuk mendapatkan pesanan berdasarkan username
$sql = "SELECT id, product_name, quantity, total_price, payment_status, order_date 
        FROM orders 
        WHERE customer_username = :username 
        ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username);
$stmt->execute();

$rowCount = $stmt->rowCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logo.png" type="image/x-icon">
    <title>Pesanan Saya</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #4CAF50;
            color: white;
        }

        footer {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 10px 20px;
        }

        .back-button {
            display: block;
            background-color: #f44336;
            color: white;
            text-align: center;
            padding: 12px 20px;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            margin: 20px auto;
            width: 200px;
            transition: all 0.3s ease-in-out;
        }

        .back-button:hover {
            background-color: #d32f2f;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(211, 47, 47, 0.3);
        }
    </style>
</head>
<body>

<header>
    <h1>Pesanan Saya</h1>
</header>

<?php if ($rowCount > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID Pesanan</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Total Harga</th>
                <th>Status Pembayaran</th>
                <th>Tanggal Pesanan</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= htmlspecialchars($row['quantity']) ?></td>
                    <td>Rp <?= number_format($row['total_price'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($row['payment_status']) ?></td>
                    <td><?= date("d-m-Y H:i", strtotime($row['order_date'])) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p style="text-align: center;">Belum ada pesanan.</p>
<?php endif; ?>

<!-- Tombol kembali ke dashboard -->
<a href="user_dashboard.php" class="back-button">Kembali ke Dashboard</a>

<footer>
    <p>© 2024 Buah Segar. All Rights Reserved.</p>
</footer>

</body>
</html>
