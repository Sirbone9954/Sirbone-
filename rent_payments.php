<?php
session_start(); // Always the first line!

include 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
include 'header.php';

echo "<h2><i class='fa fa-money-bill'></i> Rent Payments</h2>";
echo "<table>
    <tr><th>Tenant</th><th>Room</th><th>Amount</th><th>Date</th><th>Status</th></tr>";
$res = $conn->query("SELECT u.full_name, r.room_number, p.amount, p.payment_date, p.status
    FROM payments p
    INNER JOIN tenants t ON p.tenant_id = t.id
    INNER JOIN users u ON t.user_id = u.id
    LEFT JOIN rooms r ON t.room_id = r.id
    ORDER BY p.payment_date DESC");
while ($row = $res->fetch_assoc()) {
    echo "<tr>
        <td>" . htmlspecialchars($row['full_name']) . "</td>
        <td>" . ($row['room_number'] ? htmlspecialchars($row['room_number']) : 'None') . "</td>
        <td>$" . htmlspecialchars($row['amount']) . "</td>
        <td>" . htmlspecialchars($row['payment_date']) . "</td>
        <td>" . htmlspecialchars($row['status']) . "</td>
    </tr>";
}
echo "</table>";
include 'footer.php';
?>