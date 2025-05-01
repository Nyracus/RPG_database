<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$chat_with = $_POST['friend_id'] ?? null;

// ✅ Send message
if (isset($_POST['send']) && $chat_with) {
    $msg = trim($_POST['message']);
    if ($msg !== '') {
        $msg_safe = mysqli_real_escape_string($conn, $msg);
        mysqli_query($conn, "INSERT INTO ChatMessages (sender_id, receiver_id, message) 
                             VALUES ($user_id, $chat_with, '$msg_safe')");
    }
}

// ✅ Get friends (Accepted)
$friends_query = mysqli_query($conn,
    "SELECT U.user_id, U.username
     FROM FriendRequests F
     JOIN Users U ON (
         (U.user_id = F.sender_id AND F.receiver_id = $user_id)
         OR (U.user_id = F.receiver_id AND F.sender_id = $user_id)
     )
     WHERE F.status = 'Accepted' AND U.user_id != $user_id"
);
?>

<!DOCTYPE html>
<html>
<head><title>RPG Chat</title></head>
<body>
<h2>🗨️ Chat with a Friend</h2>

<!-- Friend selection -->
<form method="post">
    <label>Choose friend: </label>
    <select name="friend_id" required onchange="this.form.submit()">
        <option value="">--Select--</option>
        <?php while ($row = mysqli_fetch_assoc($friends_query)) {
            $selected = ($chat_with == $row['user_id']) ? 'selected' : '';
            echo "<option value='{$row['user_id']}' $selected>{$row['username']}</option>";
        } ?>
    </select>
</form>

<?php if ($chat_with): ?>
    <h3>Chatting with 
        <?php
        $res = mysqli_query($conn, "SELECT username FROM Users WHERE user_id = $chat_with");
        $name = mysqli_fetch_assoc($res)['username'];
        echo $name;
        ?>
    </h3>

    <!-- Display chat history -->
    <div style="border:1px solid #999; padding:10px; height:200px; overflow-y:scroll;">
        <?php
        $chat_query = mysqli_query($conn,
            "SELECT sender_id, message, sent_at FROM ChatMessages 
             WHERE (sender_id = $user_id AND receiver_id = $chat_with)
                OR (sender_id = $chat_with AND receiver_id = $user_id)
             ORDER BY sent_at ASC");

        while ($row = mysqli_fetch_assoc($chat_query)) {
            $sender = ($row['sender_id'] == $user_id) ? "🧍‍♂️ You" : $name;
            echo "<p><strong>$sender:</strong> {$row['message']} <small style='color:gray'>[{$row['sent_at']}]</small></p>";
        }
        ?>
    </div>

    <!-- Send message -->
    <form method="post" style="margin-top:10px;">
        <input type="hidden" name="friend_id" value="<?php echo $chat_with; ?>">
        <textarea name="message" rows="2" cols="40" placeholder="Type your message..." required></textarea><br>
        <button type="submit" name="send">Send</button>
    </form>
<?php endif; ?>

<p><a href="menu.php">← Back to Menu</a></p>
</body>
</html>
