<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - TroMaster</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="chua">
    <div class="login-container">
        <h2>Đăng nhập</h2>
        <form action="process_login.php" method="POST">
            <div class="form-group">
                <label for="ten_dang_nhap">Tên đăng nhập hoặc Email:</label>
                <input type="text" id="ten_dang_nhap" name="ten_dang_nhap" required>
            </div>
            <div class="form-group">
                <label for="mat_khau">Mật khẩu:</label>
                <input type="password" id="mat_khau" name="mat_khau" required>
            </div>
            <button type="submit" class="login-btn">Đăng nhập</button>
            <p class="forgot-password"><a href="forgot_password.php">Quên mật khẩu?</a></p>
            <p class="back-to-login"><a href="register.php">Chưa có tài khoản? Đăng ký</a></p>
            <button class="back-button1" onclick="window.location.href='khach_thue.php'">Quay lại trang chủ</button>
            <?php
            if (isset($_SESSION['error'])) {
                echo '<p class="error">' . $_SESSION['error'] . '</p>';
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<p class="success">' . $_SESSION['success'] . '</p>';
                unset($_SESSION['success']);
            }
            ?>
        </form>
    </div>
    </div>
</body>
</html>