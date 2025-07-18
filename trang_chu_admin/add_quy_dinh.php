<?php
header('Content-Type: application/json');
require_once 'connect.php';

try {
    // Lấy dữ liệu từ request
    $data = json_decode(file_get_contents('php://input'), true);
    $idKhuTro = $data['idKhuTro'] ?? '';
    $noiDung = $data['link'] ?? ''; // Sử dụng 'link' từ request để gán vào noi_dung

    // Kiểm tra dữ liệu đầu vào
    if (empty($idKhuTro) || empty($noiDung)) {
        throw new Exception('Vui lòng điền đầy đủ thông tin (ID khu trọ và link quy định)!');
    }

    // Chuẩn bị và thực thi truy vấn thêm
    $query = "INSERT INTO quy_dinh (id_khu_tro, noi_dung) VALUES (:idKhuTro, :noiDung)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':idKhuTro', $idKhuTro, PDO::PARAM_INT);
    $stmt->bindParam(':noiDung', $noiDung, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Thêm quy định thành công!',
            'id' => $conn->lastInsertId()
        ]);
    } else {
        throw new Exception('Thêm quy định thất bại!');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>