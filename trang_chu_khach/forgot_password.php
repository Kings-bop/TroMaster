<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - TroMaster</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="chua">
    <div class="login-container">
        <h2>Quên mật khẩu</h2>
        <p>Nhập email của bạn để nhận liên kết đặt lại mật khẩu.</p>
        <form action="process_forgot_password.php" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit" class="login-btn">Gửi liên kết</button>
            <p class="back-to-login"><a href="login.php">Quay lại đăng nhập</a></p>
            <?php
            session_start();
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