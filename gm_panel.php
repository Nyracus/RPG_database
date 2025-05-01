<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Confirm Guild Master
$user_id = $_SESSION['user_id'];
$role_result = mysqli_query($conn, "SELECT role FROM Users WHERE user_id = $user_id");
$role = mysqli_fetch_assoc($role_result)['role'];
if ($role !== 'GuildMaster') {
    echo "<h2>Access Denied. Only Guild Masters may enter the Protocol.</h2>";
    exit();
}

// ✅ Handle Quest Posting
if (isset($_POST['post_quest'])) {
    $name = $_POST['quest_name'];
    $desc = $_POST['description'];
    $level = $_POST['required_level'];

    mysqli_query($conn, "INSERT INTO Quests (name, description, required_level) 
                         VALUES ('$name', '$desc', $level)");
    $msg = "Quest posted successfully!";
}
?>

<!DOCTYPE html>
<html>
<head><title>🦇 Batman Protocol Panel</title></head>
<body>

<h2>🦇 Guild Master: Batman Protocol</h2>
<?php if (isset($msg)) echo "<p style='color:green;'>$msg</p>"; ?>

<h3>Post New Quest</h3>
<form method="post">
    Quest Name: <input type="text" name="quest_name" required><br>
    Description: <input type="text" name="description" required><br>
    Required Level: <input type="number" name="required_level" min="1" value="1"><br>
    <button type="submit" name="post_quest">Post Quest</button>
</form>

<hr>

<h3>🧍 All Players & Their Characters</h3>
<table border="1" cellpadding="5">
    <tr>
        <th>Username</th>
        <th>Character Name</th>
        <th>Level</th>
        <th>Power</th>
        <th>Coins</th>
    </tr>
    <?php
    $all = mysqli_query($conn,
        "SELECT U.username, C.name AS char_name, C.level, C.power_level, C.coins 
         FROM Users U 
         JOIN Characters C ON U.user_id = C.user_id");

    while ($row = mysqli_fetch_assoc($all)) {
        echo "<tr>
                <td>{$row['username']}</td>
                <td>{$row['char_name']}</td>
                <td>{$row['level']}</td>
                <td>{$row['power_level']}</td>
                <td>{$row['coins']}</td>
              </tr>";
    }
    ?>
</table>

<p><a href="menu.php">← Back to Menu</a></p>
</body>
</html>
