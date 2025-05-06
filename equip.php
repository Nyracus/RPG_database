<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get character ID
$char_result = mysqli_query($conn, "SELECT char_id FROM Characters WHERE user_id = $user_id");
$char = mysqli_fetch_assoc($char_result);
$char_id = $char['char_id'] ?? null;

if (!$char_id) {
    echo "<p>No character found.</p>";
    exit();
}

// ✅ Equip item
if (isset($_POST['equip']) && isset($_POST['item_id'])) {
    $item_id = (int)$_POST['item_id'];

    // Get item slot
    $item = mysqli_fetch_assoc(mysqli_query($conn, "SELECT slot FROM Items WHERE item_id = $item_id"));
    $slot = $item['slot'];

    // Remove existing item in that slot
    mysqli_query($conn, "DELETE FROM Equipment WHERE char_id = $char_id AND slot = '$slot'");

    // Equip new item
    mysqli_query($conn, "INSERT INTO Equipment (char_id, slot, item_id) VALUES ($char_id, '$slot', $item_id)");

    $message = "✅ Equipped item in $slot slot.";
}

// ✅ Get inventory items
$inventory = mysqli_query($conn,
    "SELECT I.item_id, I.name, I.slot, I.power_value, Inv.quantity
     FROM Inventory Inv
     JOIN Items I ON Inv.item_id = I.item_id
     WHERE Inv.char_id = $char_id AND Inv.quantity > 0");
?>

<!DOCTYPE html>
<html>
<head>
    <title> Equip Items</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
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

        .equip-wrapper {
            max-width: 800px;
            margin: 80px auto;
            background-color: rgba(25, 25, 25, 0.9);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 20px #000;
        }

        .equip-wrapper h2 {
            text-align: center;
            color: #ffd700;
        }

        .equip-wrapper table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .equip-wrapper th, .equip-wrapper td {
            border: 1px solid #555;
            padding: 10px;
            text-align: center;
        }

        .equip-wrapper th {
            background-color: #333;
            color: #ffd700;
        }

        .equip-wrapper td form {
            margin: 0;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #9acd32;
        }

        p {
            text-align: center;
        }
    </style>
</head>
<body>

<!-- 🎥 Background Video -->
<video autoplay muted loop id="bg-video">
    <source src="assets/background.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<div class="equip-wrapper">
    <h2> Your Inventory</h2>

    <?php if (isset($message)) echo "<p style='color:lightgreen;'>$message</p>"; ?>

    <table>
        <tr>
            <th>Name</th>
            <th>Slot</th>
            <th>Power</th>
            <th>Qty</th>
            <th>Action</th>
        </tr>
        <?php while ($item = mysqli_fetch_assoc($inventory)): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= $item['slot'] ?></td>
                <td>+<?= $item['power_value'] ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                        <button type="submit" name="equip">Equip</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <div class="back-link">
        <a href="menu.php">← Back to Menu</a>
    </div>
</div>

</body>
</html>
