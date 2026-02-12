<?php
// index.php
// Main landing page – no DB needed yet
error_reporting(E_ALL);
ini_set('display_errors', 1);

$keyword = "";
$location = "";
$searchMessage = "";

if (isset($_GET['keyword']) || isset($_GET['location'])) {
    $keyword = htmlspecialchars($_GET['keyword'] ?? "");
    $location = htmlspecialchars($_GET['location'] ?? "");

    if ($keyword == "" && $location == "") {
        $searchMessage = "Please enter job keyword or select a location.";
    } else {
        $searchMessage = "Showing results for
            <strong>$keyword</strong>
            in <strong>$location</strong>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Job Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f5f7fb;
            color: #333;
        }

        /* ===== HEADER ===== */
        header {
            background: #ffffff;
            padding: 15px 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .logo {
            font-size: 22px;
            font-weight: 700;
            color: #1e88e5;
        }

        nav a {
            margin-left: 25px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }

        nav a:hover {
            color: #1e88e5;
        }

        /* ===== HERO SECTION ===== */
        .hero {
            height: 700px;
            background: linear-gradient(
                rgba(30, 136, 229, 0.85),
                rgba(30, 136, 229, 0.85)
            ),
            url("https://images.unsplash.com/photo-1521737604893-d14cc237f11d") center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
        }

        .hero-content h1 {
            font-size: 42px;
            margin-bottom: 10px;
        }

        .hero-content p {
            font-size: 16px;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        /* ===== SEARCH BOX ===== */
        .search-box {
            background: #fff;
            padding: 15px;
            border-radius: 6px;
            display: flex;
            gap: 10px;
            max-width: 700px;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .search-box input,
        .search-box select {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            font-size: 14px;
        }

        .search-box button {
            padding: 12px 25px;
            background: #1e88e5;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
        }

        .search-box button:hover {
            background: #1565c0;
        }

        /* ===== STATS ===== */
        .stats {
            background: #ffffff;
            padding: 50px 60px;
            display: flex;
            justify-content: space-around;
            text-align: center;
        }

        .stat-box h2 {
            color: #1e88e5;
            font-size: 26px;
            margin-bottom: 5px;
        }

        .stat-box p {
            color: #666;
            font-size: 14px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            header {
                padding: 15px 20px;
            }

            .hero-content h1 {
                font-size: 30px;
            }

            .search-box {
                flex-direction: column;
            }

            .stats {
                flex-direction: column;
                gap: 25px;
            }
        }
    </style>
</head>
<body>

<!-- ===== HEADER ===== -->
<header>
    <div class="logo">ONLINE JOB PORTAL</div>
    <nav>
        <a href="auth/login.php">Login</a>
        <a href="auth/register.php">Register</a>
    </nav>
</header>

<!-- ===== HERO ===== -->
<section class="hero">
    <div class="hero-content">
        <h1>FIND YOUR DREAM JOB</h1>
        <p>Search for the latest jobs in top companies.</p>

        <form class="search-box" method="GET" action="index.php">
            <input type="text" name="keyword" placeholder="Job Title, Company or Keywords">
            <select name="location">
                <option value="">Location</option>
                <option>India</option>
                <option>Mumbai</option>
                <option>Pune</option>
                <option>Bangalore</option>
                <option>Remote</option>
            </select>
            <button type="submit">Search</button>
        </form>
	
	<?php if ($searchMessage != ""): ?>
    <div style="
        margin-top:15px;
        background:#ffffff;
        color:#333;
        padding:10px 15px;
        border-radius:4px;
        font-size:14px;
        box-shadow:0 5px 15px rgba(0,0,0,0.1);
    ">
        <?php echo $searchMessage; ?>
    </div>
<?php endif; ?>
  

  </div>
</section>

<!-- ===== STATS ===== -->
<section class="stats">
    <div class="stat-box">
        <h2>200+</h2>
        <p>Jobs Available</p>
    </div>
    <div class="stat-box">
        <h2>150+</h2>
        <p>Companies Registered</p>
    </div>
    <div class="stat-box">
        <h2>1000+</h2>
        <p>Active Users</p>
    </div>
</section>

<script>
    // Basic JS placeholder (can be extended later)
    console.log("Index page loaded successfully");
</script>

</body>
</html>

