<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - TroMaster</title>
    <link rel="stylesheet" href="style.css"> <!-- Đảm bảo có file style.css -->
    <script>
        // Hiển thị thông báo khi trang tải
        window.onload = function() {
            <?php if (isset($_SESSION['success'])): ?>
                if (confirm("<?php echo addslashes($_SESSION['success']); ?>")) {
                    window.location.href = "login.php"; // Chuyển hướng đến trang đăng nhập khi nhấn OK
                }
                <?php unset($_SESSION['success']); // Xóa thông báo sau khi sử dụng ?>
            <?php elseif (isset($_SESSION['error'])): ?>
                alert("<?php echo addslashes($_SESSION['error']); ?>");
                <?php unset($_SESSION['error']); // Xóa lỗi sau khi sử dụng ?>
            <?php endif; ?>
        };
    </script>
</head>
<body>
    <div class="chua">
    <div class="login-container">
        <h2>Đăng ký</h2>
        <form action="process_register.php" method="POST">
            <div class="form-group">
                <label for="ten_dang_nhap">Tên đăng nhập:</label>
                <input type="text" id="ten_dang_nhap" name="ten_dang_nhap" required>
            </div>
            <div class="form-group">
                <label for="mat_khau">Mật khẩu:</label>
                <input type="password" id="mat_khau" name="mat_khau" required>
            </div>
            <div class="form-group">
                <label for="ho_ten">Họ và tên:</label>
                <input type="text" id="ho_ten" name="ho_ten" required>
            </div>
            <div class="form-group">
                <label for="so_dien_thoai">Số điện thoại:</label>
                <input type="text" id="so_dien_thoai" name="so_dien_thoai" required>
            </div>
            <div class="form-group">
                <label for="so_cccd">Số CCCD:</label>
                <input type="text" id="so_cccd" name="so_cccd" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit" class="login-btn">Đăng ký</button>
            <p class="back-to-login"><a href="login.php">Đã có tài khoản? Đăng nhập</a></p>
        </form>
    </div>
    </div>
</body>
</html>