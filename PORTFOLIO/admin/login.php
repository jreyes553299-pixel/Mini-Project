<?php
session_start();
require_once '../includes/db.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id();
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Portfolio System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: var(--bg-primary);
        }
        .login-card {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .login-card .form-control {
            margin-bottom: 0;
            border-radius: var(--border-radius);
        }
        .login-title {
            font-family: var(--font-mono);
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 2rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
        .login-title span {
            color: var(--accent-color);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-title">Admin<span>.</span>Portal</div>
    
    <?php if(!empty($error)): ?>
        <div class="alert alert-danger" role="alert" style="background-color: rgba(220, 53, 69, 0.1); border-color: #dc3545; color: #ff6b6b; font-size: 0.9em; border-radius: var(--border-radius);">
            &#9888; <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label mono-text text-uppercase mb-1">Username</label>
            <input type="text" name="username" id="username" class="form-control mb-3" required autofocus>
        </div>
        
        <div class="mb-4">
            <label for="password" class="form-label mono-text text-uppercase mb-1">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary w-100">Login &rarr;</button>
    </form>
    
    <div class="text-center mt-4">
        <a href="../index.php" class="mono-text" style="font-size: 0.75rem;">&larr; Return to Public Site</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
