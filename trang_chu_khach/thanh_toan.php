<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .info-group { margin-bottom: 15px; }
        .info-group label { font-weight: bold; display: block; margin-bottom: 5px; }
        .info-group p { margin: 5px 0; }
        .total { font-weight: bold; color: #00cc00; margin: 15px 0; }
        .buttons { display: flex; gap: 10px; justify-content: flex-end; }
        button { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .back-btn { background-color: #757575; color: #fff; }
        .back-btn:hover { background-color: #555; }
        .pay-btn { background-color: #00cc00; color: #fff; }
        .pay-btn:hover { background-color: #009900; }
        .home-btn { background-color: #3498db; color: #fff; display: none; }
        .home-btn:hover { background-color: #2980b9; }
        .message { color: #006600; font-weight: bold; display: none; }
        .error { color: #cc0000; font-weight: bold; display: none; }
        .debug { color: #333; font-size: 12px; margin-top: 10px; background: #f0f0f0; padding: 10px; white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Thanh toán</h2>
        
        <div class="info-group">
            <label>Thông tin phòng:</label>
            <p>Phòng <?php echo htmlspecialchars($_GET['so_phong'] ?? 'N/A') ?> - Khu vực Hà Đông</p>
            <p>Giá: <?php echo isset($_GET['total']) ? number_format($_GET['total'] / $_GET['months'], 0, ',', '.') : 'N/A' ?> VNĐ/tháng</p>
            <p>Diện tích: 30m²</p>
            <p>Địa chỉ: 123 Đường Láng</p>
        </div>

        <div class="info-group">
            <label>Thông tin khách thuê:</label>
            <p><?php echo htmlspecialchars($_SESSION['temp_ho_ten'] ?? 'N/A') ?></p>
            <p><?php echo htmlspecialchars($_SESSION['temp_so_dien_thoai'] ?? 'N/A') ?></p>
            <p><?php echo htmlspecialchars($_SESSION['temp_email'] ?? 'N/A') ?></p>
        </div>

        <div class="info-group">
            <label>Thời gian thuê:</label>
            <p><?php echo isset($_GET['months']) ? $_GET['months'] . ' tháng' : 'N/A'; ?></p>
        </div>

        <div class="total">Tổng chi phí: <?php echo isset($_GET['total']) ? number_format($_GET['total'], 0, ',', '.') . ' VNĐ' : 'N/A'; ?></div>

        <div class="buttons">
            <button class="back-btn" onclick="goBack()">Quay lại</button>
            <button class="pay-btn" onclick="processPayment()">Thanh toán</button>
            <button class="home-btn" id="home-button" onclick="goHome()">Quay lại trang chủ</button>
        </div>
        <div id="message" class="message"></div>
        <div id="error" class="error"></div>
        <?php if (isset($_SESSION['last_error'])): ?>
            <div class="error">
                Lỗi: <?php echo htmlspecialchars($_SESSION['last_error']); ?>
            </div>
        <?php endif; ?>
       <!-- <div class="debug">
            Debug Session: <?php var_dump($_SESSION); ?>
        </div> !--> <!-- Thêm log để debug !-->
    </div>

    <script>
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                alert('Không có trang trước để quay lại!');
            }
        }

        function goHome() {
            window.location.href = 'khach_thue.php';
        }

        function processPayment() {
            if (confirm('Xác nhận thanh toán?')) {
                const roomId = <?php echo json_encode($_GET['id_phong'] ?? null); ?>;
                const total = <?php echo json_encode($_GET['total'] ?? null); ?>;
                const months = <?php echo json_encode($_GET['months'] ?? null); ?>;
                const hasSession = <?php echo json_encode(isset($_SESSION['temp_ho_ten']) && isset($_SESSION['temp_so_dien_thoai']) && isset($_SESSION['temp_email'])); ?>;

                console.log('Dữ liệu trước khi gọi:', { roomId, total, months, hasSession, session: <?php echo json_encode($_SESSION); ?> });

                if (!roomId || !total || !months || !hasSession) {
                    alert('Dữ liệu không hợp lệ! Kiểm tra console và debug trên trang. Session: ' + JSON.stringify(<?php echo json_encode($_SESSION); ?>));
                    return;
                }

                fetch('./update_status.php?id_phong=' + roomId + '&trang_thai=da-thue', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                })
                .then(response => {
                    console.log('Response update_status:', response);
                    if (!response.ok) throw new Error('Lỗi cập nhật trạng thái phòng: ' + response.status + ' - ' + response.statusText);
                    return response.json();
                })
                .then(data => {
                    console.log('Data update_status:', data);
                    if (!data.success) throw new Error(data.message || 'Cập nhật trạng thái phòng thất bại');
                    const formData = new URLSearchParams({
                        ho_ten: <?php echo json_encode($_SESSION['temp_ho_ten'] ?? ''); ?>,
                        so_dien_thoai: <?php echo json_encode($_SESSION['temp_so_dien_thoai'] ?? ''); ?>,
                        email: <?php echo json_encode($_SESSION['temp_email'] ?? ''); ?>,
                        id_phong: roomId
                    }).toString();
                    return fetch('./save_user.php', {
                        method: 'POST',
                        credentials: "include",
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: formData
                    });
                })
                .then(response => {
                    console.log('Response save_user:', response);
                    if (!response.ok) throw new Error('Lỗi lưu thông tin khách thuê: ' + response.status + ' - ' + response.statusText);
                    return response.json();
                })
                .then(userData => {
                    console.log('Data save_user:', userData);
                    const messageDiv = document.getElementById('message');
                    const errorDiv = document.getElementById('error');
                    const homeButton = document.getElementById('home-button');
                    if (userData.success) {
                        if (userData.temp_login) {
                            messageDiv.style.display = 'block';
                            messageDiv.textContent = `Tài khoản đã được tạo! Tên đăng nhập: ${userData.temp_login.ten_dang_nhap}, Mật khẩu: ${userData.temp_login.mat_khau}. Vui lòng lưu lại thông tin đăng nhập trước khi trở lại trang chủ. Xin cảm ơn.`;
                        } else {
                            messageDiv.style.display = 'block';
                            messageDiv.textContent = 'Thanh toán thành công! (Tài khoản đã tồn tại)';
                        }
                        homeButton.style.display = 'inline-block'; // Hiển thị nút quay lại trang chủ
                    } else {
                        errorDiv.style.display = 'block';
                        errorDiv.textContent = userData.message || 'Lưu thông tin khách thuê thất bại';
                        throw new Error(userData.message);
                    }
                })
                .catch(error => {
                    console.error('Lỗi chi tiết:', error);
                    const errorDiv = document.getElementById('error');
                    errorDiv.style.display = 'block';
                    errorDiv.textContent = 'Lỗi khi xử lý thanh toán: ' + error.message;
                    <?php $_SESSION['last_error'] = isset($error) ? $error->message : 'Không xác định'; ?>
                })
                .finally(() => {
                    // Không xóa session ở đây, để người dùng tự xử lý khi nhấn "Quay lại trang chủ"
                });
            }
        }
    </script>
</body>
</html>