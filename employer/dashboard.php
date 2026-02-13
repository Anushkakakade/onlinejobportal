<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
session_start();
include("../config/db.php");

// Check Employer Login
if(!isset($_SESSION['user_id']) || $_SESSION['role']!='employer'){
    header("Location: ../auth/login.php");
    exit();
}

$employer_id = $_SESSION['user_id'];

// POST JOB
if(isset($_POST['post_job'])){
    $title = $conn->real_escape_string($_POST['title']);
    $company = $conn->real_escape_string($_POST['company']);
    $location = $conn->real_escape_string($_POST['location']);
    $salary = $conn->real_escape_string($_POST['salary']);
    $description = $conn->real_escape_string($_POST['description']);

    $conn->query("INSERT INTO jobs (employer_id,title,company,location,salary,description,status)
                  VALUES ($employer_id,'$title','$company','$location','$salary','$description','pending')");
}

// DELETE JOB
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM jobs WHERE id=$id AND employer_id=$employer_id");
    header("Location: dashboard.php");
    exit();
}

// FETCH JOBS OF THIS EMPLOYER
$jobs = $conn->query("SELECT * FROM jobs 
                      WHERE employer_id=$employer_id
                      ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Employer Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="employer_dashboard.css">
</head>

<body>

<div class="sidebar"><br>
    <h4 class="text-center text-white fw-bold  employer-title"><?= htmlspecialchars($_SESSION['name']) ?></h4>
 <br>   <a href="dashboard.php" >Dashboard</a>
    <a href="post_job.php">Post Job</a>
    <a href="view_applications.php">View Applications</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<div class="content">
<h1 class="dashboard-title" >Employer Dashboard</h1>
   <br>


<div class="card mb-4">
<div class="card-header">Post a New Job</div>
<div class="card-body">
<form method="POST">
<div class="mb-2">
<label>Job Title</label>
<input type="text" name="title" class="form-control" required>
</div>

<div class="mb-2">
<label>Company</label>
<input type="text" name="company" class="form-control" required>
</div>

<div class="mb-2">
<label>Location</label>
<input type="text" name="location" class="form-control" required>
</div>

<div class="mb-2">
<label>Salary</label>
<input type="text" name="salary" class="form-control">
</div>

<div class="mb-2">
<label>Job Description</label>
<textarea name="description" class="form-control" rows="3"></textarea>
</div>

<button name="post_job" class="btn btn-primary">Post Job</button>
</form>
</div>
</div>

<div class="card">
<div class="card-header">Your Job Listings</div>
<div class="card-body">

<?php while($row=$jobs->fetch_assoc()): ?>
<div class="d-flex justify-content-between align-items-center border p-3 mb-2 rounded">

<div>
<strong><?= htmlspecialchars($row['title']) ?></strong> -
<?= htmlspecialchars($row['company']) ?>
<br>
<small>
<?= $row['location'] ?> |
<?= $row['salary'] ?> |
Status:
<?php if($row['status']=='pending'): ?>
<span class="badge bg-warning">Pending</span>
<?php elseif($row['status']=='approved'): ?>
<span class="badge bg-success">Approved</span>
<?php else: ?>
<span class="badge bg-danger">Rejected</span>
<?php endif; ?>
</small>
</div>

<div>
<a href="?delete=<?= $row['id'] ?>" 
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this job?')">
Delete
</a>
</div>

</div>
<?php endwhile; ?>

</div>
</div>

</div>
</body>
</html>

