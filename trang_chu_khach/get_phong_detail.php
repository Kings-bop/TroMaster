<?php
require_once 'connect.php';

header('Content-Type: application/json');

if (isset($_GET['id_phong'])) {
    $id_phong = $_GET['id_phong'];
    $stmt = $conn->prepare("
        SELECT p.id_phong, p.so_phong, p.dien_tich, p.gia_thue, p.trang_thai, p.hinh_anh, p.mo_ta, k.dia_chi
        FROM phong_tro p
        JOIN khu_tro k ON p.id_khu_tro = k.id_khu_tro
        WHERE p.id_phong = ?
    ");
    $stmt->execute([$id_phong]);
    $phong = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($phong) {
        echo json_encode(['success' => true, ...$phong]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Phòng không tồn tại']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID phòng không hợp lệ']);
}
?>