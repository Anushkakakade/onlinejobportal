<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once("../config/db.php");

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Validation
    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } else {

        // Check if email exists
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");

        if (mysqli_num_rows($check) > 0) {
            $error = "Email already registered!";
        } else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert = mysqli_query($conn,
                "INSERT INTO users (name, email, password, role) 
                 VALUES ('$name', '$email', '$hashed_password', '$role')"
            );

            if ($insert) {
                $success = "Registration successful! You can login now.";
            } else {
                $error = "Something went wrong!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Register - Online Job Portal</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    height:100vh;
    background: linear-gradient(rgba(0,0,0,0.4),rgba(0,0,0,0.4)),
    url("https://images.unsplash.com/photo-1504384308090-c894fdcc538d") center/cover no-repeat;
}

.header{
    background:white;
    padding:15px 30px;
    font-weight:bold;
    font-size:24px;
    color:#2c5aa0;
}

.card{
    border-radius:10px;
}

.btn-primary{
    background:#2c5aa0;
    border:none;
}
</style>
</head>

<body>

<div class="header">
    ONLINE JOB PORTAL
</div>

<div class="container d-flex justify-content-center align-items-center" style="height:90vh;">

    <div class="card p-4 shadow" style="width:420px;">
        <h3 class="text-center mb-4">Create a New Account</h3>

        <?php if($error!=""): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if($success!=""): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Register As:</label>
                <select name="role" class="form-select" required>
                    <option value="">-- Select Role --</option>
                    <option value="jobseeker">Job Seeker</option>
                    <option value="employer">Employer</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Register
            </button>

        </form>

        <div class="text-center mt-3">
            Already have an account?
            <a href="login.php">Login</a>
        </div>

    </div>

</div>

</body>
</html>

