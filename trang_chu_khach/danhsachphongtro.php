<?php
require_once 'connect.php';

// Lấy danh sách tất cả phòng trọ
$stmt_phong = $conn->prepare("
    SELECT p.id_phong, p.so_phong, p.dien_tich, p.gia_thue, p.trang_thai, p.hinh_anh, p.mo_ta, k.dia_chi, k.id_khu_tro
    FROM phong_tro p
    JOIN khu_tro k ON p.id_khu_tro = k.id_khu_tro
");
$stmt_phong->execute();
$phong_list = $stmt_phong->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Danh sách phòng trọ</h2>
<div class="rooms">
    <?php foreach ($phong_list as $phong): ?>
        <div class="room-card" data-khu-tro="<?php echo htmlspecialchars($phong['id_khu_tro']); ?>">
            <img src="<?php echo htmlspecialchars($phong['hinh_anh'] ?? 'https://via.placeholder.com/300x200?text=Phòng+' . htmlspecialchars($phong['so_phong'])); ?>" alt="Phòng <?php echo htmlspecialchars($phong['so_phong']); ?>">
            <div class="details">
                <h3>Phòng <?php echo htmlspecialchars($phong['so_phong']); ?> - <?php echo htmlspecialchars($phong['dia_chi']); ?></h3>
                <p>Diện tích: <?php echo htmlspecialchars($phong['dien_tich']); ?>m²</p>
                <p class="price"><?php echo number_format($phong['gia_thue'], 0, ',', '.'); ?> VNĐ/tháng</p>
                <p>Trạng thái: <?php echo $phong['trang_thai'] == 'trong' ? 'Trống' : 'Đã thuê'; ?></p>
                <button <?php echo $phong['trang_thai'] == 'da_thue' ? 'disabled' : ''; ?>>Đăng ký thuê</button>
                <button class="view-details" onclick="openDetail(<?php echo $phong['id_phong']; ?>)">Xem chi tiết</button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    function openDetail(id_phong) {
        window.open(`chi_tiet_phong.php?id_phong=${id_phong}`, '_blank');
    }
</script>

<style>
    .rooms { display: flex; flex-wrap: wrap; gap: 20px; padding: 20px; }
    .room-card { border: 1px solid #ddd; border-radius: 5px; width: 300px; text-align: center; }
    .room-card img { width: 100%; height: 200px; object-fit: cover; }
    .details { padding: 10px; }
    .price { color: #e74c3c; font-weight: bold; }
    button { padding: 5px 10px; margin: 5px; cursor: pointer; }
    button:disabled { background-color: #ccc; cursor: not-allowed; }
    .view-details { background-color: #3498db; color: white; border: none; }
    .view-details:hover { background-color: #2980b9; }
</style>