<?php
header('Content-Type: application/json');
require_once 'connect.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? '';

    if (empty($id)) {
        throw new Exception('Vui lòng cung cấp ID khu trọ!');
    }

    $query = "DELETE FROM khu_tro WHERE id_khu_tro = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Xóa khu trọ thành công!'
        ]);
    } else {
        throw new Exception('Xóa khu trọ thất bại!');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>