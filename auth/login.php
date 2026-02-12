<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once("../config/db.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email' AND status=1 LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            header("Location: ../index.php");
            exit();

        } else {
            $error = "Invalid password!";
        }

    } else {
        $error = "Email not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    height: 100vh;
    background: linear-gradient(rgba(0,0,0,0.4),rgba(0,0,0,0.4)),
    url("https://images.unsplash.com/photo-1521737604893-d14cc237f11d") center/cover no-repeat;
}

.portal-title {
    font-weight: 700;
    color: #1e88e5;
}

.login-card {
    border-radius: 10px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}

.btn-primary {
    background-color: #1e88e5;
    border: none;
}

.btn-primary:hover {
    background-color: #1565c0;
}
</style>
</head>
<body>

<!-- Header -->
<div class="bg-white py-3 px-5">
    <h4 class="portal-title">ONLINE JOB PORTAL</h4>
</div>

<!-- Centered Login -->
<div class="container d-flex justify-content-center align-items-center" style="height:85vh;">
    <div class="card login-card p-4" style="width: 420px;">
        <h3 class="text-center mb-4">Login to Your Account</h3>

        <?php if($error != ""): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Email Address" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>

            <div class="d-flex justify-content-between mb-3">
                <div>
                    <input type="checkbox"> Remember Me
                </div>
                <a href="forgot_password.php">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>

        </form>

        <p class="text-center mt-3">
            Don't have an account?
            <a href="register.php">Register</a>
        <br><br>	
            <a href="../index.php">Back to Home</a>
        </p>

    </div>
</div>

</body>
</html>

