<?php
session_start();
require_once 'connect.php';

header('Content-Type: application/json');

$response = ['success' => false, 'html' => ''];

try {
    $status = $_POST['status'] ?? 'tat-ca';
    $priceRange = $_POST['priceRange'] ?? '';
    $areaRange = $_POST['areaRange'] ?? '';
    $searchTerm = $_POST['searchTerm'] ?? '';

    // Khởi tạo HTML với các tiêu đề khu vực
    $html = '<h2 id="ha-dong">Phòng trọ khu vực Hà Đông</h2>';
    $html .= processArea('Hà Đông', $status, $priceRange, $areaRange, $searchTerm);
    $html .= '<h2 id="cau-giay">Phòng trọ khu vực Cầu Giấy</h2>';
    $html .= processArea('Cầu Giấy', $status, $priceRange, $areaRange, $searchTerm);
    $html .= '<h2 id="dong-da">Phòng trọ khu vực Đống Đa</h2>';
    $html .= processArea('Đống Đa', $status, $priceRange, $areaRange, $searchTerm);

    $response['success'] = true;
    $response['html'] = $html;
} catch (PDOException $e) {
    $response['html'] = '<p>Lỗi khi lọc phòng: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

echo json_encode($response);

function processArea($areaName, $status, $priceRange, $areaRange, $searchTerm) {
    global $conn;
    $sql = "SELECT p.id_phong, p.so_phong, p.dien_tich, p.gia_thue, p.trang_thai, p.hinh_anh, k.dia_chi 
            FROM phong_tro p 
            JOIN khu_tro k ON p.id_khu_tro = k.id_khu_tro 
            WHERE k.dia_chi LIKE :areaName AND p.trang_thai IN ('trong', 'da-thue')";

    $conditions = [];
    $params = [':areaName' => "%$areaName%"];
    if ($status !== 'tat-ca') {
        $conditions[] = "p.trang_thai = :status";
        $params[':status'] = $status;
    }
    if ($priceRange) {
        [$minPrice, $maxPrice] = explode('-', $priceRange);
        $conditions[] = "p.gia_thue BETWEEN :minPrice AND :maxPrice";
        $params[':minPrice'] = (int)$minPrice;
        $params[':maxPrice'] = (int)$maxPrice;
    }
    if ($areaRange) {
        [$minArea, $maxArea] = explode('-', $areaRange);
        $conditions[] = "p.dien_tich BETWEEN :minArea AND :maxArea";
        $params[':minArea'] = (int)$minArea;
        $params[':maxArea'] = (int)$maxArea;
    }
    if ($searchTerm) {
        $conditions[] = "(p.so_phong LIKE :searchTerm OR k.dia_chi LIKE :searchTerm)";
        $params[':searchTerm'] = "%$searchTerm%";
    }

    if (!empty($conditions)) {
        $sql .= " AND " . implode(" AND ", $conditions);
    }

    $stmt = $conn->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $html = '';
    if ($rooms) {
         $html .= '<div class="aly">';
        foreach ($rooms as $room) {
            $image = !empty($room['hinh_anh']) ? htmlspecialchars($room['hinh_anh']) : 'https://via.placeholder.com/150x100?text=Phòng+' . htmlspecialchars($room['so_phong']);
            $price = number_format($room['gia_thue'], 0, ',', '.');
            $area = number_format($room['dien_tich'], 1);
            $address = htmlspecialchars($room['dia_chi']);
            $posted_time = date('Y-m-d H:i:s', strtotime('-1 day'));
            $status_class = $room['trang_thai'] === 'trong' ? 'status-available' : 'status-rented';
            $status_text = $room['trang_thai'] === 'trong' ? 'Còn trống' : 'Đã thuê';
            $html .= '<div class="hot-room-card">';
            $html .= '<a href="chi_tiet_phong.php?id_phong=' . htmlspecialchars($room['id_phong']) . '">';
            $html .= '<img src="' . $image . '" alt="Phòng ' . htmlspecialchars($room['so_phong']) . '">';
            $html .= '</a>';
            $html .= '<div class="hot-room-info">';
            $html .= '<div class="rating">★★★★★ <span>CHO Ở GHÉP ĐÔI NAM NỮ</span></div>';
            $html .= '<div class="status"><span class="' . $status_class . '">' . $status_text . '</span></div>';
            $html .= '<div class="room-details">';
            $html .= '<span class="price">Giá: ' . $price . ' đ</span><br>';
            $html .= '<span class="area">Diện tích: ' . $area . 'm²</span><br>';
            $html .= '<span class="address">Địa chỉ: ' . $address . '</span><br>';
            $html .= '</div>';
            $html .= '<div class="posted-time">Cập nhật: ' . $posted_time . '</div>';
            $html .= '<div class="room-actions">';
            if ($room['trang_thai'] === 'trong') {
                $html .= '<a href="dang_ky_thue.php?id_phong=' . htmlspecialchars($room['id_phong']) . '" class="btn-rent">Đăng ký thuê</a>';
            }
            $html .= '<a href="chi_tiet_phong.php?id_phong=' . htmlspecialchars($room['id_phong']) . '" class="btn-detail">Xem chi tiết</a>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
            
        }$html .= '</div>';
    } else {
        $html .= '<p>Không có phòng nào theo tìm kiếm tại ' . htmlspecialchars($areaName) . '.</p>';
    }
    return $html;
}