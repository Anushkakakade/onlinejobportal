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

// Fetch Employer Name
$empResult = $conn->query("SELECT name FROM users WHERE id = $employer_id LIMIT 1");
$empData = $empResult->fetch_assoc();
$employer_name = $empData['name'] ?? "Employer";

// POST NEW JOB
if(isset($_POST['post_job'])){
    $title = $conn->real_escape_string($_POST['title']);
    $company = $conn->real_escape_string($_POST['company']);
    $location = $conn->real_escape_string($_POST['location']);
    $salary = $conn->real_escape_string($_POST['salary']);
    $description = $conn->real_escape_string($_POST['description']);

    $conn->query("INSERT INTO jobs (employer_id,title,company,location,salary,description,status)
                  VALUES ($employer_id,'$title','$company','$location','$salary','$description','pending')");

    header("Location: post_job.php");
    exit();
}

// EDIT JOB (Load data)
$editData = null;
if(isset($_GET['edit'])){
    $edit_id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM jobs
                            WHERE id=$edit_id
                            AND employer_id=$employer_id");
    $editData = $result->fetch_assoc();
}

// UPDATE JOB
if(isset($_POST['update_job'])){
    $id = intval($_POST['job_id']);
    $title = $conn->real_escape_string($_POST['title']);
    $company = $conn->real_escape_string($_POST['company']);
    $location = $conn->real_escape_string($_POST['location']);
    $salary = $conn->real_escape_string($_POST['salary']);
    $description = $conn->real_escape_string($_POST['description']);

    $conn->query("UPDATE jobs SET 
        title='$title',
        company='$company',
        location='$location',
        salary='$salary',
	description='$description',
	status='pending',
	reposted=0
        WHERE id=$id AND employer_id=$employer_id");

    header("Location: post_job.php");
    exit();
}
// DELETE JOB
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM jobs WHERE id=$id AND employer_id=$employer_id");
    header("Location: post_job.php");
    exit();
}

// REPOST JOB (change status to pending)
if(isset($_GET['repost'])){
    $id = intval($_GET['repost']);
    $conn->query("UPDATE jobs SET status='pending', reposted=1 WHERE id=$id AND employer_id=$employer_id");
    header("Location: post_job.php");
    exit();
}

// FETCH JOBS
$jobs = $conn->query("SELECT * FROM jobs 
                      WHERE employer_id=$employer_id
                      ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Post Job</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="employer_dashboard.css">

</head>

<body>

<div class="sidebar"><br>
    <h4 class="text-center text-white fw-bold  employer-title"><?= htmlspecialchars($employer_name) ?></h4>
<br>
    <a href="dashboard.php">Dashboard</a>
    <a href="post_job.php">Post Job</a>
    <a href="view_applications.php">View Applications</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<div class="content">
<h1 class="dashboard-title" >Employer Dashboard</h1>
<br>
<h3>Post a New Job</h3>
<hr>

<div class="card mb-4">
<div class="card-body">

<form method="POST">
<?php if($editData): ?>
<input type="hidden" name="job_id" value="<?= $editData['id'] ?>">
<?php endif; ?>

<div class="row mb-3">
<div class="col-md-6">
<label>Job Title</label>
<input type="text" name="title" class="form-control" value="<?= $editData['title'] ?? '' ?>" required>
</div>

<div class="col-md-6">
<label>Company</label>
<input type="text" name="company" class="form-control" value="<?= $editData['company'] ?? '' ?>" required>
</div>
</div>

<div class="row mb-3">
<div class="col-md-6">
<label>Location</label>
<input type="text" name="location" class="form-control" value="<?= $editData['location'] ?? '' ?>" required>
</div>

<div class="col-md-6">
<label>Salary</label>
<input type="text" name="salary" class="form-control" value="<?= $editData['salary'] ?? '' ?>">
</div>
</div>

<div class="mb-3">
<label>Job Description</label>
<textarea name="description" class="form-control" rows="4"><?= $editData['description'] ?? '' ?></textarea>
</div>

<?php if($editData): ?>
    <button name="update_job" class="btn btn-warning">Update Job</button>
<?php else: ?>
    <button name="post_job" class="btn btn-primary">Post Job</button>
<?php endif; ?>


<button type="reset" class="btn btn-secondary">Reset</button>

</form>

</div>
</div>

<h4>Your Job Listings</h4>
<hr>

<?php while($row=$jobs->fetch_assoc()): ?>
<div class="d-flex justify-content-between align-items-center border p-3 mb-2 rounded bg-white">

<div>
<strong><?= htmlspecialchars($row['title']) ?></strong> - 
<?= htmlspecialchars($row['company']) ?>
<br>
<small>
<?= $row['location'] ?> | <?= $row['salary'] ?> |
Status:
<?php if($row['status']=='pending' && $row['reposted']==1): ?>
<span class="badge bg-info">Reposted</span>
<?php elseif($row['status']=='pending'): ?>
<span class="badge bg-warning">Pending</span>
<?php elseif($row['status']=='approved'): ?>
<span class="badge bg-success">Approved</span>
<?php else: ?>
<span class="badge bg-danger">Rejected</span>
<?php endif; ?>
</small>
</div>

<div>

<!-- EDIT -->
<a href="?edit=<?= $row['id'] ?>" 
class="btn btn-sm btn-warning">
Edit
</a>

<!-- DELETE -->
<a href="?delete=<?= $row['id'] ?>" 
class="btn btn-sm btn-danger"
onclick="return confirm('Delete this job?')">
Delete
</a>

<!-- REPOST (Only if Rejected and not already reposted) -->
<?php if($row['status']=='rejected'): ?>
<a href="?repost=<?= $row['id'] ?>" 
class="btn btn-sm btn-primary"
onclick="return confirm('Repost this job?')">
Repost
</a>
<?php endif; ?>

</div>

</div>
<?php endwhile; ?>

</div>

</body>
</html>

