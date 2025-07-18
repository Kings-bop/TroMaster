<?php
header('Content-Type: application/json');
require_once 'connect.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_phong = $data['id_phong'] ?? '';

    if (empty($id_phong)) {
        throw new Exception('ID phòng không được để trống!');
    }

    // Kiểm tra phòng tồn tại
    $stmt = $conn->prepare("SELECT id_phong FROM phong_tro WHERE id_phong = :id_phong");
    $stmt->bindParam(':id_phong', $id_phong, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() === 0) {
        throw new Exception('Phòng không tồn tại!');
    }

    $query = "DELETE FROM phong_tro WHERE id_phong = :id_phong";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_phong', $id_phong, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Xóa phòng thành công!'
        ]);
    } else {
        throw new Exception('Xóa phòng thất bại!');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>