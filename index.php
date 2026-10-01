<?php
require_once __DIR__ . "/config.php";

// Tambah todo baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task'])) {
    $task = mysqli_real_escape_string($conn, $_POST['task']);
    mysqli_query($conn, "INSERT INTO todos (task, status) VALUES ('$task', 'pending')");
    header("Location: index.php");
    exit;
}

// Tandai selesai
if (isset($_GET['complete'])) {
    $id = (int)$_GET['complete'];
    mysqli_query($conn, "UPDATE todos SET status='done' WHERE id=$id");
    header("Location: index.php");
    exit;
}

// Hapus todo
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM todos WHERE id=$id");
    header("Location: index.php");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM todos ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>To-Do List</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 50px auto; background: #f4f4f4; }
        h1 { text-align: center; color: #333; }
        form { display: flex; gap: 8px; margin-bottom: 20px; }
        input[type=text] { flex: 1; padding: 8px; }
        button { padding: 8px 16px; cursor: pointer; }
        ul { list-style: none; padding: 0; }
        li { background: white; padding: 10px; margin-bottom: 8px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; }
        .done { text-decoration: line-through; color: #888; }
        a { text-decoration: none; margin-left: 8px; }
    </style>
</head>
<body>
    <h1>To-Do List</h1>
    <form method="POST">
        <input type="text" name="task" placeholder="Tambah tugas baru..." required>
        <button type="submit">Tambah</button>
    </form>
    <ul>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <li>
                <span class="<?= $row['status'] === 'done' ? 'done' : '' ?>">
                    <?= htmlspecialchars($row['task']) ?>
                </span>
                <span>
                    <?php if ($row['status'] !== 'done'): ?>
                        <a href="?complete=<?= $row['id'] ?>">✔</a>
                    <?php endif; ?>
                    <a href="?delete=<?= $row['id'] ?>">x</a>
                </span>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
