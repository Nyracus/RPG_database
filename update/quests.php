<?php
session_start();
include 'db.php';

function getXPThreshold($level) {
    $xpCurve = [1 => 100];
    for ($i = 2; $i <= 20; $i++) {
        $xpCurve[$i] = (int)($xpCurve[$i-1] * 1.25 + 50);
    }
    return $xpCurve[$level] ?? PHP_INT_MAX;
}

function getLevelPowerBoost($level) {
    return 5 + ($level * 2); 
}

function getRankByPower($power) {
    if ($power >= 999) return 'SSS';
    elseif ($power >= 850) return 'SS';
    elseif ($power >= 700) return 'S';
    elseif ($power >= 500) return 'A';
    elseif ($power >= 350) return 'B';
    elseif ($power >= 200) return 'C';
    elseif ($power >= 120) return 'D';
    elseif ($power >= 50) return 'E';
    else return 'F';
}

function applyLevelUp(&$char) {
    while ($char['level'] < 20) {
        $xp_needed = getXPThreshold($char['level']);
        if ($char['xp'] < $xp_needed) break;

        $char['xp'] -= $xp_needed;
        $char['level']++;
        $char['power'] += getLevelPowerBoost($char['level']);
    }

    $char['rank'] = getRankByPower($char['power']);
}


if (isset($_POST['mark_completed']) && isset($_POST['accept_id'])) {
    $accept_id = $_POST['accept_id'];
    mysqli_query($conn, "UPDATE QuestAcceptance SET completion_status = 'Pending' WHERE accept_id = $accept_id");
}

