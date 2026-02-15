<?php
include 'connection.php';

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM journal WHERE journal_id = $id");
    header("Location: journal.php");
    exit;
}

// ADD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $title   = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    mysqli_query($conn, "INSERT INTO journal (title, content) VALUES ('$title', '$content')");
    header("Location: journal.php");
    exit;
}

// UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id         = (int)$_POST['journal_id'];
    $title      = mysqli_real_escape_string($conn, $_POST['title']);
    $content    = mysqli_real_escape_string($conn, $_POST['content']);
    $entry_date = mysqli_real_escape_string($conn, $_POST['entry_date']);
    mysqli_query($conn, "UPDATE journal SET title='$title', content='$content', entry_date='$entry_date' WHERE journal_id=$id");
    header("Location: journal.php");
    exit;
}

// FETCH entry to edit
$edit_entry = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM journal WHERE journal_id = $id");
    $edit_entry = mysqli_fetch_assoc($result);
}

// READ all entries
$entries = mysqli_query($conn, "SELECT * FROM journal ORDER BY entry_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Journal</title>
    <link rel="stylesheet" href="journal.css">
</head>
<body>

    <div class="navbar">
        <h1>📓 My Journal</h1>
    </div>

<div class="container">

    <div class="card">
        <h5><?= $edit_entry ? 'Edit Entry' : 'Add New Entry' ?></h5>
        <form method="POST">
            <?php if ($edit_entry): ?>
                <input type="hidden" name="journal_id" value="<?= $edit_entry['journal_id'] ?>">
            <?php endif; ?>
            <div class="field">
                <label>Title</label>
                <input type="text" name="title" required
                       value="<?= htmlspecialchars($edit_entry['title'] ?? '') ?>">
            </div>
            <?php if ($edit_entry): ?>
            <div class="field">
                <label>Date</label>
                <input type="datetime-local" name="entry_date" required
                       value="<?= date('Y-m-d\TH:i', strtotime($edit_entry['entry_date'])) ?>">
            </div>
            <?php endif; ?>
            <div class="field">
                <label>Content</label>
                <textarea name="content" rows="4" required><?= htmlspecialchars($edit_entry['content'] ?? '') ?></textarea>
            </div>
            <?php if ($edit_entry): ?>
                <button type="submit" name="update" class="btn btn-green">Update Entry</button>
                <a href="journal.php" class="btn btn-gray">Cancel</a>
            <?php else: ?>
                <button type="submit" name="add" class="btn btn-blue">Save Entry</button>
            <?php endif; ?>
        </form>
    </div>

    <h4>All Entries</h4>
    <?php if (mysqli_num_rows($entries) === 0): ?>
        <p class="muted">No entries yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Date Added</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($entries)): ?>
                <tr>
                    <td><?= $row['journal_id'] ?></td>
                    <td><a href="view.php?id=<?= $row['journal_id'] ?>" class="entry-link"><?= htmlspecialchars($row['title']) ?></a></td>
                    <td class="content-cell"><?= htmlspecialchars(mb_strlen($row['content']) > 60 ? mb_substr($row['content'], 0, 60) . '...' : $row['content']) ?></td>
                    <td><?= $row['entry_date'] ?></td>
                    <td><?= $row['updated_at'] ?></td>
                    <td class="actions">
                        <a href="?edit=<?= $row['journal_id'] ?>" class="btn btn-yellow">Edit</a>
                        <a href="?delete=<?= $row['journal_id'] ?>" class="btn btn-red"
                           onclick="return confirm('Delete this entry?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>
</body>
</html>