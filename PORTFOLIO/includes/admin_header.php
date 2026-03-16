<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: /MINI_PROJ/PORTFOLIO/admin/login.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Portfolio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="/MINI_PROJ/PORTFOLIO/assets/css/style.css">
    <style>
        .admin-sidebar {
            background-color: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            min-height: 100vh;
        }
        .admin-nav-link {
            color: var(--text-secondary);
            font-family: var(--font-mono);
            padding: 1rem 1.5rem;
            display: block;
            border-left: 3px solid transparent;
            transition: all var(--transition-speed) ease;
        }
        .admin-nav-link:hover, .admin-nav-link.active {
            background-color: var(--bg-tertiary);
            color: var(--accent-color);
            border-left-color: var(--accent-color);
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block admin-sidebar collapse">
            <div class="position-sticky pt-4">
                <div class="px-4 mb-5">
                    <h5 class="mono-text text-uppercase mb-0" style="color: var(--accent-color);">Admin Panel</h5>
                    <small class="text-secondary">Logged in as <?php echo htmlspecialchars($_SESSION['admin_username']); ?></small>
                </div>
                
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="admin-nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                            &#8962; Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="admin-nav-link <?php echo ($current_page == 'projects.php') ? 'active' : ''; ?>" href="projects.php">
                            &#9638; Projects
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="admin-nav-link <?php echo ($current_page == 'skills.php') ? 'active' : ''; ?>" href="skills.php">
                            &#9733; Skills
                        </a>
                    </li>
                </ul>

                <hr class="mx-3 mt-5" style="border-color: var(--border-color);">
                
                <ul class="nav flex-column mb-2">
                    <li class="nav-item">
                        <a class="admin-nav-link text-danger" href="logout.php">
                            &#10140; Logout
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <a class="admin-nav-link" href="../index.php" target="_blank" style="font-size: 0.8em;">
                            &#8599; View Site
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4 pb-5">
