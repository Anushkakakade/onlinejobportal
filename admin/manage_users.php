<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../config/db.php");

// Admin Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Block User
if(isset($_GET['block'])){
    $id = intval($_GET['block']);
    $conn->query("UPDATE users SET status='blocked' WHERE id=$id");
    header("Location: manage_users.php");
    exit();
}

// Unblock user
if(isset($_GET['activate'])){
    $id = intval($_GET['activate']);
    $conn->query("UPDATE users SET status='active' WHERE id=$id");
    header("Location: manage_users.php");
    exit();
}

// Search Logic
$search = "";
if(isset($_GET['search'])){
    $search = $conn->real_escape_string($_GET['search']);
    $users = $conn->query("SELECT * FROM users 
                           WHERE name LIKE '%$search%' 
                           OR email LIKE '%$search%' 
                           OR role LIKE '%$search%'
                           ORDER BY created_at DESC");
} else {
    $users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
}

// Counts
$employers = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='employer'")->fetch_assoc()['total'];
$jobseekers = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='jobseeker'")->fetch_assoc()['total'];
$totalUsers = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Users</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="admin_dashboard.css">

</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
<br>
    <h4 class="text-center text-white fw-bold fs-2 display-5 admin-title">Admin</h4>
<br>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_jobs.php">Manage Jobs</a>
    <a href="manage_users.php">Manage Users</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<!-- Content -->
<div class="content">
<h1 class="dashboard-title" >Admin Dashboard</h1>
   <br> 

    <h3>Manage Users</h3>

    <!-- Cards -->
    <div class="row my-4">
        <div class="col-md-4">
            <div class="card-box blue">
                <h5>Employers</h5>
                <h2><?= $employers ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-box green">
                <h5>Job Seekers</h5>
                <h2><?= $jobseekers ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-box orange">
                <h5>Total Users</h5>
                <h2><?= $totalUsers ?></h2>
            </div>
        </div>
    </div>

    <!-- Search -->
    <form method="GET" class="row mb-4">
        <div class="col-md-9">
            <input type="text" name="search" class="form-control"
                   placeholder="Name, Email, Role"
                   value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary w-100">Search</button>
        </div>
    </form>

    <!-- Table -->
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Date Registered</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                <?php while($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><?= ucfirst($row['role']) ?></td>
                        <td><?= date("Y-m-d", strtotime($row['created_at'])) ?></td>
                        <td>
                            <?php if($row['status']=='pending'): ?>
                                <span class="badge bg-warning">Pending</span>
                            <?php elseif($row['status']=='active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Blocked</span>
                            <?php endif; ?>
                        </td>
                        <td>
    <?php if($row['status']=='active'): ?>
        <a href="?block=<?= $row['id'] ?>" 
           class="btn btn-sm btn-danger"
           onclick="return confirm('Are you sure you want to block this user?')">
           Block
        </a>
    <?php else: ?>
        <a href="?activate=<?= $row['id'] ?>" 
           class="btn btn-sm btn-success"
           onclick="return confirm('Are you sure you want to unblock this user?')">
           Unblock
        </a>
    <?php endif; ?>
</td>

                    </tr>
                <?php endwhile; ?>

                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>

