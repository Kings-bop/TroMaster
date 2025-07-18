<?php
header('Content-Type: application/json');
require_once 'connect.php';

try {
    // Lấy dữ liệu từ request
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? '';

    // Kiểm tra dữ liệu đầu vào
    if (empty($id)) {
        throw new Exception('Vui lòng cung cấp ID quy định!');
    }

    // Chuẩn bị và thực thi truy vấn xóa
    $query = "DELETE FROM quy_dinh WHERE id_quy_dinh = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Xóa quy định thành công!'
        ]);
    } else {
        throw new Exception('Xóa quy định thất bại!');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>