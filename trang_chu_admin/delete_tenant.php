<?php
header('Content-Type: application/json');
require_once 'connect.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;

    if (empty($id) || !is_numeric($id)) {
        throw new Exception('ID khách thuê không hợp lệ!');
    }

    $query = "DELETE FROM khach_thue WHERE id_khach_thue = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Xóa khách thuê thành công!'
        ]);
    } else {
        throw new Exception('Xóa khách thuê thất bại: ' . implode(', ', $stmt->errorInfo()));
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>