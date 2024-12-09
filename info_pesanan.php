<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'connect.php';

// Query untuk mengambil data pesanan dari database
$sql = "SELECT orders.id, orders.customer_name, orders.customer_address, orders.product_name, orders.quantity, orders.total_price, orders.payment_status, orders.order_date, orders.user_phone, orders.payment_proof 
        FROM orders 
        ORDER BY orders.order_date DESC";
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
    <title>Info Pemesanan - Admin Panel</title>
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

        .order-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .order-table th, .order-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .order-table th {
            background-color: #4CAF50;
            color: white;
        }

        .order-table td:last-child {
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
    <h1>Info Pemesanan - Admin Panel</h1>
</header>

<nav>
    <button onclick="window.location.href='logout.php';">Logout</button>
    <button onclick="window.location.href='admin_dashboard.php';">Dashboard Produk</button>
</nav>

<!-- Tabel Pesanan -->
<table class="order-table">
    <thead>
        <tr>
            <th>ID Pesanan</th>
            <th>Nama Pembeli</th>
            <th>Alamat Pembeli</th>
            <th>Produk</th>
            <th>Jumlah</th>
            <th>Total Harga</th>
            <th>Status Pembayaran</th>
            <th>Nomor Telepon</th>
            <th>Bukti Pembayaran</th>
            <th>Tanggal Pemesanan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($rowCount > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr data-id='" . htmlspecialchars($row["id"]) . "'>";
                echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["customer_name"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["customer_address"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["product_name"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["quantity"]) . "</td>";
                echo "<td>Rp " . number_format($row["total_price"], 0, ',', '.') . "</td>";
                echo "<td>" . htmlspecialchars($row["payment_status"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["user_phone"]) . "</td>";
                if ($row["payment_proof"]) {
                    echo "<td><img src='" . htmlspecialchars($row["payment_proof"]) . "' alt='Bukti Pembayaran' style='max-width: 100px;'></td>";
                } else {
                    echo "<td>No Proof</td>";
                }
                echo "<td>" . date("d-m-Y H:i", strtotime($row["order_date"])) . "</td>";
                echo "<td><button class='send-button' data-id='" . htmlspecialchars($row["id"]) . "'>Kirim</button></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='11' style='text-align: center;'>Tidak ada pesanan.</td></tr>";
        }
        ?>
    </tbody>
</table>

<footer>
    <p>© 2024 Buah Segar. All Rights Reserved.</p>
</footer>

<script>
    document.querySelectorAll('.send-button').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-id');
            const row = this.closest('tr');

            if (confirm('Apakah Anda yakin ingin mengirim pesanan ini?')) {
                fetch('process_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: orderId }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pesanan berhasil dikirim.');
                        row.remove();
                    } else {
                        alert('Gagal mengirim pesanan: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan.');
                });
            }
        });
    });
</script>

</body>
</html>
