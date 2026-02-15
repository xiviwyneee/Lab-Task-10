<?php
include 'connection.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: journal.php");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM journal WHERE journal_id = $id");
$entry  = mysqli_fetch_assoc($result);

if (!$entry) {
    header("Location: journal.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($entry['title']) ?> — My Journal</title>
    <link rel="stylesheet" href="journal.css">
</head>
<body>

<div class="navbar">
    <h1>📓 My Journal</h1>
</div>

<div class="container">

    <a href="journal.php" class="btn btn-gray" style="margin-bottom: 20px; display: inline-block;">← Back</a>

    <div class="card">
        <div class="entry-header">
            <h2><?= htmlspecialchars($entry['title']) ?></h2>
            <div class="entry-dates">
                <span>📅 Date Added: <?= $entry['entry_date'] ?></span>
                <span>✏️ Last Updated: <?= $entry['updated_at'] ?></span>
            </div>
        </div>
        <div class="entry-content">
            <?= nl2br(htmlspecialchars($entry['content'])) ?>
        </div>
        <div class="entry-actions">
            <a href="journal.php?edit=<?= $entry['journal_id'] ?>" class="btn btn-yellow">Edit</a>
            <a href="journal.php?delete=<?= $entry['journal_id'] ?>" class="btn btn-red"
               onclick="return confirm('Delete this entry?')">Delete</a>
        </div>
    </div>

</div>
</body>
</html>