<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'connect.php';

if (!isset($_GET['id_phong']) || !is_numeric($_GET['id_phong']) || $_GET['id_phong'] <= 0) {
    die("ID phòng không hợp lệ! Debug: " . var_export($_GET, true));
}

$id_phong = (int)$_GET['id_phong'];

$stmt = $conn->prepare("
    SELECT p.id_phong, p.so_phong, p.dien_tich, p.gia_thue, p.trang_thai, p.hinh_anh, p.mo_ta, k.dia_chi
    FROM phong_tro p
    JOIN khu_tro k ON p.id_khu_tro = k.id_khu_tro
    WHERE p.id_phong = ?
");
$stmt->execute([$id_phong]);
$phong = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$phong) {
    die("Phòng không tồn tại!");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết phòng - TroMaster</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .detail-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .detail-info {
            text-align: left;
        }
        .detail-info p {
            margin: 10px 0;
        }
        .price { color: #e74c3c; font-weight: bold; }
        button {
            padding: 10px 20px;
            margin: 5px;
            cursor: pointer;
            background-color: #2ecc71;
            color: white;
            border: none;
            border-radius: 5px;
        }
        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        button:hover:not(:disabled) {
            background-color: #27ae60;
        }
        .back-button {
            background-color: #3498db;
        }
        .back-button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <img class="detail-image" src="<?php echo htmlspecialchars($phong['hinh_anh'] ?: 'https://via.placeholder.com/400?text=No+Image'); ?>" alt="Phòng <?php echo htmlspecialchars($phong['so_phong']); ?>">
        <div class="detail-info">
            <h2>Phòng <?php echo htmlspecialchars($phong['so_phong']); ?> - <?php echo htmlspecialchars($phong['dia_chi']); ?></h2>
            <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($phong['dia_chi']); ?></p>
            <p><strong>Diện tích:</strong> <?php echo htmlspecialchars($phong['dien_tich']); ?>m²</p>
            <p><strong>Giá thuê:</strong> <?php echo number_format($phong['gia_thue'], 0, ',', '.'); ?> VNĐ/tháng</p>
            <p><strong>Trạng thái:</strong> <?php echo $phong['trang_thai'] == 'trong' ? 'Trống' : 'Đã thuê'; ?></p>
            <p><strong>Mô tả:</strong> <?php echo htmlspecialchars($phong['mo_ta'] ?: 'Chưa có mô tả'); ?></p>
            <button id="rent-button" <?php echo $phong['trang_thai'] == 'da_thue' ? 'disabled' : ''; ?> onclick="rentRoom(<?php echo $id_phong; ?>)">Thuê phòng</button>
            <button class="back-button" onclick="window.location.href='khach_thue.php'">Quay lại</button>
        </div>
    </div>

    <script>
        function rentRoom(id_phong) {
            // Chuyển hướng đến trang đăng ký thuê với id_phong
            window.location.href = `dang_ky_thue.php?id_phong=${id_phong}`;
        }
    </script>
</body>
</html>