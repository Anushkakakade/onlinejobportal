<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../config/db.php");

// Check Admin Login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Approve Job
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE jobs SET status='approved' WHERE id=$id AND status='pending'");
    header("Location: dashboard.php");
    exit();
}

// Reject Job 
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $conn->query("UPDATE jobs SET status='rejected' WHERE id=$id AND status='pending'");
    header("Location: dashboard.php");
    exit();
}

// Counts
$pending = $conn->query("SELECT COUNT(*) as total FROM jobs WHERE status='pending'")->fetch_assoc()['total'];
$approved = $conn->query("SELECT COUNT(*) as total FROM jobs WHERE status='approved'")->fetch_assoc()['total'];
$users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];

// Get Pending Jobs
$jobs = $conn->query("SELECT * FROM jobs WHERE status='pending' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background:#f4f6f9; }
.sidebar {
    width:220px;
    height:100vh;
    position:fixed;
    background:#1e3799;
    padding-top:20px;
}
.sidebar a {
    color:white;
    padding:12px;
    display:block;
    text-decoration:none;
}
.sidebar a:hover { background:#34495e; }

.content {
    margin-left:230px;
    padding:20px;
}

.card-box {
    color:white;
    padding:20px;
    border-radius:8px;
}

.pending { background:#e74c3c; }
.approved { background:#1abc9c; }
.users { background:#f39c12; }
</style>
</head>

<body>

<div class="sidebar">
    <h4 class="text-center text-white">Admin Panel</h4>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_jobs.php">Manage Jobs</a>
    <a href="manage_users.php">Manage Users</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<div class="content">
    <h3>Admin Dashboard</h3>

    <div class="row my-4">
        <div class="col-md-4">
            <div class="card-box pending">
                <h5>Pending Jobs</h5>
                <h2><?= $pending ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-box approved">
                <h5>Approved Jobs</h5>
                <h2><?= $approved ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-box users">
                <h5>Total Users</h5>
                <h2><?= $users ?></h2>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <strong>Manage Job Listings</strong>
        </div>
        <div class="card-body">
            <?php while($row = $jobs->fetch_assoc()): ?>
                <div class="d-flex justify-content-between align-items-center border p-3 mb-2 rounded">
                    <div>
                        <strong><?= $row['title'] ?></strong> - <?= $row['company'] ?>
                        <br>
                        <small><?= $row['location'] ?> | <?= $row['salary'] ?></small>
                    </div>
                    <div>
                        <a href="?approve=<?= $row['id'] ?>" class="btn btn-success btn-sm">Approve</a>
                        <a href="?reject=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Reject</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</div>

</body>
</html>

