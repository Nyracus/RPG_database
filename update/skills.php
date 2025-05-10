<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get character info
$char = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT char_id, level FROM Characters WHERE user_id = $user_id"));
$char_id = $char['char_id'];
$char_level = $char['level'];
$MAX_EQUIPPED_SKILLS = 3;

// Handle equip/unequip
if (isset($_POST['toggle_skill']) && isset($_POST['skill_id'])) {
    $skill_id = (int)$_POST['skill_id'];

    $check = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT equipped FROM CharacterSkills WHERE char_id = $char_id AND skill_id = $skill_id"));

    if ($check) {
        if ($check['equipped']) {
            mysqli_query($conn, "UPDATE CharacterSkills SET equipped = 0 
                                 WHERE char_id = $char_id AND skill_id = $skill_id");
        } else {
            $count = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT COUNT(*) AS c FROM CharacterSkills 
                 WHERE char_id = $char_id AND equipped = 1"))['c'];

            if ($count < $MAX_EQUIPPED_SKILLS) {
                mysqli_query($conn, "UPDATE CharacterSkills SET equipped = 1 
                                     WHERE char_id = $char_id AND skill_id = $skill_id");
            } else {
                $message = "❌ You can equip up to $MAX_EQUIPPED_SKILLS skills only.";
            }
        }
    }
}

// Fetch all owned skills
$skills = mysqli_query($conn,
    "SELECT S.skill_id, S.name, S.description, S.power_value, S.required_level, CS.equipped
     FROM CharacterSkills CS
     JOIN Skills S ON CS.skill_id = S.skill_id
     WHERE CS.char_id = $char_id");
?>

<!DOCTYPE html>
<html>
<head>
    <title> Your Skills</title>
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

        .skills-wrapper {
            max-width: 1000px;
            margin: 80px auto;
            background-color: rgba(25, 25, 25, 0.95);
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 0 25px #000;
            color: #f0e6d2;
        }

        .skills-wrapper h2 {
            text-align: center;
            color: #ffd700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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

        td em {
            color: #888;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #9acd32;
        }

        .locked {
            color: #cc4444;
            font-style: italic;
        }

        button {
            margin-top: 5px;
        }
    </style>
</head>
<body>

<!-- 🎥 Background Video -->
<video autoplay muted loop id="bg-video">
    <source src="assets/background.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<div class="skills-wrapper">
    <h2> Skill Management</h2>

    <?php if (isset($message)) echo "<p style='color:red; text-align:center;'>$message</p>"; ?>

    <table>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Power</th>
            <th>Required Level</th>
            <th>Equipped?</th>
            <th>Action</th>
        </tr>

        <?php while ($s = mysqli_fetch_assoc($skills)): ?>
            <tr>
                <td><?= htmlspecialchars($s['name']) ?></td>
                <td><?= htmlspecialchars($s['description']) ?></td>
                <td>+<?= $s['power_value'] ?></td>
                <td><?= $s['required_level'] ?></td>
                <td><?= $s['equipped'] ? "✅" : "❌" ?></td>
                <td>
                    <?php if ($char_level >= $s['required_level']): ?>
                        <form method="post">
                            <input type="hidden" name="skill_id" value="<?= $s['skill_id'] ?>">
                            <button type="submit" name="toggle_skill">
                                <?= $s['equipped'] ? "Unequip" : "Equip" ?>
                            </button>
                        </form>
                    <?php else: ?>
                        <span class="locked">🔒 Lvl <?= $s['required_level'] ?> Required</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <div class="back-link">
        <a href="menu.php">← Back to Menu</a>
    </div>
</div>

</body>
</html>

