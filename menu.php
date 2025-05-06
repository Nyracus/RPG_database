<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT username, role FROM Users WHERE user_id = $user_id");

if (!$result || mysqli_num_rows($result) === 0) {
    echo "<h2>Error: User not found.</h2>";
    exit();
}

$user = mysqli_fetch_assoc($result);
$username = $user['username'];
$role = $user['role'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Main Menu</title>
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
            opacity: 0.65;
            filter: brightness(90%);
        }

        .menu-wrapper {
            max-width: 600px;
            margin: 80px auto;
            background-color: rgba(30, 30, 30, 0.92);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px #000;
            text-align: center;
        }

        .menu-wrapper h1 {
            color: #ffd700;
        }

        .menu-wrapper ul {
            list-style: none;
            padding: 0;
            margin-top: 20px;
        }

        .menu-wrapper ul li {
            margin: 12px 0;
        }

        .menu-wrapper ul li a {
            color: #9acd32;
            font-size: 18px;
            font-weight: bold;
            text-decoration: none;
        }

        .menu-wrapper ul li a:hover {
            color: #fff;
            text-shadow: 0 0 5px #ffd700;
        }

        .logout-link {
            margin-top: 30px;
            display: inline-block;
        }

        .logout-link a {
            color: #ff6961;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- 🎥 Background Video -->
<video autoplay muted loop id="bg-video">
    <source src="assets/menu_bg.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<div class="menu-wrapper">
    <h1>Welcome, <?= htmlspecialchars($username); ?>!</h1>

    <?php if ($role === 'GuildMaster'): ?>
        <h2> Guild Master Menu</h2>
        <ul>
            <li><a href="batman.php">🦇 Batman Protocol</a></li>
            <li><a href="shop.php"> Gift Items to Players</a></li>
            <li><a href="quests.php"> Post Quests</a></li>
            <li><a href="leaderboard.php"> Leaderboard</a></li>
        </ul>
    <?php else: ?>
        <h2>Adventurer Menu</h2>
        <ul>
            <li><a href="stats.php"> View Stats</a></li>
            <li><a href="inventory.php"> Inventory</a></li>
            <li><a href="equip.php"> Equip Items</a></li>
            <li><a href="skills.php"> Manage Skills</a></li>
            <li><a href="shop.php"> Coin Shop</a></li>
            
            <li><a href="friends.php"> Friends</a></li>
            <li><a href="chat.php"> Chat</a></li>
            <li><a href="quests.php"> Available Quests</a></li>
            <li><a href="leaderboard.php"> Leaderboard</a></li>
        </ul>
    <?php endif; ?>

    <div class="logout-link">
        <a href="logout.php"> Logout</a>
    </div>
</div>

</body>
</html>
