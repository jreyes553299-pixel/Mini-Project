<?php
require_once '../includes/db.php';
require_once '../includes/admin_header.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_skill'])) {
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['skill_name']);
    $percent = (int) $_POST['proficiency_percentage'];

    if ($percent < 0 || $percent > 100) $percent = 100;

    if (empty($id)) {
        $stmt = $pdo->prepare("INSERT INTO skills (skill_name, proficiency_percentage) VALUES (?, ?)");
        $stmt->execute([$name, $percent]);
        $message = "<div class='alert alert-success' style='border-color:var(--accent-color); color:var(--text-primary); background:rgba(0, 229, 255, 0.1);'>&#10003; Skill mapped.</div>";
    } else {
        $stmt = $pdo->prepare("UPDATE skills SET skill_name = ?, proficiency_percentage = ? WHERE id = ?");
        $stmt->execute([$name, $percent, $id]);
        $message = "<div class='alert alert-success' style='border-color:var(--accent-color); color:var(--text-primary); background:rgba(0, 229, 255, 0.1);'>&#8635; Skill metrics updated.</div>";
    }
}

if (isset($_POST['delete_id'])) {
    $delId = (int)$_POST['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ?");
    $stmt->execute([$delId]);
    $message = "<div class='alert alert-secondary' style='background:transparent; border:1px solid var(--border-color); color:var(--text-secondary);'>&#10005; Skill purged.</div>";
}

$editData = ['id' => '', 'skill_name' => '', 'proficiency_percentage' => ''];
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM skills WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $row = $stmt->fetch();
    if ($row) $editData = $row;
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-color) !important;">
    <h1 class="h2">Skills</h1>
</div>

<?php if($message) echo $message; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="bento-card">
            <h5 class="mono-text text-uppercase mb-4 text-secondary">
                <?php echo empty($editData['id']) ? '+' : '&#9998;'; ?>
                <?php echo empty($editData['id']) ? 'Add New Skill' : 'Edit Skill'; ?>
            </h5>
            
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">

                <div class="mb-3">
                    <label class="form-label mono-text text-uppercase" style="font-size: 0.8em; color: var(--text-secondary);">Skill Name</label>
                    <input type="text" name="skill_name" class="form-control" placeholder="e.g. PHP 8, React, Docker" value="<?php echo htmlspecialchars($editData['skill_name']); ?>" required>
                </div>

                <div class="mb-4">
                    <label class="form-label d-flex justify-content-between mono-text text-uppercase" style="font-size: 0.8em; color: var(--text-secondary);">
                        <span>Skill Level</span>
                        <span id="rangeval" style="color:var(--accent-color);"><?php echo empty($editData['id']) ? '85' : $editData['proficiency_percentage']; ?>%</span>
                    </label>
                    <input type="range" name="proficiency_percentage" class="form-range custom-range" min="0" max="100" step="5" 
                           value="<?php echo empty($editData['id']) ? '85' : $editData['proficiency_percentage']; ?>" 
                           oninput="document.getElementById('rangeval').innerText = this.value + '%'" required>
                    <style>
                        .custom-range::-webkit-slider-thumb {
                            background: var(--accent-color);
                        }
                        .custom-range::-moz-range-thumb {
                            background: var(--accent-color);
                        }
                    </style>
                </div>

                <button type="submit" name="save_skill" class="btn btn-primary w-100">
                    <?php echo empty($editData['id']) ? 'Save Skill' : 'Update Skill'; ?>
                </button>
                
                <?php if(!empty($editData['id'])): ?>
                    <div class="text-center mt-3">
                        <a href="skills.php" class="mono-text text-danger" style="font-size: 0.8em; text-decoration: none;">Cancel</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="admin-table-container">
            <h5 class="mono-text text-uppercase mb-4 text-secondary">Skill List</h5>
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th width="30%">Skill</th>
                        <th width="40%">Level</th>
                        <th width="10%">%</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM skills ORDER BY skill_name ASC");
                    $skills = $stmt->fetchAll();
                    
                    if (empty($skills)): ?>
                        <tr><td colspan="4" class="text-center text-secondary py-4">No skills added.</td></tr>
                    <?php else:
                        foreach ($skills as $row): ?>
                            <tr>
                                <td class="fw-bold" style="color:var(--text-primary);"><?php echo htmlspecialchars($row['skill_name']); ?></td>
                                <td>
                                    <div class="skill-bar-container w-100">
                                        <div class="skill-bar-fill" style="width: <?php echo $row['proficiency_percentage']; ?>%;"></div>
                                    </div>
                                </td>
                                <td class="mono-text" style="color:var(--accent-color); font-size: 0.8em;">
                                    <?php echo $row['proficiency_percentage']; ?>%
                                </td>
                                <td class="text-end" style="white-space:nowrap;">
                                    <a href="skills.php?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary py-0 px-2 mono-text" style="font-size:0.8em; border-radius:2px;">Edit</a>
                                    
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Delete this skill?');">
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
