<?php
session_start();
include 'db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Handle sending friend request
if (isset($_POST['send_request'])) {
    $friend_username = $_POST['friend_username'];

    $receiver_query = mysqli_query($conn, "SELECT user_id FROM Users WHERE username = '$friend_username'");
    if ($receiver = mysqli_fetch_assoc($receiver_query)) {
        $receiver_id = $receiver['user_id'];

        if ($receiver_id == $user_id) {
            $message = "❌ You cannot add yourself!";
        } else {
            // Check for existing request
            $check_query = mysqli_query($conn,
                "SELECT * FROM FriendRequests 
                 WHERE (sender_id = $user_id AND receiver_id = $receiver_id)
                    OR (sender_id = $receiver_id AND receiver_id = $user_id)");

            if (mysqli_num_rows($check_query) > 0) {
                $message = "⚠️ Friend request already exists.";
            } else {
                mysqli_query($conn,
                    "INSERT INTO FriendRequests (sender_id, receiver_id, status) 
                     VALUES ($user_id, $receiver_id, 'Pending')");
                $message = "✅ Friend request sent to $friend_username.";
            }
        }
    } else {
        $message = "❌ User not found.";
    }
}

// ✅ Handle accept/reject
if (isset($_POST['action']) && isset($_POST['request_id'])) {
    $req_id = $_POST['request_id'];
    $action = $_POST['action'];

    $new_status = ($action === 'accept') ? 'Accepted' : 'Rejected';
    mysqli_query($conn, "UPDATE FriendRequests SET status = '$new_status' WHERE request_id = $req_id");
}
?>

<!DOCTYPE html>
<html>
<head><title>Friends</title></head>
<body>

<h2>🤝 Friend System</h2>
<?php if (isset($message)) echo "<p>$message</p>"; ?>

<h3>Send a Friend Request</h3>
<form method="post">
    Username: <input type="text" name="friend_username" required>
    <button type="submit" name="send_request">Send Request</button>
</form>

<h3>Incoming Friend Requests</h3>
<?php

// Assume $user_id is already set from session
$friends_query = mysqli_query($conn,
    "SELECT U.username 
     FROM FriendRequests F
     JOIN Users U ON (
         (U.user_id = F.sender_id AND F.receiver_id = $user_id)
         OR (U.user_id = F.receiver_id AND F.sender_id = $user_id)
     )
     WHERE F.status = 'Accepted' AND U.user_id != $user_id"
);

echo "<h3>Your Friends</h3>";

if (mysqli_num_rows($friends_query) > 0) {
    while ($row = mysqli_fetch_assoc($friends_query)) {
        echo "<p>👤 " . $row['username'] . "</p>";
    }
} else {
    echo "<p>You have no friends yet. 😢</p>";
}


$incoming = mysqli_query($conn,
    "SELECT F.request_id, U.username 
     FROM FriendRequests F
     JOIN Users U ON F.sender_id = U.user_id
     WHERE F.receiver_id = $user_id AND F.status = 'Pending'");


if (mysqli_num_rows($incoming) > 0) {
    while ($row = mysqli_fetch_assoc($incoming)) {
        echo "<form method='post'>";
        echo "<strong>{$row['username']}</strong> wants to be friends.";
        echo "<input type='hidden' name='request_id' value='{$row['request_id']}'>";
        echo " <button type='submit' name='action' value='accept'>Accept</button>";
        echo " <button type='submit' name='action' value='reject'>Reject</button>";
        echo "</form>";
    }
} else {
    echo "<p>No incoming requests.</p>";
}
?>

<p><a href='menu.php'>← Back to Menu</a></p>
</body>
</html>
