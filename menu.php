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
<head><title>Main Menu</title></head>
<body>
<h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>

<?php if ($role === 'GuildMaster'): ?>
    <h2>🦇 Batman Protocol (Guild Master Access)</h2>
    <ul>
        <li><a href="shop.php">🎁 Gift Items to Players</a></li>
        <li><a href="quests.php">📜 Post Quests</a></li>
        <li><a href="view_players.php">📋 View Players & Characters</a></li>
    </ul>
<?php else: ?>
    <h2>🧍 Adventurer Menu</h2>
    <ul>
        <li><a href="stats.php">📊 View Stats</a></li>
        <li><a href="inventory.php">🛡 Inventory</a></li>
        <li><a href="shop.php">🪙 Coin Shop</a></li>
        <li><a href="battle.php">⚔ Battle</a></li>
        <li><a href="friends.php">🤝 Friends</a></li>
        <li><a href="chat.php">💬 Chat</a></li>
        <li><a href="quests.php">📜 Available Quests</a></li>
    </ul>
<?php endif; ?>

<p><a href="logout.php">🚪 Logout</a></p>
</body>
</html>
