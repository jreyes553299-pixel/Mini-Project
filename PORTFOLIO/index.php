<?php
require_once 'includes/db.php';

$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $msg_body = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($msg_body)) {
        $message = "All fields are required.";
        $message_type = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $message_type = "danger";
    } else {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_name, sender_email, message_body) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $email, $msg_body])) {
            $message = "Message sent successfully! I'll be in touch soon.";
            $message_type = "success";
        } else {
            $message = "Failed to send message. Please try again later.";
            $message_type = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Software Engineer Portfolio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.1;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 2.5rem;
            max-width: 600px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#home">&lt;DevPortfolio /&gt;</a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mono-text text-uppercase" style="font-size: 0.9em;">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#projects">Work</a></li>
                    <li class="nav-item"><a class="nav-link" href="#skills">Skills</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    <li class="nav-item ms-lg-3"><a class="nav-link" href="admin/login.php"
                            style="color: var(--accent-color);">&#9881; SysAdmin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 mb-5 mb-lg-0">
                    <div class="mono-text mb-3"
                        style="color: var(--accent-color); letter-spacing: 0.1em; text-transform: uppercase;">
                        Hello, World.
                    </div>
                    <h1 class="hero-title">I'm Jules Henri Reyes.</h1>
                    <p class="hero-subtitle mb-2" style="font-weight: 500; color: var(--text-primary);">
                        19-year-old BSIT Student
                    </p>
                    <p class="hero-subtitle">
                        Currently in my 2nd year at the University of Mindanao Davao. I am passionate about full-stack
                        development, specializing in building functional websites and web applications.
                    </p>
                    <div class="mt-4">
                        <a href="#projects" class="btn btn-primary me-3">View Projects</a>
                        <a href="#contact" class="btn btn-outline-primary">Contact Me</a>
                    </div>
                </div>
                <div class="col-lg-5 text-center px-4 px-lg-5">
                    <div class="portrait-placeholder">
                        <img src="assets/img/profile.png" alt="Jules Henri Reyes" class="img-fluid hero-portrait">
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section id="projects" style="background-color: var(--bg-secondary);">
        <div class="container">
            <h2 class="section-title">My Projects<span style="color: var(--accent-color);">.</span></h2>
            <div class="bento-grid">
                <?php
                $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
                $projects = $stmt->fetchAll();
                if (empty($projects)): ?>
                    <div class="col-12 text-center text-secondary py-5">
                        <span style="font-size: 3rem;">&#128194;</span>
                        <p class="mono-text">No projects added yet.</p>
                    </div>
                <?php else:
                    foreach ($projects as $proj): ?>
                        <div class="bento-card">
                            <img src="assets/img/projects/<?php echo htmlspecialchars($proj['image_url']); ?>"
                                alt="<?php echo htmlspecialchars($proj['title']); ?>">
                            <h3 class="h5 mb-2"><?php echo htmlspecialchars($proj['title']); ?></h3>
                            <p class="text-secondary mb-4" style="font-size: 0.9em;">
                                <?php echo htmlspecialchars($proj['description']); ?>
                            </p>
                            <?php if (!empty($proj['project_link'])): ?>
                                <a href="<?php echo htmlspecialchars($proj['project_link']); ?>" target="_blank" class="mono-text"
                                    style="font-size: 0.8em; text-transform: uppercase;">
                                    View Project &rarr;
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach;
                endif; ?>
            </div>
        </div>
    </section>

    <section id="skills">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-5 mb-5 mb-lg-0">
                    <h2 class="section-title">My Skills<span style="color: var(--accent-color);">.</span></h2>
                    <p class="text-secondary">Here are the technologies and tools I use to build my projects.</p>
                </div>
                <div class="col-lg-6">
                    <div class="bento-card">
                        <?php
                        $stmt = $pdo->query("SELECT * FROM skills ORDER BY proficiency_percentage DESC, skill_name ASC");
                        $skills = $stmt->fetchAll();
                        if (empty($skills)): ?>
                            <p class="text-secondary mono-text mb-0 text-center py-4">No skills added yet.</p>
                        <?php else: ?>
                            <div class="row g-4">
                                <?php foreach ($skills as $skill): ?>
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-between mono-text mb-1" style="font-size: 0.85em;">
                                            <span
                                                style="color: var(--text-primary);"><?php echo htmlspecialchars($skill['skill_name']); ?></span>
                                            <span
                                                style="color: var(--accent-color);"><?php echo $skill['proficiency_percentage']; ?>%</span>
                                        </div>
                                        <div class="skill-bar-container w-100">
                                            <div class="skill-bar-fill"
                                                style="width: <?php echo $skill['proficiency_percentage']; ?>%;"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" style="background-color: var(--bg-secondary); border-top: 1px solid var(--border-color);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="text-center mb-5">
                        <h2 class="section-title mb-3">Contact Me<span style="color: var(--accent-color);">.</span></h2>
                        <p class="text-secondary">Let's work together.</p>
                    </div>
                    <div class="bento-card">
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $message_type; ?> mono-text mb-4"
                                style="background: transparent; border: 1px solid <?php echo ($message_type == 'success') ? 'var(--accent-color)' : '#dc3545'; ?>; color: <?php echo ($message_type == 'success') ? 'var(--accent-color)' : '#ff6b6b'; ?>;">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>
                        <form class="contact-form" action="#contact" method="POST">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="mono-text mb-2 text-uppercase text-secondary"
                                        style="font-size: 0.8em;">Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="mono-text mb-2 text-uppercase text-secondary"
                                        style="font-size: 0.8em;">Email Address</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="mono-text mb-2 text-uppercase text-secondary"
                                    style="font-size: 0.8em;">Message</label>
                                <textarea name="message" class="form-control" rows="5" required></textarea>
                            </div>
                            <button type="submit" name="send_message" class="btn btn-primary w-100 mt-3">Send Message
                                &raquo;</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="text-center py-4" style="border-top: 1px solid var(--border-color);">
        <div class="container mono-text" style="font-size: 0.8em; color: var(--text-secondary);">
            &copy; <?php echo date('Y'); ?> DevPortfolio. All rights reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>