if (isset($_POST['approve_completion']) && isset($_POST['accept_id'])) {
    $accept_id = (int)$_POST['accept_id'];

    // Step 1: Get quest and character
    $info = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT quest_id, char_id FROM QuestAcceptance WHERE accept_id = $accept_id"));

    $quest_id = $info['quest_id'];
    $char_id = $info['char_id'];

    // Step 2: Get current XP, level, power, coins
    $char = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT xp, level, power, coins FROM Characters WHERE char_id = $char_id"));

    $xp = $char['xp'];
    $level = $char['level'];
    $power = $char['power'];
    $coins = $char['coins'];

    // Step 3: Apply rewards


    // Get quest and char
    $info = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT quest_id, char_id FROM QuestAcceptance WHERE accept_id = $accept_id"));

    $quest_id = $info['quest_id'];
    $char_id = $info['char_id'];

    // Get rewards
    $rewards = mysqli_query($conn, "SELECT * FROM QuestRewards WHERE quest_id = $quest_id");

    while ($r = mysqli_fetch_assoc($rewards)) {
        if ($r['reward_type'] === 'coin') {
            mysqli_query($conn, "UPDATE Characters SET coins = coins + {$r['reward_value']} WHERE char_id = $char_id");
        } elseif ($r['reward_type'] === 'xp') {
            mysqli_query($conn, "UPDATE Characters SET xp = xp + {$r['reward_value']} WHERE char_id = $char_id");
        } elseif ($r['reward_type'] === 'item') {
            $item_id = $r['item_id'];
            $qty = $r['reward_value'];
            $check = mysqli_query($conn, "SELECT * FROM Inventory WHERE char_id = $char_id AND item_id = $item_id");
            if (mysqli_num_rows($check) > 0) {
                mysqli_query($conn, "UPDATE Inventory SET quantity = quantity + $qty WHERE char_id = $char_id AND item_id = $item_id");
            } else {
                mysqli_query($conn, "INSERT INTO Inventory (char_id, item_id, quantity) VALUES ($char_id, $item_id, $qty)");
            }
        }
    }

    // Get current character values
    $char_stats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT xp, level, power FROM Characters WHERE char_id = $char_id"));

    $char = [
        'xp' => $char_stats['xp'],
        'level' => $char_stats['level'],
        'power' => $char_stats['power']
    ];

    applyLevelUp($char);

    // Apply XP/rank/level/power
    mysqli_query($conn,
        "UPDATE Characters SET 
            xp = {$char['xp']},
            level = {$char['level']},
            power = {$char['power']},
            rank = '{$char['rank']}'
        WHERE char_id = $char_id");


    
    mysqli_query($conn, "UPDATE QuestAcceptance SET completion_status = 'Completed' WHERE accept_id = $accept_id");
}

if (isset($_POST['reject_completion']) && isset($_POST['accept_id'])) {
    $id = $_POST['accept_id'];
    mysqli_query($conn, "UPDATE QuestAcceptance SET completion_status = 'Rejected' WHERE accept_id = $id");
}


if (isset($_POST['approve_quest']) && isset($_POST['accept_id'])) {
    $id = $_POST['accept_id'];
    mysqli_query($conn, "UPDATE QuestAcceptance SET status = 'Accepted' WHERE accept_id = $id");
}
if (isset($_POST['reject_quest']) && isset($_POST['accept_id'])) {
    $id = $_POST['accept_id'];
    mysqli_query($conn, "UPDATE QuestAcceptance SET status = 'Rejected' WHERE accept_id = $id");
}


if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_query = mysqli_query($conn, "SELECT role FROM Users WHERE user_id = $user_id");
$role = mysqli_fetch_assoc($user_query)['role'];

if (isset($_POST['request_quest'])) {
    $quest_id = $_POST['request_quest'];

    // Get current player's character ID
    $char_result = mysqli_query($conn, "SELECT char_id FROM Characters WHERE user_id = $user_id");
    $char = mysqli_fetch_assoc($char_result);

    if ($char) {
        $char_id = $char['char_id'];

        // Check if already requested
        $check = mysqli_query($conn, "SELECT * FROM QuestAcceptance WHERE quest_id = $quest_id AND char_id = $char_id");
        if (mysqli_num_rows($check) === 0) {
            mysqli_query($conn, "INSERT INTO QuestAcceptance (quest_id, char_id) VALUES ($quest_id, $char_id)");
            echo "<p style='color:green;'> Request sent!</p>";
        } else {
            echo "<p style='color:orange;'> You've already requested this quest.</p>";
        }
    }
}


?>

<!DOCTYPE html>
<html>
<head><title>Quests</title>
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

    .quest-container {
        max-width: 1000px;
        margin: 80px auto;
        background-color: rgba(30, 30, 30, 0.95);
        padding: 30px;
        border-radius: 14px;
        box-shadow: 0 0 20px #000;
        color: #f0e6d2;
    }

    h1, h2, h3 {
        color: #ffd700;
        text-align: center;
    }

    .quest-block {
        border: 1px solid #555;
        background-color: #1e1e1e;
        padding: 15px;
        border-radius: 10px;
        margin: 15px 0;
    }

    .quest-block em {
        color: #aaa;
        font-style: normal;
    }

    form {
        margin-top: 10px;
    }

    .reward-block {
        margin-bottom: 10px;
    }

    input[type="text"],
    input[type="number"],
    input[type="date"],
    select,
    textarea {
        width: 100%;
        max-width: 400px;
        background-color: #333;
        color: #fff;
        padding: 8px;
        border: 1px solid #777;
        margin-bottom: 10px;
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

<div class="quest-container">


<?php
$items = mysqli_query($conn, "SELECT item_id, name FROM Items ORDER BY name");
$item_options = "";
while ($item = mysqli_fetch_assoc($items)) {
    $item_options .= "<option value='{$item['item_id']}'>{$item['name']}</option>";
}
?>


<h1>Quests</h1>

<?php if ($role === 'GuildMaster'): ?>
    
    



    <h2><a href="quest_log.php">View Quest Log</a></h2>
    

    <h3>Incoming Quest Acceptance Requests</h3>

    <?php
    $requests = mysqli_query($conn,
        "SELECT QA.accept_id, QA.status, Q.name AS quest_name, C.name AS char_name, U.username
        FROM QuestAcceptance QA
        JOIN Quests Q ON QA.quest_id = Q.quest_id
        JOIN Characters C ON QA.char_id = C.char_id
        JOIN Users U ON C.user_id = U.user_id
        WHERE QA.status = 'Pending'
        ORDER BY QA.requested_at DESC");

    if (mysqli_num_rows($requests) > 0) {
        while ($row = mysqli_fetch_assoc($requests)) {
            echo "<div style='border:1px solid gray; padding:10px; margin-bottom:10px;'>";
            echo "<strong>{$row['char_name']} (User: {$row['username']})</strong><br>";
            echo "Requested Quest: <em>{$row['quest_name']}</em><br>";
            echo "<form method='post' style='display:inline;'>
                    <input type='hidden' name='accept_id' value='{$row['accept_id']}'>
                    <button name='approve_quest'>✅ Approve</button>
                    <button name='reject_quest'>❌ Reject</button>
                </form>";
            echo "</div>";
        }
    } else {
        echo "<p>No new quest requests.</p>";
    }
    ?>

    <h3>Quest Completion Requests</h3>

    <?php
    $requests = mysqli_query($conn,
        "SELECT QA.accept_id, QA.completion_status, Q.name AS quest_name, C.name AS char_name, U.username
        FROM QuestAcceptance QA
        JOIN Quests Q ON QA.quest_id = Q.quest_id
        JOIN Characters C ON QA.char_id = C.char_id
        JOIN Users U ON C.user_id = U.user_id
        WHERE QA.completion_status = 'Pending' AND QA.status = 'Accepted'");

    if (!$requests) {
        echo "<p style='color:red;'>Query failed: " . mysqli_error($conn) . "</p>";
    } elseif (mysqli_num_rows($requests) > 0) {
        
        while ($row = mysqli_fetch_assoc($requests)) {
            echo "<div style='border:1px solid gray; padding:10px; margin-bottom:10px;'>";
            echo "<strong>{$row['char_name']} (User: {$row['username']})</strong><br>";
            echo "Completed Quest: <em>{$row['quest_name']}</em><br>";
            echo "<form method='post' style='display:inline;'>
                    <input type='hidden' name='accept_id' value='{$row['accept_id']}'>
                    <button name='approve_completion'>✅ Approve</button>
                    <button name='reject_completion'>❌ Reject</button>
                </form>";
            echo "</div>";
        }
    } else {
        echo "<p>No new completion requests.</p>";
    }
    ?>


    
    <h2> Post a New Quest</h2>
    <?php
    if (isset($_POST['post_quest'])) {
        $name = $_POST['name'];
        $area = $_POST['area'];
        $rewards = $_POST['rewards'];
        $rank = $_POST['suggested_rank'];
        $desc = $_POST['description'];
        $deadline = $_POST['deadline'];
        $special = $_POST['special_requests'];

        $insert_quest = "INSERT INTO Quests (name, area, suggested_rank, description, deadline, special_requests)
                     VALUES ('$name', '$area', '$rank', '$desc', '$deadline', '$special')";
    
        if (mysqli_query($conn, $insert_quest)) {
            $quest_id = mysqli_insert_id($conn);

            
            $reward_types = $_POST['reward_type'];
            $reward_values = $_POST['reward_value'];
            $item_ids = $_POST['item_id'];

            for ($i = 0; $i < count($reward_types); $i++) {
                $type = $reward_types[$i];
                $value = (int)$reward_values[$i];
                $item_id = $item_ids[$i] !== "" ? (int)$item_ids[$i] : "NULL";

                if ($type !== "" && $value > 0) {
                    $query = "INSERT INTO QuestRewards (quest_id, reward_type, reward_value, item_id)
                            VALUES ($quest_id, '$type', $value, $item_id)";
                    mysqli_query($conn, $query);
                }
            }

            echo "<p style='color:green;'>✅ Quest posted with rewards!</p>";
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

        <label>Quest Rewards</label>
        <div id="reward-container">
            <!-- Reward Block Template (First one visible) -->
            <div class="reward-block">
                <select name="reward_type[]">
                    <option value="">--Type--</option>
                    <option value="coin">Coins</option>
                    <option value="xp">XP</option>
                    <option value="item">Item</option>
                </select>

                <input type="number" name="reward_value[]" placeholder="Amount" min="1">

                <select name="item_id[]">
                    <option value="">--Item (if type = item)--</option>
                    <?php
                    $items = mysqli_query($conn, "SELECT item_id, name FROM Items ORDER BY name");
                    while ($item = mysqli_fetch_assoc($items)) {
                        echo "<option value='{$item['item_id']}'>{$item['name']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <br>
        <button type="button" id="add-reward">Add another reward</button>
        <p id="limit-message" style="color:red; display:none;">⚠️ You can only add up to 10 rewards.</p>

        <br>

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

    <h3>Your Quest Requests</h3>
    <?php
    $char_id_result = mysqli_query($conn, "SELECT char_id FROM Characters WHERE user_id = $user_id");
    $char_id = mysqli_fetch_assoc($char_id_result)['char_id'];

    
    $my_quests = mysqli_query($conn,
        "SELECT Q.name, QA.status 
        FROM QuestAcceptance QA
        JOIN Quests Q ON QA.quest_id = Q.quest_id
        WHERE QA.char_id = $char_id
        ORDER BY QA.requested_at DESC");

    while ($row = mysqli_fetch_assoc($my_quests)) {
        echo "<p><strong>{$row['name']}</strong>: {$row['status']}</p>";
    }
    ?>

    <h3>Your Accepted Quests</h3>

    <?php
    $char_id = mysqli_fetch_assoc(mysqli_query($conn, "SELECT char_id FROM Characters WHERE user_id = $user_id"))['char_id'];

    $my_accepted = mysqli_query($conn,
        "SELECT QA.accept_id, Q.name, QA.status, QA.completion_status
        FROM QuestAcceptance QA
        JOIN Quests Q ON QA.quest_id = Q.quest_id
        WHERE QA.char_id = $char_id AND QA.status = 'Accepted'");

    if (mysqli_num_rows($my_accepted) > 0) {
        while ($q = mysqli_fetch_assoc($my_accepted)) {
            echo "<div style='border:1px solid gray; padding:10px; margin-bottom:10px;'>";
            echo "<strong>{$q['name']}</strong><br>";
            echo "Status: {$q['status']}<br>";

            if ($q['completion_status'] === 'Completed') {
                echo "<em>✅ Marked Completed</em>";
            } elseif ($q['completion_status'] === 'Rejected') {
                echo "<em>❌ Completion Rejected</em>";
            } elseif ($q['completion_status'] === 'Pending') {
                echo "<em>⏳ Awaiting GM Approval</em>";
            } else {
                echo "<form method='post'>
                        <input type='hidden' name='accept_id' value='{$q['accept_id']}'>
                        <button name='mark_completed'>Mark as Completed</button>
                    </form>";
            }

            echo "</div>";
        }
    }
    ?>


    <h2>Available Quests</h2>

    <form method="get">
    <label for="sort_by">Sort by:</label>
    <select name="sort_by" id="sort_by" onchange="this.form.submit()">
        <option value="">--Default--</option>
        <option value="name_asc" <?= ($_GET['sort_by'] ?? '') == 'name_asc' ? 'selected' : '' ?>>Name A–Z</option>
        <option value="name_desc" <?= ($_GET['sort_by'] ?? '') == 'name_desc' ? 'selected' : '' ?>>Name Z–A</option>
        <option value="rank" <?= ($_GET['sort_by'] ?? '') == 'rank' ? 'selected' : '' ?>>Suggested Rank (SSS→F)</option>
        <option value="deadline" <?= ($_GET['sort_by'] ?? '') == 'deadline' ? 'selected' : '' ?>>Deadline (Soonest)</option>
    </select>
    </form>

    <?php

    $order = "created_at DESC"; // default

    if (isset($_GET['sort_by'])) {
        switch ($_GET['sort_by']) {
            case 'name_asc':
                $order = "name ASC";
                break;
            case 'name_desc':
                $order = "name DESC";
                break;
            case 'rank':
                $order = "FIELD(suggested_rank, 'SSS','SS','S','A','B','C','D','E','F')";
                break;
            case 'deadline':
                $order = "deadline ASC";
                break;
        }
    }

    $quests = mysqli_query($conn,
        "SELECT * FROM Quests
        WHERE quest_id NOT IN (
            SELECT quest_id FROM QuestAcceptance WHERE char_id = $char_id
        )
        ORDER BY $order");



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

<script>
    const maxRewards = 10;
    let rewardCount = 1;

    const container = document.getElementById("reward-container");
    const addButton = document.getElementById("add-reward");
    const limitMessage = document.getElementById("limit-message");

    addButton.addEventListener("click", () => {
        if (rewardCount >= maxRewards) {
            limitMessage.style.display = "block";
            addButton.disabled = true;
            return;
        }

        const block = document.createElement("div");
        block.classList.add("reward-block");

        block.innerHTML = `
            <select name="reward_type[]">
                <option value="">--Type--</option>
                <option value="coin">Coins</option>
                <option value="xp">XP</option>
                <option value="item">Item</option>
            </select>

            <input type="number" name="reward_value[]" placeholder="Amount" min="1">

            <select name="item_id[]">
                <option value="">--Item (if type = item)--</option>
                <?= $item_options ?> 
            </select>
            <br>
        `;

        container.appendChild(block);
        rewardCount++;

        if (rewardCount === maxRewards) {
            limitMessage.style.display = "block";
            addButton.disabled = true;
        }
    });
</script>



</div> <!-- end .quest-container -->
</body>
</html>
