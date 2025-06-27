<?php
include 'config.php';
include 'header.php';

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
?>
<h2><i class="fa fa-door-open"></i> Rooms</h2>
<?php
if ($isAdmin) {
    // Add Room Form
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
        $number = $conn->real_escape_string($_POST['room_number']);
        $type = $conn->real_escape_string($_POST['type']);
        $price = $conn->real_escape_string($_POST['price']);
        $conn->query("INSERT INTO rooms (room_number, type, price) VALUES ('$number', '$type', '$price')");
        echo "<div class='success'>Room added!</div>";
    }
    ?>
    <form method="post" class="room-form">
        <input type="text" name="room_number" placeholder="Room Number" required>
        <input type="text" name="type" placeholder="Type" required>
        <input type="number" name="price" placeholder="Price" required>
        <button type="submit" name="add"><i class="fa fa-plus"></i> Add Room</button>
    </form>
<?php } ?>

<table>
    <tr>
        <th>Room No.</th>
        <th>Type</th>
        <th>Price</th>
        <th>Status</th>
        <?php if($isAdmin) echo "<th>Actions</th>"; ?>
    </tr>
    <?php
    $res = $conn->query("SELECT * FROM rooms");
    while ($row = $res->fetch_assoc()) {
        echo "<tr>
            <td>{$row['room_number']}</td>
            <td>{$row['type']}</td>
            <td>\${$row['price']}</td>
            <td>{$row['status']}</td>";
        if ($isAdmin) {
            echo "<td>
                <a href='edit_room.php?id={$row['id']}'><i class='fa fa-edit'></i></a>
                <a href='delete_room.php?id={$row['id']}' onclick='return confirm(\"Delete this room?\")'><i class='fa fa-trash'></i></a>
                </td>";
        }
        echo "</tr>";
    }
    ?>
</table>
<?php include 'footer.php'; ?>