<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Add Item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_name'])) {
    $item_name = htmlspecialchars(trim($_POST['item_name']));
    $quantity = intval($_POST['quantity']);

    $stmt = $conn->prepare("INSERT INTO shopping_items (user_id, item_name, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $user_id, $item_name, $quantity);
    $stmt->execute();
}

// Delete Item
if (isset($_GET['delete'])) {
    $item_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM shopping_items WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $item_id, $user_id);
    $stmt->execute();
}

// Fetch Items
$stmt = $conn->prepare("SELECT * FROM shopping_items WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$items = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Shopping List</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>! <a href="logout.php">Logout</a></p>

    <form method="POST">
        <input type="text" name="item_name" placeholder="Item Name" required>
        <input type="number" name="quantity" placeholder="Quantity" required>
        <button type="submit">Add Item</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>
                        <a href="?delete=<?= $item['id'] ?>">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
