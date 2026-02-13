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

$admin_id = $_SESSION['user_id'];


//Fetch Admin Name
$admResult = $conn->query("SELECT name FROM users WHERE id = $admin_id LIMIT 1");
$admData = $admResult->fetch_assoc();
$admin_name = $admData['name'] ?? "Admin";

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
<link rel="stylesheet" href="admin_dashboard.css">

</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
<br>
    <h4 class="text-center text-white fw-bold fs-2 display-5 admin-title"><?= htmlspecialchars($admin_name) ?></h4>
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

    <h3 ">Manage Job Listings</h3>

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

