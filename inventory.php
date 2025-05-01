<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];

$query = "SELECT I.name, Inv.quantity 
          FROM Inventory Inv
          JOIN Characters C ON Inv.char_id = C.char_id
          JOIN Items I ON Inv.item_id = I.item_id
          WHERE C.user_id = $user_id";

$result = mysqli_query($conn, $query);
?>

<h2>Your Inventory</h2>
<ul>
<?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <li><?php echo $row['name'] . " x" . $row['quantity']; ?></li>
<?php } ?>
</ul>
