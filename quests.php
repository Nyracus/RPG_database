<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_query = mysqli_query($conn, "SELECT role FROM Users WHERE user_id = $user_id");
$role = mysqli_fetch_assoc($user_query)['role'];
?>

<!DOCTYPE html>
<html>
<head><title>📜 Quests</title></head>
<body>

<h1>📜 Quests</h1>

<?php if ($role === 'GuildMaster'): ?>

    <h2>🦇 Post a New Quest</h2>
    <?php
    if (isset($_POST['post_quest'])) {
        $name = $_POST['name'];
        $area = $_POST['area'];
        $rewards = $_POST['rewards'];
        $rank = $_POST['suggested_rank'];
        $desc = $_POST['description'];
        $deadline = $_POST['deadline'];
        $special = $_POST['special_requests'];

        $query = "INSERT INTO Quests (name, area, rewards, suggested_rank, description, deadline, special_requests)
                  VALUES ('$name', '$area', '$rewards', '$rank', '$desc', '$deadline', '$special')";

        if (mysqli_query($conn, $query)) {
            echo "<p style='color:green;'>✅ Quest posted successfully!</p>";
        } else {
            echo "<p style='color:red;'>❌ Failed to post quest.</p>";
        }
    }
    ?>

    <form method="post">
        <label>Quest Name:</label><br>
        <input type="text" name="name" required><br>

        <label>Quest Area:</label><br>
        <input type="text" name="area" required><br>

        <label>Quest Rewards:</label><br>
        <input type="text" name="rewards" required><br>

        <label>Suggested Rank:</label><br>
        <select name="suggested_rank" required>
            <option>SSS</option><option>SS</option><option>S</option><option>A</option><option>B</option>
            <option>C</option><option>D</option><option>E</option><option>F</option>
        </select><br>

        <label>Quest Description:</label><br>
        <textarea name="description" rows="3" cols="40" required></textarea><br>

        <label>Deadline:</label><br>
        <input type="date" name="deadline" required><br>

        <label>Special Requests:</label><br>
        <input type="text" name="special_requests"><br><br>

        <button type="submit" name="post_quest">Post Quest</button>
    </form>

<?php else: ?>

    <h2>📜 Available Quests</h2>

    <?php
    $quests = mysqli_query($conn, "SELECT * FROM Quests ORDER BY created_at DESC");

    if (mysqli_num_rows($quests) > 0) {
        while ($q = mysqli_fetch_assoc($quests)) {
            echo "<div style='border:1px solid gray; padding:10px; margin-bottom:10px;'>";
            echo "<strong>{$q['name']} [Rank: {$q['suggested_rank']}]</strong><br>";
            echo "<em>Area:</em> {$q['area']}<br>";
            echo "<em>Rewards:</em> {$q['rewards']}<br>";
            echo "<em>Description:</em> {$q['description']}<br>";
            echo "<em>Deadline:</em> {$q['deadline']}<br>";
            echo "<em>Special:</em> {$q['special_requests']}<br>";
            echo "<form method='post'><button name='request_quest' value='{$q['quest_id']}'>Request to Accept</button></form>";
            echo "</div>";
        }
    } else {
        echo "<p>No quests available yet.</p>";
    }
    ?>

<?php endif; ?>

<p><a href="menu.php">← Back to Menu</a></p>
</body>
</html>
