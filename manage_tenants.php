<?php
session_start(); // Must be first!

include 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
include 'header.php';

// Assign room
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign'])) {
    $tenant_id = intval($_POST['tenant_id']);
    $room_id = intval($_POST['room_id']);
    // Check if selected room is still available
    $roomCheck = $conn->query("SELECT status FROM rooms WHERE id=$room_id")->fetch_assoc();
    if ($roomCheck && $roomCheck['status'] === 'available') {
        $conn->query("UPDATE tenants SET room_id=$room_id WHERE id=$tenant_id");
        $conn->query("UPDATE rooms SET status='occupied' WHERE id=$room_id");
        echo "<div class='success'>Room assigned!</div>";
    } else {
        echo "<div class='error'>Room already occupied or does not exist.</div>";
    }
}
?>

<h2><i class="fa fa-users"></i> Manage Tenants</h2>
<table>
    <tr>
        <th>Name</th><th>Email</th><th>Room</th><th>Assign Room</th>
    </tr>
    <?php
    $res = $conn->query("SELECT t.id AS tid, u.full_name, u.email, r.room_number, r.id AS rid FROM tenants t
        INNER JOIN users u ON t.user_id = u.id
        LEFT JOIN rooms r ON t.room_id = r.id");
    while ($row = $res->fetch_assoc()) {
        echo "<tr>
            <td>" . htmlspecialchars($row['full_name']) . "</td>
            <td>" . htmlspecialchars($row['email']) . "</td>
            <td>" . ($row['room_number'] ? htmlspecialchars($row['room_number']) : 'None') . "</td>
            <td>";
        // If tenant already has a room, don't allow assignment
        if ($row['room_number']) {
            echo "<span>Already assigned</span>";
        } else {
            echo "<form method='post' style='margin:0;'>
                    <input type='hidden' name='tenant_id' value='{$row['tid']}'>
                    <select name='room_id' required>";
            $roomRes = $conn->query("SELECT id, room_number FROM rooms WHERE status='available'");
            if ($roomRes->num_rows === 0) {
                echo "<option value=''>No rooms available</option>";
            } else {
                while ($room = $roomRes->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($room['id']) . "'>" . htmlspecialchars($room['room_number']) . "</option>";
                }
            }
            echo "</select>
                    <button type='submit' name='assign'><i class='fa fa-check'></i> Assign</button>
                </form>";
        }
        echo "</td></tr>";
    }
    ?>
</table>
<?php include 'footer.php'; ?>