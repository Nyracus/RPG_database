<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user and character info
$query = mysqli_query($conn, 
    "SELECT U.username, C.char_id, C.name AS char_name, C.level, C.xp, C.power, C.rank, C.coins
     FROM Users U 
     JOIN Characters C ON U.user_id = C.user_id 
     WHERE U.user_id = $user_id");


if (!$query || mysqli_num_rows($query) === 0) {
    echo "<p>No character found.</p>";
    exit();
}

$char = mysqli_fetch_assoc($query);
$char_id = $char['char_id'] ?? null;

if (!$char_id) {
    echo "<p>Error: Character ID not found.</p>";
    exit();
}


$equip = mysqli_query($conn,
    "SELECT E.slot, I.name, I.power_value
     FROM Equipment E
     JOIN Items I ON E.item_id = I.item_id
     WHERE E.char_id = $char_id");

$equipped_skills = mysqli_query($conn,
    "SELECT S.name, S.description, S.power_value
     FROM CharacterSkills CS
     JOIN Skills S ON CS.skill_id = S.skill_id
     WHERE CS.char_id = $char_id AND CS.equipped = 1");


// XP threshold function
function getXPThreshold($level) {
    $xpCurve = [1 => 100];
    for ($i = 2; $i <= 20; $i++) {
        $xpCurve[$i] = (int)($xpCurve[$i-1] * 1.25 + 50);
    }
    return $xpCurve[$level] ?? PHP_INT_MAX;
}

$xp_needed = getXPThreshold($char['level']);
?>

<!DOCTYPE html>
<html>
<head><title>Character Stats</title><link rel="stylesheet" type="text/css" href="style.css">
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

    .stats-wrapper {
        max-width: 900px;
        margin: 80px auto;
        background-color: rgba(30, 30, 30, 0.95);
        padding: 30px;
        border-radius: 14px;
        box-shadow: 0 0 25px #000;
        color: #f0e6d2;
    }

    h1, h3 {
        text-align: center;
        color: #ffd700;
    }

    p {
        margin: 8px 0;
    }

    ul {
        margin-left: 20px;
    }

    strong {
        color: #ffd700;
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

<!-- 🎥 Background Video -->
<video autoplay muted loop id="bg-video">
    <source src="assets/background.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<div class="stats-wrapper">


<h1>Character Stats</h1>

<p><strong>User:</strong> <?= htmlspecialchars($char['username']) ?></p>
<p><strong>Character:</strong> <?= htmlspecialchars($char['char_name']) ?></p>
<p><strong>Level:</strong> <?= $char['level'] ?></p>
<p><strong>XP:</strong> <?= $char['xp'] ?> / <?= $xp_needed ?></p>
<p><strong>Power:</strong> <?= $char['power'] >= 999 ? '999+' : $char['power'] ?></p>
<p><strong>Rank:</strong> <?= $char['rank'] ?></p>
<p><strong>Coins:</strong> <?= $char['coins'] ?></p>
<h3>Equipped Items</h3>
<ul>
<?php
$total_equip_power = 0;
while ($eq = mysqli_fetch_assoc($equip)) {
    $total_equip_power += $eq['power_value'];
    echo "<li><strong>{$eq['slot']}:</strong> {$eq['name']} (+{$eq['power_value']} power)</li>";
}
?>
</ul>
<p><strong>Power from Equipment:</strong> <?= $total_equip_power ?></p>

<?php
$skill_power = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT SUM(S.power_value) AS total FROM CharacterSkills CS
     JOIN Skills S ON CS.skill_id = S.skill_id
     WHERE CS.char_id = $char_id AND CS.equipped = 1"))['total'] ?? 0;
?>
<h3>Equipped Skills</h3>
<?php
$total_skill_power = 0;
if (mysqli_num_rows($equipped_skills) === 0) {
    echo "<p>No skills equipped.</p>";
} else {
    echo "<ul>";
    while ($skill = mysqli_fetch_assoc($equipped_skills)) {
        $total_skill_power += $skill['power_value'];
        echo "<li><strong>{$skill['name']}</strong>: {$skill['description']} (+{$skill['power_value']} power)</li>";
    }
    echo "</ul>";
}
?>

<p><strong>Power from Skills:</strong> <?= $skill_power ?></p> <br>
<p><strong>Total Power:</strong> <?= $char['power'] + $total_equip_power + $skill_power ?></p>

<br>
<p><a href="menu.php">← Back to Menu</a></p>
</body>
</html>
