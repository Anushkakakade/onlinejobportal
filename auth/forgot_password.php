<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once("../config/db.php");

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $email = mysqli_real_escape_string($conn, $email);

    $check = mysqli_query($conn,
        "SELECT * FROM users WHERE email='$email' AND status=1 LIMIT 1"
    );

    if (mysqli_num_rows($check) == 1) {

        // Generate token
        $token = bin2hex(random_bytes(32));
        date_default_timezone_set("Asia/Kolkata");

	$expire = date("Y-m-d H:i:s", strtotime("+1 hour"));


        mysqli_query($conn,
            "UPDATE users
             SET reset_token='$token',
                 reset_expire='$expire'
             WHERE email='$email'"
        );

        $reset_link = "http://localhost/onlinejobportal/auth/reset_password.php?token=$token";

        $message = "Reset link generated successfully.";
    } else {
        $error = "Email not found!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>

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

        <h4 class="text-center mb-4">Forgot Password</h4>

        <?php if($error!=""): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if($message!=""): ?>
            <div class="alert alert-success">
                <?php echo $message; ?>
                <br><br>
                <a href="<?php echo $reset_link; ?>" class="btn btn-primary btn-sm">
                    Click Here to Reset Password
                </a>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Send Reset Link
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="login.php">Back to Login</a>
        </div>

    </div>
</div>

</body>
</html>

