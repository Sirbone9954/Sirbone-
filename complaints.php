<?php
include 'config.php';
include 'header.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') header("Location: index.php");

echo "<h2><i class='fa fa-comments'></i> Tenant Complaints/Requests</h2>";
echo "<table>
    <tr><th>Tenant</th><th>Complaint/Request</th><th>Date</th><th>Response</th></tr>";
$res = $conn->query("SELECT u.full_name, c.content, c.created_at, c.response
    FROM complaints c
    INNER JOIN tenants t ON c.tenant_id = t.id
    INNER JOIN users u ON t.user_id = u.id
    ORDER BY c.created_at DESC");
while ($row = $res->fetch_assoc()) {
    echo "<tr>
        <td>{$row['full_name']}</td>
        <td>{$row['content']}</td>
        <td>{$row['created_at']}</td>
        <td>{$row['response']}</td>
    </tr>";
}
echo "</table>";
include 'footer.php';
?>