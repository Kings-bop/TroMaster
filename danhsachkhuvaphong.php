<?php
// Include file kết nối cơ sở dữ liệu
require_once 'connect.php';

// Lấy danh sách khu trọ cho khu vực nổi bật
$stmt_khu_tro = $conn->prepare("SELECT id_khu_tro, dia_chi FROM khu_tro");
$stmt_khu_tro->execute();
$khu_tro_list = $stmt_khu_tro->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách phòng trọ
$stmt_phong = $conn->prepare("
    SELECT p.id_phong, p.so_phong, p.dien_tich, p.gia_thue, p.trang_thai, p.hinh_anh, k.dia_chi
    FROM phong_tro p
    JOIN khu_tro k ON p.id_khu_tro = k.id_khu_tro
");
$stmt_phong->execute();
$phong_list = $stmt_phong->fetchAll(PDO::FETCH_ASSOC);
?>