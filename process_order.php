<?php
include 'connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'])) {
    $orderId = $data['id'];

    try {
        $conn->beginTransaction();

        $sql = "SELECT * FROM orders WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan.']);
            exit;
        }

        $sql = "INSERT INTO shipments (order_id, customer_name, customer_address, product_name, quantity, total_price, user_phone, order_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $order['id'], $order['customer_name'], $order['customer_address'], $order['product_name'],
            $order['quantity'], $order['total_price'], $order['user_phone'], $order['order_date']
        ]);

        $sql = "DELETE FROM orders WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$orderId]);

        $conn->commit();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID pesanan tidak diberikan.']);
}
?>
