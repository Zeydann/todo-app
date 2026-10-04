<?php
require_once __DIR__ . "/config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task']) && trim($_POST['task']) !== '') {
    $task = mysqli_real_escape_string($conn, trim($_POST['task']));
    mysqli_query($conn, "INSERT INTO todos (task, status) VALUES ('$task', 'pending')");
    header("Location: index.php");
    exit;
}

if (isset($_GET['complete'])) {
    $id = (int)$_GET['complete'];
    mysqli_query($conn, "UPDATE todos SET status='done' WHERE id=$id");
    header("Location: index.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM todos WHERE id=$id");
    header("Location: index.php");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM todos ORDER BY created_at DESC");
$todos = mysqli_fetch_all($result, MYSQLI_ASSOC);
$total = count($todos);
$done = count(array_filter($todos, fn($t) => $t['status'] === 'done'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>To-Do List</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f5f6f8;
            color: #1a1a2e;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            padding: 48px 16px;
        }
        .container { width: 100%; max-width: 480px; }
        .header { margin-bottom: 24px; }
        .header h1 { font-size: 22px; font-weight: 600; margin-bottom: 4px; }
        .header p { font-size: 13px; color: #8a8f98; }
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        form {
            display: flex;
            gap: 8px;
            padding: 16px;
            border-bottom: 1px solid #eef0f3;
        }
        input[type=text] {
            flex: 1;
            border: 1px solid #e3e5e8;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.15s;
        }
        input[type=text]:focus { border-color: #6366f1; }
        button {
            background: #6366f1;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.15s;
        }
        button:hover { background: #4f46e5; }
        ul { list-style: none; }
        li {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-bottom: 1px solid #f2f3f5;
            transition: background 0.1s;
        }
        li:last-child { border-bottom: none; }
        li:hover { background: #fafbfc; }
        .check {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #d1d5db;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 11px;
            color: transparent;
            transition: all 0.15s;
        }
        .check:hover { border-color: #6366f1; }
        .check.done { background: #6366f1; border-color: #6366f1; color: #fff; }
        .task-text { flex: 1; font-size: 14px; }
        .task-text.done { text-decoration: line-through; color: #a0a4ab; }
        .delete-btn {
            color: #c1c5cb;
            text-decoration: none;
            font-size: 18px;
            line-height: 1;
            padding: 4px;
            transition: color 0.15s;
        }
        .delete-btn:hover { color: #ef4444; }
        .empty {
            padding: 48px 16px;
            text-align: center;
            color: #a0a4ab;
            font-size: 14px;
        }
        .footer {
            padding: 10px 16px;
            font-size: 12px;
            color: #a0a4ab;
            background: #fafbfc;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>To-Do List</h1>
            <p><?= $total > 0 ? "$done dari $total tugas selesai" : "Belum ada tugas" ?></p>
        </div>
        <div class="card">
            <form method="POST">
                <input type="text" name="task" placeholder="Tambah tugas baru..." required autofocus>
                <button type="submit">Tambah</button>
            </form>
            <?php if ($total === 0): ?>
                <div class="empty">Belum ada tugas. Tambahkan satu di atas.</div>
            <?php else: ?>
                <ul>
                    <?php foreach ($todos as $row): ?>
                        <li>
                            <?php if ($row['status'] !== 'done'): ?>
                                <a href="?complete=<?= $row['id'] ?>" class="check"></a>
                            <?php else: ?>
                                <a href="?complete=<?= $row['id'] ?>" class="check done">✓</a>
                            <?php endif; ?>
                            <span class="task-text <?= $row['status'] === 'done' ? 'done' : '' ?>">
                                <?= htmlspecialchars($row['task']) ?>
                            </span>
                            <a href="?delete=<?= $row['id'] ?>" class="delete-btn">&times;</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
