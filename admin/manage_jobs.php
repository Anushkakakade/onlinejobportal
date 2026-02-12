<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../config/db.php");

// Admin check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Approve job
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE jobs SET status='approved' WHERE id=$id");
    header("Location: manage_jobs.php");
    exit();
}

// Reject job
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $conn->query("UPDATE jobs SET status='rejected' WHERE id=$id");
    header("Location: manage_jobs.php");
    exit();
}

// Search
$search = "";
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $jobs = $conn->query("SELECT * FROM jobs 
                          WHERE title LIKE '%$search%' 
                          OR company LIKE '%$search%'
                          ORDER BY created_at DESC");
} else {
    $jobs = $conn->query("SELECT * FROM jobs ORDER BY created_at DESC");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Jobs</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; }

.sidebar {
    width:220px;
    height:100vh;
    position:fixed;
    background:#2c3e50;
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

.search-box {
    display:flex;
    gap:10px;
}
</style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center text-white">Admin Panel</h4>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_jobs.php" style="background:#1e3799;">Manage Jobs</a>
    <a href="manage_users.php">Manage Users</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<!-- Content -->
<div class="content">
    <h3>Manage Job Listings</h3>

    <!-- Search -->
    <form method="GET" class="my-4 search-box">
        <input type="text" name="search" class="form-control"
               placeholder="Job Title, Company or Keywords"
               value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary">Search</button>
    </form>

    <!-- Job Table -->
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Company</th>
                        <th>Post Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                <?php while($row = $jobs->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['title'] ?></td>
                        <td><?= $row['company'] ?></td>
                        <td><?= date("Y-m-d", strtotime($row['created_at'])) ?></td>
                        <td>
                            <?php if($row['status'] == 'pending'): ?>
                                <span class="badge bg-warning">Pending</span>
				<?php elseif($row['status'] == 'approved'): ?>			    
				    <span class="badge bg-success">Approved</span>
			    	<?php else: ?>
    <span class="badge bg-danger">Rejected</span>
				
				<?php endif; ?>
                        </td>
                        <td>
                            <?php if($row['status'] == 'pending'): ?>
                                <a href="?approve=<?= $row['id'] ?>"
                                   class="btn btn-sm btn-success">
                                   Approve
				</a>
				<a href="?reject=<?= $row['id'] ?>" 
       class="btn btn-sm btn-danger">
       Reject
    </a>

<?php elseif($row['status'] == 'approved'): ?>
    <button class="btn btn-sm btn-success" disabled>
        Approved
    </button>
                            <?php else: ?>
    <button class="btn btn-danger btn-sm" disabled>Rejected</button>
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

