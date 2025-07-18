<?php
header('Content-Type: application/json');
require_once 'connect.php';

try {
    // Lấy dữ liệu từ request
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? '';
    $idKhuTro = $data['idKhuTro'] ?? '';
    $noiDung = $data['link'] ?? ''; // Sử dụng 'link' từ request để gán vào noi_dung

    // Kiểm tra dữ liệu đầu vào
    if (empty($id) || empty($idKhuTro) || empty($noiDung)) {
        throw new Exception('Vui lòng điền đầy đủ thông tin (ID, ID khu trọ và link quy định)!');
    }

    // Chuẩn bị và thực thi truy vấn cập nhật
    $query = "UPDATE quy_dinh SET id_khu_tro = :idKhuTro, noi_dung = :noiDung, ngay_cap_nhat = NOW() WHERE id_quy_dinh = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':idKhuTro', $idKhuTro, PDO::PARAM_INT);
    $stmt->bindParam(':noiDung', $noiDung, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật quy định thành công!'
        ]);
    } else {
        throw new Exception('Cập nhật quy định thất bại!');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>