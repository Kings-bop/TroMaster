<?php
header('Content-Type: application/json');
require_once 'connect.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $so_phong = $data['so_phong'] ?? '';
    $id_khu_tro = $data['id_khu_tro'] ?? null;
    $gia_thue = $data['gia_thue'] ?? 0;
    $trang_thai = $data['trang_thai'] ?? 'trong';

    if (empty($so_phong) || empty($id_khu_tro) || empty($gia_thue)) {
        throw new Exception('Vui lòng điền đầy đủ thông tin!');
    }

    // Kiểm tra khu trọ tồn tại
    $stmt = $conn->prepare("SELECT id_khu_tro FROM khu_tro WHERE id_khu_tro = :id_khu_tro");
    $stmt->bindParam(':id_khu_tro', $id_khu_tro, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount() === 0) {
        throw new Exception('Khu trọ không tồn tại!');
    }

    // Kiểm tra trạng thái hợp lệ
    if (!in_array($trang_thai, ['trong', 'da_thue'])) {
        throw new Exception('Trạng thái không hợp lệ!');
    }

    $query = "INSERT INTO phong_tro (so_phong, id_khu_tro, gia_thue, trang_thai, ngay_tao) VALUES (:so_phong, :id_khu_tro, :gia_thue, :trang_thai, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':so_phong', $so_phong, PDO::PARAM_STR);
    $stmt->bindParam(':id_khu_tro', $id_khu_tro, PDO::PARAM_INT);
    $stmt->bindParam(':gia_thue', $gia_thue, PDO::PARAM_STR);
    $stmt->bindParam(':trang_thai', $trang_thai, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Thêm phòng thành công!'
        ]);
    } else {
        throw new Exception('Thêm phòng thất bại!');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>