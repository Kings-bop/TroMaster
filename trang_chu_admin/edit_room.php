<?php
header('Content-Type: application/json');
require_once 'connect.php';

try {
    $data = json_decode(file_get_contents('php://input'), true); //xử lý dữ liệu json gửi đến server
    $id_phong = $data['id_phong'] ?? '';
    $so_phong = $data['so_phong'] ?? '';
    $id_khu_tro = $data['id_khu_tro'] ?? null;
    $gia_thue = $data['gia_thue'] ?? 0;
    $trang_thai = $data['trang_thai'] ?? 'trong';

    if (empty($id_phong) || empty($so_phong)) {
        throw new Exception('Vui lòng điền đầy đủ ID và số phòng!');
    }

    $query = "UPDATE phong_tro SET so_phong = :so_phong, id_khu_tro = :id_khu_tro, gia_thue = :gia_thue, trang_thai = :trang_thai, ngay_cap_nhat = NOW() WHERE id_phong = :id_phong";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_phong', $id_phong, PDO::PARAM_INT);
    $stmt->bindParam(':so_phong', $so_phong, PDO::PARAM_STR);
    $stmt->bindParam(':id_khu_tro', $id_khu_tro, PDO::PARAM_INT);
    $stmt->bindParam(':gia_thue', $gia_thue, PDO::PARAM_STR); // DECIMAL nên dùng STR
    $stmt->bindParam(':trang_thai', $trang_thai, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật phòng thành công!'
        ]);
    } else {
        throw new Exception('Cập nhật phòng thất bại: ' . implode(', ', $stmt->errorInfo()));
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>