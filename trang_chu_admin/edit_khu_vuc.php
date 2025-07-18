<?php
header('Content-Type: application/json');
require_once 'connect.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? '';
    $address = $data['address'] ?? '';
    $overview = $data['overview'] ?? '';

    if (empty($id) || empty($address)) {
        throw new Exception('Vui lòng điền đầy đủ thông tin (ID và địa chỉ)!');
    }

    $query = "UPDATE khu_tro SET dia_chi = :address, tong_quan = :overview, ngay_cap_nhat = NOW() WHERE id_khu_tro = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':address', $address, PDO::PARAM_STR);
    $stmt->bindParam(':overview', $overview, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật khu trọ thành công!'
        ]);
    } else {
        throw new Exception('Cập nhật khu trọ thất bại!');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>