<?php
include 'db.php';
session_start();


$message = "";

// Handle purge request
if (isset($_POST['batman_protocol'])) {
    $user_id = (int)$_POST['purge_user_id'];
    $password = $_POST['gm_password'];
    $GM_PASSWORD = "1";  // 🔐 change this to your real GM password

    if ($password !== $GM_PASSWORD) {
        $message = "❌ Incorrect GM password.";
    } else {
        // Get username before deleting
        $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT username FROM Users WHERE user_id = $user_id"));
        $username = $user['username'] ?? "Unknown";

        // Delete all associated data
        mysqli_query($conn, "DELETE FROM CharacterSkills WHERE char_id IN (SELECT char_id FROM Characters WHERE user_id = $user_id)");
        mysqli_query($conn, "DELETE FROM Inventory WHERE char_id IN (SELECT char_id FROM Characters WHERE user_id = $user_id)");
        mysqli_query($conn, "DELETE FROM Equipment WHERE char_id IN (SELECT char_id FROM Characters WHERE user_id = $user_id)");
        mysqli_query($conn, "DELETE FROM QuestAcceptance WHERE char_id IN (SELECT char_id FROM Characters WHERE user_id = $user_id)");
        mysqli_query($conn, "DELETE FROM Characters WHERE user_id = $user_id");
        mysqli_query($conn, "DELETE FROM Users WHERE user_id = $user_id");

        // Create emergency bounty quest
        $description = "A rogue adventurer named $username has vanished. Hunt down their legacy and restore balance.";
        mysqli_query($conn,
            "INSERT INTO Quests (name, area, description, suggested_rank, deadline)
             VALUES ('Emergency Bounty: $username', 'Unknown', '$description', 'S', NOW() + INTERVAL 7 DAY)");

        $message = "🦇 $username purged. Emergency bounty posted.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>🦇 Batman Protocol</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        #bg-video {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            z-index: -1;
            object-fit: cover;
            opacity: 0.72;
            filter: brightness(75%);
            transform: translateY(-60px);
        }

        .batman-container {
            max-width: 500px;
            margin: 80px auto;
            background-color: rgba(15, 15, 15, 0.93);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 30px #000;
            text-align: center;
            color: #ff4444;
        }

        .batman-container h2 {
            color: #ff4444;
            text-shadow: 0 0 5px #ff0000;
        }

        .batman-container select,
        .batman-container input[type="password"] {
            width: 90%;
            padding: 8px;
            background-color: #222;
            color: #fff;
            border: 1px solid #700;
            margin-top: 10px;
        }

        .batman-container button {
            background-color: #8b0000;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-top: 15px;
            cursor: pointer;
            font-weight: bold;
            border-radius: 6px;
        }

        .batman-container button:hover {
            background-color: #ff1c1c;
            box-shadow: 0 0 10px #ff5555;
        }

        .batman-container p a {
            color: #ccc;
        }

        .batman-container p a:hover {
            color: #fff;
        }
    </style>
</head>
<body>

<!-- 🎥 Background Video -->
<video autoplay muted loop id="bg-video">
    <source src="assets/batman.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<div class="batman-container">
    <h2>🦇 Initiate Batman Protocol</h2>

    <?php if (!empty($message)) echo "<p style='color:red;'>$message</p>"; ?>

    <form method="post">
        <label>Select User to Eliminate:</label><br>
        <select name="purge_user_id" required>
            <option value="">-- Select Player --</option>
            <?php
            $players = mysqli_query($conn, "SELECT user_id, username FROM Users WHERE role = 'Player'");
            while ($p = mysqli_fetch_assoc($players)) {
                echo "<option value='{$p['user_id']}'>{$p['username']}</option>";
            }
            ?>
        </select><br><br>

        <label>Enter GM Password:</label><br>
        <input type="password" name="gm_password" required><br><br>

        <button type="submit" name="batman_protocol"> Engage Protocol</button>
    </form>

    <p><a href="menu.php">← Back to Menu</a></p>
</div>

</body>
</html>

