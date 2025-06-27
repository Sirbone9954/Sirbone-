<?php
include 'config.php';
include 'header.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') header("Location: index.php");

// Assign room
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign'])) {
    $tenant_id = intval($_POST['tenant_id']);
    $room_id = intval($_POST['room_id']);
    $conn->query("UPDATE tenants SET room_id=$room_id WHERE id=$tenant_id");
    $conn->query("UPDATE rooms SET status='occupied' WHERE id=$room_id");
    echo "<div class='success'>Room assigned!</div>";
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
            <td>{$row['full_name']}</td>
            <td>{$row['email']}</td>
            <td>{$row['room_number']}</td>
            <td>
                <form method='post'>
                    <input type='hidden' name='tenant_id' value='{$row['tid']}'>
                    <select name='room_id'>";
        $roomRes = $conn->query("SELECT id, room_number FROM rooms WHERE status='available'");
        while ($room = $roomRes->fetch_assoc()) {
            echo "<option value='{$room['id']}'>{$room['room_number']}</option>";
        }
        echo "  </select>
                    <button type='submit' name='assign'><i class='fa fa-check'></i> Assign</button>
                </form>
            </td>
        </tr>";
    }
    ?>
</table>
<?php include 'footer.php'; ?>