<?php
header('Content-Type: application/json');
include 'connect.php';

try {
    // Mendapatkan data JSON dari permintaan
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['id']) || !is_numeric($input['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID produk tidak valid.']);
        exit;
    }

    $productId = intval($input['id']);

    // Query untuk menghapus produk
    $sql = "DELETE FROM products WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus produk.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Kesalahan server: ' . $e->getMessage()]);
}
?>
