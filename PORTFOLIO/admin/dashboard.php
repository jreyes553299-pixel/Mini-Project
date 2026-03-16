<?php
require_once '../includes/db.php';
require_once '../includes/admin_header.php';

try {
    $projCount = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
    $skillCount = $pdo->query("SELECT COUNT(*) FROM skills")->fetchColumn();
    
    $msgStmt = $pdo->query("SELECT * FROM messages ORDER BY received_at DESC");
    $messages = $msgStmt->fetchAll();
    $unreadCount = 0;
    foreach($messages as $m) {
        if(!$m['is_read']) $unreadCount++;
    }

} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}

if(isset($_POST['delete_msg_id'])) {
    $delId = (int)$_POST['delete_msg_id'];
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = :id");
    $stmt->execute(['id' => $delId]);
    header("Location: dashboard.php");
    exit();
}

if(isset($_POST['read_msg_id'])) {
    $readId = (int)$_POST['read_msg_id'];
    $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = :id");
    $stmt->execute(['id' => $readId]);
    header("Location: dashboard.php");
    exit();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom" style="border-color: var(--border-color) !important;">
    <h1 class="h2">Dashboard</h1>
    <div class="mono-text text-secondary"><?php echo date('F j, Y'); ?></div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="bento-card text-center" style="padding: 1.5rem;">
            <div class="mono-text mb-2 text-uppercase text-secondary">Total Projects</div>
            <div class="display-4 font-weight-bold" style="color: var(--accent-color);"><?php echo $projCount; ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="bento-card text-center" style="padding: 1.5rem;">
            <div class="mono-text mb-2 text-uppercase text-secondary">Active Skills</div>
            <div class="display-4 font-weight-bold" style="color: var(--accent-color);"><?php echo $skillCount; ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="bento-card text-center" style="padding: 1.5rem; <?php if($unreadCount > 0) echo 'border-color: var(--accent-color);'; ?>">
            <div class="mono-text mb-2 text-uppercase text-secondary">Unread Messages</div>
            <div class="display-4 font-weight-bold" style="color: var(--text-primary);"><?php echo $unreadCount; ?></div>
        </div>
    </div>
</div>

<h3 class="h5 mb-3 mono-text text-uppercase text-secondary">Recent Messages</h3>
<div class="admin-table-container">
    <table class="table admin-table mb-0">
        <thead>
            <tr>
                <th>Status</th>
                <th>Sender</th>
                <th>Email</th>
                <th>Message</th>
                <th>Received</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($messages)): ?>
                <tr><td colspan="6" class="text-center py-4 text-secondary">No messages received yet.</td></tr>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <tr style="<?php echo (!$msg['is_read']) ? 'border-left: 3px solid var(--accent-color); background-color: rgba(0, 229, 255, 0.03);' : ''; ?>">
                        <td>
                            <?php if (!$msg['is_read']): ?>
                                <span class="badge" style="background-color: var(--accent-color); color: var(--bg-primary);">NEW</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Read</span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-bold"><?php echo htmlspecialchars($msg['sender_name']); ?></td>
                        <td class="mono-text" style="font-size:0.85em;"><a href="mailto:<?php echo htmlspecialchars($msg['sender_email']); ?>" style="color:var(--text-secondary);"><?php echo htmlspecialchars($msg['sender_email']); ?></a></td>
                        <td style="max-width:300px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            <?php echo htmlspecialchars($msg['message_body']); ?>
                        </td>
                        <td class="mono-text" style="font-size:0.85em;"><?php echo date('M j, g:i A', strtotime($msg['received_at'])); ?></td>
                        <td class="text-end" style="white-space:nowrap;">
                            <?php if (!$msg['is_read']): ?>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="read_msg_id" value="<?php echo $msg['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-primary py-0 px-2 mono-text" style="font-size:0.8em; border-radius:2px;">Mark Read</button>
                            </form>
                            <?php endif; ?>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Delete this message?');">
                                <input type="hidden" name="delete_msg_id" value="<?php echo $msg['id']; ?>">
                                <button type="submit" class="btn btn-sm text-danger py-0 px-2" style="background:transparent; border:none;">&#10005;</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/admin_footer.php'; ?>
