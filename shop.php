<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user role
$user_query = mysqli_query($conn, "SELECT role FROM Users WHERE user_id = $user_id");
$role = mysqli_fetch_assoc($user_query)['role'];


// Fetch character ID
$char_query = mysqli_query($conn, "SELECT char_id, coins FROM Characters WHERE user_id = $user_id LIMIT 1");

if ($char = mysqli_fetch_assoc($char_query)) {
    $char_id = $char['char_id'];
    $coins = $char['coins'];
} else {
    echo "<p style='color:red;'>No character found for this user. Please create a character first.</p>";
    exit(); // stop execution to avoid more errors
}


// Handle purchase
if (isset($_POST['buy'])) {
    $shop_id = $_POST['shop_id'];

    // Get item and price
    $item_query = mysqli_query($conn, 
        "SELECT S.cost, I.item_id, I.name FROM Shop S 
         JOIN Items I ON S.item_id = I.item_id 
         WHERE S.shop_id = $shop_id");
    $item = mysqli_fetch_assoc($item_query);
    


    
    if ($coins >= $item['cost']) {
        // Deduct coins and add item to inventory
        mysqli_query($conn, "UPDATE Characters SET coins = coins - {$item['cost']} WHERE char_id = $char_id");

        // Add to inventory or increase quantity
        $check = mysqli_query($conn, "SELECT * FROM Inventory WHERE char_id = $char_id AND item_id = {$item['item_id']}");
        if (mysqli_num_rows($check) > 0) {
            mysqli_query($conn, "UPDATE Inventory SET quantity = quantity + 1 
                                 WHERE char_id = $char_id AND item_id = {$item['item_id']}");
        } else {
            mysqli_query($conn, "INSERT INTO Inventory (char_id, item_id, quantity) 
                                 VALUES ($char_id, {$item['item_id']}, 1)");
        }

        $message = "You bought 1 × " . $item['name'];
    } else {
        $message = "Not enough coins!";
    }
}

if (isset($_POST['gift']) && $role === 'GuildMaster') {
    $item_id = $_POST['gift'];
    $recipient_id = $_POST['player_id'];

    $char_query = mysqli_query($conn, "SELECT char_id FROM Characters WHERE user_id = $recipient_id LIMIT 1");
    $char = mysqli_fetch_assoc($char_query);

    if ($char) {
        $char_id = $char['char_id'];
        $check = mysqli_query($conn, "SELECT * FROM Inventory WHERE char_id = $char_id AND item_id = $item_id");
        if (mysqli_num_rows($check) > 0) {
            mysqli_query($conn, "UPDATE Inventory SET quantity = quantity + 1 WHERE char_id = $char_id AND item_id = $item_id");
        } else {
            mysqli_query($conn, "INSERT INTO Inventory (char_id, item_id, quantity) VALUES ($char_id, $item_id, 1)");
        }
        $message = "🎁 Item gifted successfully!";
    } else {
        $message = "❌ Could not find character for selected user.";
    }
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>Coin Shop</title>
    <style>
        input[type="text"] { width: 200px; }
        select { width: 220px; }
    </style>
</head>
<body>

<?php if ($role === 'GuildMaster'): ?>

<h2>🎁 Gift Items to Players</h2>
<?php if (isset($message)) echo "<p style='color:green;'>$message</p>"; ?>

<!-- Player Dropdown -->
<form method="post">
    <label>Choose Player:</label><br>
    <select name="player_id" required>
        <option value="">--Select Player--</option>
        <?php
        $players = mysqli_query($conn, "SELECT user_id, username FROM Users WHERE role = 'Player'");
        while ($p = mysqli_fetch_assoc($players)) {
            echo "<option value='{$p['user_id']}'>{$p['username']}</option>";
        }
        ?>
    </select><br><br>

    <!-- Search Bar -->
    <label>Search Items:</label><br>
    <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search for items..."><br><br>

    <!-- Item Table -->
    <table border="1" id="itemTable">
        <tr>
            <th>Item</th>
            <th>Action</th>
        </tr>
        <?php
        $items = mysqli_query($conn, "SELECT item_id, name FROM Items ORDER BY name");
        while ($item = mysqli_fetch_assoc($items)) {
            echo "<tr>";
            echo "<td>{$item['name']}</td>";
            echo "<td>
                    <button type='submit' name='gift' value='{$item['item_id']}'>Gift</button>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>
</form>

<script>
function filterTable() {
    const input = document.getElementById("searchInput").value.toLowerCase();
    const table = document.getElementById("itemTable");
    const rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) {
        let itemName = rows[i].getElementsByTagName("td")[0].textContent.toLowerCase();
        rows[i].style.display = itemName.includes(input) ? "" : "none";
    }
}
</script>

<?php else: ?>


    <h2>🪙 RPG Coin Shop</h2>
    <p>You have <strong><?php echo $coins; ?></strong> coins.</p>
    <?php if (isset($message)) echo "<p style='color:blue;'>$message</p>"; ?>

    <table border="1">
        <tr>
            <th>Item</th>
            <th>Cost</th>
            <th>Action</th>
        </tr>
        <?php
        $shop_items = mysqli_query($conn,
            "SELECT S.shop_id, I.name, S.cost 
             FROM Shop S JOIN Items I ON S.item_id = I.item_id");

        while ($row = mysqli_fetch_assoc($shop_items)) {
            echo "<tr>";
            echo "<td>{$row['name']}</td>";
            echo "<td>{$row['cost']} coins</td>";
            echo "<td>
                    <form method='post'>
                        <input type='hidden' name='shop_id' value='{$row['shop_id']}'>
                        <button type='submit' name='buy'>Buy</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>

<?php endif; ?>

<p><a href="menu.php">← Back to Menu</a></p>
</body>
</html>

