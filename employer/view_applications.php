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

// Fetch Jobs for dropdown
$jobs = $conn->query("SELECT id, title FROM jobs WHERE employer_id=$employer_id");

// Filter logic
$filter = "";
$selected_job = "";

if(isset($_GET['job_id']) && $_GET['job_id'] != ""){
    $selected_job = intval($_GET['job_id']);
    $filter = " AND jobs.id = $selected_job ";
}

// Fetch Applications
$applications = $conn->query("
    SELECT applications.*, 
           users.name AS applicant_name,
           jobs.title,
           jobs.company
    FROM applications
    JOIN users ON applications.user_id = users.id
    JOIN jobs ON applications.job_id = jobs.id
    WHERE jobs.employer_id = $employer_id
    $filter
    ORDER BY applications.applied_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>View Applications</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { font-family: Arial, sans-serif; background:#f4f6f9; }

.sidebar {
    width:250px;
    height:100vh;
    position:fixed;
    background:#1e3799;
    padding-top:20px;
}

.sidebar a {
    color:white;
    padding:12px;
    display:block;
    font-size:16px;
    margin-left:30px;
    text-decoration:none;
}

.sidebar a:hover { background:#4A69BD; }

.content {
    margin-left:280px;
    padding:20px;
}

.employer-title {
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
    text-align: center;
}

.dashboard-title {
    font-weight:800;
    font-size:36px;
}
</style>
</head>

<body>

<div class="sidebar"><br>
    <h4 class="text-center text-white fw-bold employer-title">
        <?= htmlspecialchars($employer_name) ?>
    </h4>
<br>
    <a href="dashboard.php">Dashboard</a>
    <a href="post_job.php">Post Job</a>
    <a href="view_applications.php">View Applications</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<div class="content">

<h1 class="dashboard-title">Employer Dashboard</h1>
<br>
<h3>View Applications</h3>
<hr>

<div class="card p-4">

<!-- FILTER -->
<form method="GET" class="mb-4">
<label class="fw-bold">Filter by Job Title:</label>
<select name="job_id" class="form-select w-50 mt-2" onchange="this.form.submit()">
    <option value="">--- Select Job Title ---</option>

    <?php while($job = $jobs->fetch_assoc()): ?>
        <option value="<?= $job['id'] ?>" 
            <?= ($selected_job == $job['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($job['title']) ?>
        </option>
    <?php endwhile; ?>
</select>
</form>

<!-- TABLE -->
<table class="table table-bordered table-striped">
<thead class="table-light">
<tr>
    <th>Applicant</th>
    <th>Job Title</th>
    <th>Applied Date</th>
    <th>Resume</th>
</tr>
</thead>

<tbody>
<?php if($applications->num_rows > 0): ?>
    <?php while($row = $applications->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['applicant_name']) ?></td>
        <td>
            <?= htmlspecialchars($row['title']) ?> -
            <?= htmlspecialchars($row['company']) ?>
        </td>
        <td><?= date("d M Y", strtotime($row['applied_at'])) ?></td>
        <td>
            <a href="../<?= $row['resume'] ?>" 
               target="_blank" 
               class="btn btn-primary btn-sm">
               View
            </a>
        </td>
    </tr>
    <?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="4" class="text-center">No applications found.</td>
</tr>
<?php endif; ?>
</tbody>
</table>

</div>
</div>

</body>
</html>

