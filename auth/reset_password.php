<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once("../config/db.php");

date_default_timezone_set("Asia/Kolkata");

if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("Invalid access");
}

$token = mysqli_real_escape_string($conn, $_GET['token']);

$query = mysqli_query($conn,
    "SELECT * FROM users 
     WHERE reset_token='$token' 
     LIMIT 1"
);

if (mysqli_num_rows($query) != 1) {
    die("Invalid Token");
}

$user = mysqli_fetch_assoc($query);

// Check expiry using PHP (more reliable)
if (strtotime($user['reset_expire']) < time()) {
    die("Token Expired");
}


$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } else {

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        mysqli_query($conn,
            "UPDATE users 
             SET password='$hashed',
                 reset_token=NULL,
                 reset_expire=NULL
             WHERE id=".$user['id']
        );

        $success = "Password reset successful!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Reset Password</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    height:100vh;
    background: linear-gradient(rgba(0,0,0,0.4),rgba(0,0,0,0.4)),
    url("https://images.unsplash.com/photo-1521737604893-d14cc237f11d") center/cover no-repeat;
}
.card{
    border-radius:12px;
}
</style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
    <div class="card p-4 shadow" style="width:420px;">

        <h4 class="text-center mb-4">Reset Password</h4>

        <?php if($error!=""): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if($success!=""): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
                <br><br>
                <a href="login.php" class="btn btn-success btn-sm">
                    Go to Login
                </a>
            </div>
        <?php else: ?>

        <form method="POST">

            <div class="mb-3">
                <label>New Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Reset Password
            </button>

        </form>

        <?php endif; ?>

    </div>
</div>

</body>
</html>

