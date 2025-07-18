<?php
session_start();
require_once 'connect.php'; // Kết nối cơ sở dữ liệu

// Lấy thông tin phòng từ id_phong
$id_phong = $_GET['id_phong'] ?? null;
$room_info = [];
if ($id_phong) {
    try {
        $sql = "SELECT p.so_phong, p.dien_tich, p.gia_thue, k.dia_chi 
                FROM phong_tro p 
                JOIN khu_tro k ON p.id_khu_tro = k.id_khu_tro 
                WHERE p.id_phong = :id_phong";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_phong', $id_phong, PDO::PARAM_INT);
        $stmt->execute();
        $room_info = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Lỗi truy vấn: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký thuê phòng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .info-group {
            margin-bottom: 15px;
        }
        .info-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .info-group p {
            margin: 5px 0;
        }
        .info-group input, .info-group select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .total {
            font-weight: bold;
            color: #00cc00;
            margin: 15px 0;
        }
        .terms {
            font-size: 18px;
            color: #666;
            margin-bottom: 15px;
        }
        .buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .back-btn {
            background-color: #757575;
            color: #fff;
        }
        .back-btn:hover {
            background-color: #555;
        }
        .confirm-btn {
            background-color: #00cc00;
            color: #fff;
        }
        .confirm-btn:hover {
            background-color: #009900;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Đăng ký thuê phòng</h2>
        
        <div class="info-group">
            <label>Thông tin phòng:</label>
            <p>Phòng <?php echo htmlspecialchars($room_info['so_phong'] ?? 'N/A') ?> - Khu vực <?php echo htmlspecialchars(strstr($room_info['dia_chi'] ?? '', ' ')) ?: 'N/A'; ?></p>
            <p>Giá: <?php echo $room_info['gia_thue'] ? number_format($room_info['gia_thue'], 0, ',', '.') . ' VNĐ/tháng' : 'N/A'; ?></p>
            <p>Diện tích: <?php echo $room_info['dien_tich'] ? number_format($room_info['dien_tich'], 1) . 'm²' : 'N/A'; ?></p>
            <p>Địa chỉ: <?php echo htmlspecialchars($room_info['dia_chi'] ?? 'N/A'); ?></p>
        </div>

        <div class="info-group">
            <label>Thông tin khách thuê:</label>
            <input type="text" id="ho_ten" placeholder="Họ và tên" required>
            <input type="text" id="so_dien_thoai" placeholder="Số điện thoại" required>
            <input type="email" id="email" placeholder="Email" required>
        </div>

        <div class="info-group">
            <label>Thời gian thuê:</label>
            <select id="duration">
                <option value="1">1 tháng</option>
                <option value="3">3 tháng</option>
                <option value="6">6 tháng</option>
                <option value="12">12 tháng</option>
            </select>
        </div>

        <div class="total" id="totalCost">Tổng chi phí: <?php echo $room_info['gia_thue'] ? number_format($room_info['gia_thue'], 0, ',', '.') . ' VNĐ' : 'N/A'; ?></div>

        <div class="terms">
            * Điều khoản: Thanh toán trước 50% tiền thuê, hủy hợp đồng trước 7 ngày sẽ được hoàn tiền 30%.
        </div>

        <div class="buttons">
            <button class="back-btn" onclick="goBack()">Quay lại</button>
            <button class="confirm-btn" onclick="confirmPayment()">Đồng ý thanh toán</button>
        </div>
    </div>
<script>
    const price = <?php echo json_encode($room_info['gia_thue'] ?? 5000000); ?>;
    const durationSelect = document.getElementById('duration');
    const totalCost = document.getElementById('totalCost');
    const idPhong = <?php echo json_encode($id_phong); ?>;
    const soPhong = <?php echo json_encode($room_info['so_phong'] ?? '101'); ?>;

    durationSelect.addEventListener('change', function() {
        const months = parseInt(this.value);
        const total = price * months;
        totalCost.textContent = `Tổng chi phí: ${total.toLocaleString()} VNĐ`;
    });

    function confirmPayment() {
        if (confirm('Bạn có muốn chuyển đến trang thanh toán?')) {
            const months = parseInt(durationSelect.value);
            const total = price * months;
            const ho_ten = document.getElementById('ho_ten').value;
            const so_dien_thoai = document.getElementById('so_dien_thoai').value;
            const email = document.getElementById('email').value;

            if (!ho_ten || !so_dien_thoai || !email) {
                alert('Vui lòng nhập đầy đủ thông tin khách thuê!');
                return;
            }

            // Lưu id_phong vào session trước khi gửi
            fetch('save_temp_user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `ho_ten=${encodeURIComponent(ho_ten)}&so_dien_thoai=${encodeURIComponent(so_dien_thoai)}&email=${encodeURIComponent(email)}`
            })
            .then(response => {
                if (!response.ok) throw new Error('Lỗi server: ' + response.statusText);
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Lưu id_phong vào session (PHP sẽ xử lý qua save_temp_user.php)
                    window.location.href = `thanh_toan.php?id_phong=${idPhong}&so_phong=${soPhong}&months=${months}&total=${total}`;
                } else {
                    alert('Lỗi khi lưu thông tin: ' + (data.message || 'Không xác định'));
                }
            })
            .catch(error => {
                alert('Lỗi khi lưu thông tin: ' + error.message);
            });
        }
    }

    function goBack() {
        window.history.back();
    }
</script>
</body>
</html>