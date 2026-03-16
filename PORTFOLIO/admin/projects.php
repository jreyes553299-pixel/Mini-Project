<?php
require_once '../includes/db.php';
require_once '../includes/admin_header.php';

$message = '';
$uploadDir = '../assets/img/projects/';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_project'])) {
    $id = $_POST['id'] ?? '';
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $link = trim($_POST['project_link']);
    $image_url = $_POST['current_image'] ?? '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", basename($_FILES['image']['name']));
        $destPath = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            if (!empty($image_url) && file_exists($uploadDir . $image_url)) {
                unlink($uploadDir . $image_url);
            }
            $image_url = $fileName;
        } else {
            $message = "<div class='alert alert-danger'>File upload failed.</div>";
        }
    }

    if (empty($message)) {
        if (empty($id)) {
            $stmt = $pdo->prepare("INSERT INTO projects (title, description, image_url, project_link) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $desc, $image_url, $link]);
            $message = "<div class='alert alert-success' style='border-color:var(--accent-color); color:var(--text-primary); background:rgba(0, 229, 255, 0.1);'>&#10003; Project secured to database.</div>";
        } else {
            $stmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, image_url = ?, project_link = ? WHERE id = ?");
            $stmt->execute([$title, $desc, $image_url, $link, $id]);
            $message = "<div class='alert alert-success' style='border-color:var(--accent-color); color:var(--text-primary); background:rgba(0, 229, 255, 0.1);'>&#8635; Project entity updated.</div>";
        }
    }
}

if (isset($_POST['delete_id'])) {
    $delId = (int)$_POST['delete_id'];
    
    $stmt = $pdo->prepare("SELECT image_url FROM projects WHERE id = ?");
    $stmt->execute([$delId]);
    $proj = $stmt->fetch();
    
    if($proj && !empty($proj['image_url']) && file_exists($uploadDir . $proj['image_url'])) {
        unlink($uploadDir . $proj['image_url']);
    }

    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$delId]);
    $message = "<div class='alert alert-secondary' style='background:transparent; border:1px solid var(--border-color); color:var(--text-secondary);'>&#10005; Project purged permanently.</div>";
}

$editData = ['id' => '', 'title' => '', 'description' => '', 'project_link' => '', 'image_url' => ''];
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $row = $stmt->fetch();
    if ($row) $editData = $row;
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-color) !important;">
    <h1 class="h2">Projects</h1>
</div>

<?php if($message) echo $message; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="bento-card">
            <h5 class="mono-text text-uppercase mb-4 text-secondary">
                <?php echo empty($editData['id']) ? '+' : '&#9998;'; ?>
                <?php echo empty($editData['id']) ? 'Add New Project' : 'Edit Project'; ?>
            </h5>
            
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
                <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($editData['image_url']); ?>">

                <div class="mb-3">
                    <label class="form-label mono-text text-uppercase" style="font-size: 0.8em; color: var(--text-secondary);">Project Title</label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($editData['title']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label mono-text text-uppercase" style="font-size: 0.8em; color: var(--text-secondary);">Brief Description</label>
                    <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($editData['description']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label mono-text text-uppercase" style="font-size: 0.8em; color: var(--text-secondary);">Live URL / Repo</label>
                    <input type="url" name="project_link" class="form-control" placeholder="https://..." value="<?php echo htmlspecialchars($editData['project_link']); ?>">
                </div>

                <div class="mb-4">
                    <label class="form-label mono-text text-uppercase" style="font-size: 0.8em; color: var(--text-secondary);">Upload Thumbnail</label>
                    <?php if(!empty($editData['image_url'])): ?>
                        <div class="mb-2">
                            <img src="<?php echo $uploadDir . htmlspecialchars($editData['image_url']); ?>" alt="Current" style="width: 100%; height: 120px; object-fit: cover; border-radius: var(--border-radius); border: 1px solid var(--border-color);">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="image" class="form-control" accept="image/*" <?php echo empty($editData['id']) ? 'required' : ''; ?>>
                </div>

                <button type="submit" name="save_project" class="btn btn-primary w-100">
                    <?php echo empty($editData['id']) ? 'Save Project' : 'Update Project'; ?>
                </button>
                
                <?php if(!empty($editData['id'])): ?>
                    <div class="text-center mt-3">
                        <a href="projects.php" class="mono-text text-danger" style="font-size: 0.8em; text-decoration: none;">Cancel</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="admin-table-container">
            <h5 class="mono-text text-uppercase mb-4 text-secondary">Project List</h5>
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th width="80">Img</th>
                        <th>Title</th>
                        <th>Link</th>
                        <th>Added On</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
                    $projects = $stmt->fetchAll();
                    
                    if (empty($projects)): ?>
                        <tr><td colspan="5" class="text-center text-secondary py-4">No projects found.</td></tr>
                    <?php else:
                        foreach ($projects as $row): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo $uploadDir . htmlspecialchars($row['image_url']); ?>" 
                                         alt="Thumb" 
                                         style="width: 45px; height: 45px; border-radius: var(--border-radius); object-fit: cover; border: 1px solid var(--border-color);">
                                </td>
                                <td class="fw-bold" style="color:var(--text-primary);"><?php echo htmlspecialchars($row['title']); ?></td>
                                <td>
                                    <?php if(!empty($row['project_link'])): ?>
                                        <a href="<?php echo htmlspecialchars($row['project_link']); ?>" target="_blank" style="color: var(--text-secondary);">&#8599;</a>
                                    <?php else: ?>
                                        <span class="text-secondary">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="mono-text" style="font-size:0.85em; color:var(--text-secondary);">
                                    <?php echo date('M j, Y', strtotime($row['created_at'])); ?>
                                </td>
                                <td class="text-end" style="white-space:nowrap;">
                                    <a href="projects.php?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary py-0 px-2 mono-text" style="font-size:0.8em; border-radius:2px;">Edit</a>
                                    
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Delete this project?');">
                                        <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-sm text-danger py-0 px-2" style="background:transparent; border:none;">&#10005;</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; 
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/admin_footer.php'; ?>
