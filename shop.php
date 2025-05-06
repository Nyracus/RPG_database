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
    $shop_id = (int)$_POST['shop_id'];

    // Get character details
    $char = mysqli_fetch_assoc(mysqli_query($conn, "SELECT char_id, coins FROM Characters WHERE user_id = $user_id"));
    $char_id = $char['char_id'];
    $coins = $char['coins'];

    // Get shop item (can be item or skill)
    $shop = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Shop WHERE shop_id = $shop_id"));
    $type = $shop['type'];
    $cost = $shop['cost'];

    if ($coins < $cost) {
        $message = "❌ Not enough coins!";
    } else {
        if ($type === 'item') {
            // Get item info
            $item = mysqli_fetch_assoc(mysqli_query($conn, 
                "SELECT item_id, name FROM Items WHERE item_id = {$shop['item_id']}"));

            // Update inventory
            $check = mysqli_query($conn, "SELECT * FROM Inventory WHERE char_id = $char_id AND item_id = {$item['item_id']}");
            if (mysqli_num_rows($check) > 0) {
                mysqli_query($conn, "UPDATE Inventory SET quantity = quantity + 1 
                                     WHERE char_id = $char_id AND item_id = {$item['item_id']}");
            } else {
                mysqli_query($conn, "INSERT INTO Inventory (char_id, item_id, quantity) 
                                     VALUES ($char_id, {$item['item_id']}, 1)");
            }

            // Deduct coins
            mysqli_query($conn, "UPDATE Characters SET coins = coins - $cost WHERE char_id = $char_id");
            $message = "✅ You bought 1 × " . $item['name'];

        } elseif ($type === 'skill') {
            // Get skill info
            $skill = mysqli_fetch_assoc(mysqli_query($conn, 
                "SELECT skill_id, name FROM Skills WHERE skill_id = {$shop['skill_id']}"));

            // Check if already owned
            $owned = mysqli_query($conn, "SELECT * FROM CharacterSkills WHERE char_id = $char_id AND skill_id = {$skill['skill_id']}");
            if (mysqli_num_rows($owned) === 0) {
                // Insert new skill
                mysqli_query($conn, "INSERT INTO CharacterSkills (char_id, skill_id, equipped) 
                                     VALUES ($char_id, {$skill['skill_id']}, 0)");

                // Deduct coins
                mysqli_query($conn, "UPDATE Characters SET coins = coins - $cost WHERE char_id = $char_id");
                $message = "✅ You bought skill: " . $skill['name'];
            } else {
                $message = "⚠️ You already own the skill: " . $skill['name'];
            }
        } else {
            $message = "❌ Unknown item type!";
        }
    }
}


if (isset($_POST['gift_shop_id']) && isset($_POST['player_id'])) {
    $shop_id = (int)$_POST['gift_shop_id'];
    $target_user = (int)$_POST['player_id'];

    $char_row = mysqli_fetch_assoc(mysqli_query($conn, 
        "SELECT char_id FROM Characters WHERE user_id = $target_user"));
    $char_id = $char_row['char_id'];

    $item = mysqli_fetch_assoc(mysqli_query($conn, 
        "SELECT * FROM Shop WHERE shop_id = $shop_id"));

    if ($item['type'] === 'item') {
        // Give item
        $check = mysqli_query($conn, 
            "SELECT * FROM Inventory WHERE char_id = $char_id AND item_id = {$item['item_id']}");

        if (mysqli_num_rows($check) > 0) {
            mysqli_query($conn, "UPDATE Inventory SET quantity = quantity + 1 
                                 WHERE char_id = $char_id AND item_id = {$item['item_id']}");
        } else {
            mysqli_query($conn, "INSERT INTO Inventory (char_id, item_id, quantity) 
                                 VALUES ($char_id, {$item['item_id']}, 1)");
        }

        $message = "✅ Item gifted.";

    } elseif ($item['type'] === 'skill') {
        // Give skill
        $skill_id = $item['skill_id'];
        $owned = mysqli_query($conn, 
            "SELECT * FROM CharacterSkills WHERE char_id = $char_id AND skill_id = $skill_id");

        if (mysqli_num_rows($owned) === 0) {
            mysqli_query($conn, "INSERT INTO CharacterSkills (char_id, skill_id, equipped) 
                                 VALUES ($char_id, $skill_id, 0)");
            $message = "✅ Skill gifted.";
        } else {
            $message = "⚠️ Player already has this skill.";
        }
    }
}



?>

<!DOCTYPE html>
<html>
<head>
    <title>Coin Shop</title>
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

    .shop-wrapper {
        max-width: 1000px;
        margin: 80px auto;
        background-color: rgba(30, 30, 30, 0.95);
        padding: 30px;
        border-radius: 14px;
        box-shadow: 0 0 25px #000;
        color: #f0e6d2;
    }

    h2, h3 {
        color: #ffd700;
        text-align: center;
    }

    label {
        font-weight: bold;
        display: inline-block;
        margin: 10px 0 5px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    th, td {
        padding: 10px;
        text-align: center;
        border: 1px solid #555;
    }

    th {
        background-color: #333;
        color: #ffd700;
    }

    input[type="text"],
    select {
        padding: 6px;
        margin: 5px;
        background-color: #333;
        color: #fff;
        border: 1px solid #777;
        border-radius: 5px;
    }

    button {
        margin-top: 5px;
    }

    .back-link {
        text-align: center;
        margin-top: 20px;
    }

    .back-link a {
        color: #9acd32;
    }
</style>
</head>
<body>

<video autoplay muted loop id="bg-video">
    <source src="assets/background.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<div class="shop-wrapper">


<?php if ($role === 'GuildMaster'): ?>

<h2>Gift Items to Players</h2>
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
        $shop_items = mysqli_query($conn,
        "SELECT S.shop_id, S.type, 
                    COALESCE(I.name, SK.name) AS display_name
                FROM Shop S
                LEFT JOIN Items I ON S.item_id = I.item_id
                LEFT JOIN Skills SK ON S.skill_id = SK.skill_id
                ORDER BY display_name");
    
        while ($row = mysqli_fetch_assoc($shop_items)) {
            echo "<tr>";
            echo "<td>{$row['display_name']} (" . ucfirst($row['type']) . ")</td>";
            echo "<td>
                    <button type='submit' name='gift_shop_id' value='{$row['shop_id']}'>Gift</button>
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


    <h2> RPG Coin Shop</h2>
    <p>You have <strong><?php echo $coins; ?></strong> coins.</p>
    <?php if (isset($message)) echo "<p style='color:blue;'>$message</p>"; ?>

    <form method="get">
    <label>Filter by Slot:</label>
    <select name="slot">
        <option value="">All</option>
        <option value="MainHand">MainHand</option>
        <option value="OffHand">OffHand</option>
        <option value="Head">Head</option>
        <option value="Torso">Torso</option>
        <option value="Legs">Legs</option>
        <option value="Arms">Arms</option>
        <option value="Hands">Hands</option>
        <option value="Feet">Feet</option>
        <option value="Finger1">Finger1</option>
        <option value="Finger2">Finger2</option>
        <option value="None">Skill</option>
    </select>

    <label>Sort by:</label>
    <select name="sort">
        <option value="">Default</option>
        <option value="name_asc" <?= ($_GET['sort'] ?? '') == 'name_asc' ? 'selected' : '' ?>>Name A → Z</option>
        <option value="name_desc" <?= ($_GET['sort'] ?? '') == 'name_desc' ? 'selected' : '' ?>>Name Z → A</option>
        <option value="price_asc" <?= ($_GET['sort'] ?? '') == 'price_asc' ? 'selected' : '' ?>>Price Low → High</option>
        <option value="price_desc" <?= ($_GET['sort'] ?? '') == 'price_desc' ? 'selected' : '' ?>>Price High → Low</option>
        <option value="power_asc" <?= ($_GET['sort'] ?? '') == 'power_asc' ? 'selected' : '' ?>>Power Low → High</option>
        <option value="power_desc" <?= ($_GET['sort'] ?? '') == 'power_desc' ? 'selected' : '' ?>>Power High → Low</option>
    </select>


    <button type="submit">Apply</button>
    </form>
    <br>


    <table border="1">
        <tr>
            <th>Item</th>
            <th>Cost</th>
            <th>Action</th>
        </tr>
        <?php
        
        // Build WHERE clause for slot
        $where = "1"; // base filter
        if (!empty($_GET['slot'])) {
            $safe_slot = mysqli_real_escape_string($conn, $_GET['slot']);
            $where .= " AND slot = '$safe_slot'";
        }

        $order = "display_name ASC";
        if (!empty($_GET['sort'])) {
            switch ($_GET['sort']) {
                case 'price_asc': $order = "cost ASC"; break;
                case 'price_desc': $order = "cost DESC"; break;
                case 'power_asc': $order = "power_value ASC"; break;
                case 'power_desc': $order = "power_value DESC"; break;
                case 'name_desc': $order = "display_name DESC"; break;
                default: $order = "display_name ASC";
            }
        }

        $shop_items = mysqli_query($conn,
            "SELECT * FROM Shop WHERE $where ORDER BY $order");



    

        while ($row = mysqli_fetch_assoc($shop_items)) {
            echo "<tr>";
            $name = ($row['type'] === 'skill' ? "{$row['display_name']} (Skill +{$row['power_value']})"
                                  : "{$row['display_name']} (Power +{$row['power_value']})");


            echo "<td>$name</td>";
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

