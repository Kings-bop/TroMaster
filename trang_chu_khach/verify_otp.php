<?php
session_start();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác minh OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        p {
            text-align: center;
            color: #666;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #ff5e00;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #ff5e00;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #ff5e00;
            text-decoration: none;
        }
        .error { color: red; text-align: center; }
        .success { color: green; text-align: center; }
    </style>
</head>
<body>
    <h2>Xác minh OTP</h2>
    <p>Nhập mã OTP đã được gửi đến email của bạn.</p>

    <form action="process_verify_otp.php" method="POST">
        <div class="form-group">
            <label for="otp">Mã OTP:</label>
            <input type="text" id="otp" name="otp" required maxlength="6" pattern="\d{6}" title="Mã OTP là 6 chữ số">
        </div>
        <button type="submit">Xác minh</button>
        <a href="login.php">Quay lại đăng nhập</a>
    </form>

    <?php
    if (isset($_SESSION['error'])) {
        echo '<p class="error">' . htmlspecialchars($_SESSION['error']) . '</p>';
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo '<p class="success">' . htmlspecialchars($_SESSION['success']) . '</p>';
        unset($_SESSION['success']);
    }
    ?>
</body>
</html>