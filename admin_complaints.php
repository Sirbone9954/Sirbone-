<?php
include 'config.php';

// Always start session and check admin before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

include 'header.php';

echo "<h2><i class='fa fa-comments'></i> Tenant Complaints/Requests</h2>";
echo "<table>
    <tr>
        <th>Tenant</th>
        <th>Complaint/Request</th>
        <th>Date</th>
        <th>Response</th>
    </tr>";

$sql = "SELECT u.full_name, c.content, c.created_at, c.response
    FROM complaints c
    INNER JOIN tenants t ON c.tenant_id = t.id
    INNER JOIN users u ON t.user_id = u.id
    ORDER BY c.created_at DESC";

if ($res = $conn->query($sql)) {
    while ($row = $res->fetch_assoc()) {
        echo "<tr>
            <td>" . htmlspecialchars($row['full_name']) . "</td>
            <td>" . nl2br(htmlspecialchars($row['content'])) . "</td>
            <td>" . htmlspecialchars($row['created_at']) . "</td>
            <td>" . (!empty($row['response']) ? nl2br(htmlspecialchars($row['response'])) : "<em>No response</em>") . "</td>
        </tr>";
    }
    $res->free();
} else {
    echo "<tr><td colspan='4'>Error fetching complaints: " . htmlspecialchars($conn->error) . "</td></tr>";
}

echo "</table>";

include 'footer.php';
?>