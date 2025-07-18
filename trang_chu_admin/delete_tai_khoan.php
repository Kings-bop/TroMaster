<?php
header('Content-Type: application/json');
require_once 'connect.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? '';

    if (empty($id)) {
        throw new Exception('Vui lòng cung cấp ID tài khoản!');
    }

    $query = "DELETE FROM tai_khoan WHERE id_tai_khoan = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Xóa tài khoản thành công!'
        ]);
    } else {
        throw new Exception('Xóa tài khoản thất bại!');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>