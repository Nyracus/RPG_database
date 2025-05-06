<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$char = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT char_id FROM Characters WHERE user_id = $user_id"));
$char_id = $char['char_id'];

$inventory = mysqli_query($conn,
    "SELECT I.name, I.slot, INV.quantity
     FROM Inventory INV
     JOIN Items I ON INV.item_id = I.item_id
     WHERE INV.char_id = $char_id
     ORDER BY I.slot, I.name");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        /* Fullscreen background video */
        #bg-video {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            z-index: -1;
            object-fit: cover;
            opacity: 0.72;
            filter: brightness(75%);
        }

        .inventory-wrapper {
            max-width: 800px;
            margin: auto;
            background-color: rgba(30, 30, 30, 0.9);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 20px #000;
            margin-top: 50px;
        }

        .inventory-table {
            width: 100%;
            border: 1px solid #444;
        }

        .inventory-table th {
            background-color: #333;
            color: #ffd700;
            text-align: center;
        }

        .inventory-table td {
            text-align: center;
        }

        .title {
            text-align: center;
            color: #ffd700;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<!-- 🎥 Background Video -->
<video autoplay muted loop id="bg-video">
    <source src="assets/background.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<div class="inventory-wrapper">
    <div class="title">Character Inventory</div>

    <?php if (mysqli_num_rows($inventory) === 0): ?>
        <p style="text-align:center;">Your inventory is empty.</p>
    <?php else: ?>
        <table class="inventory-table">
            <tr>
                <th>Item Name</th>
                <th>Slot</th>
                <th>Quantity</th>
            </tr>
            <?php while ($item = mysqli_fetch_assoc($inventory)): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= $item['slot'] ?></td>
                    <td><?= $item['quantity'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>

    <div class="back-link">
        <a href="menu.php">← Return to Menu</a>
    </div>
</div>

</body>
</html>
