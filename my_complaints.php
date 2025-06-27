<?php
include 'config.php';
include 'header.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'tenant') header("Location: index.php");
$uid = $_SESSION['userid'];

// Get tenant id
$tenRes = $conn->query("SELECT id FROM tenants WHERE user_id=$uid");
$ten = $tenRes->fetch_assoc();
$tenant_id = $ten['id'];

// Submit complaint
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['complaint'])) {
    $content = $conn->real_escape_string($_POST['content']);
    $conn->query("INSERT INTO complaints (tenant_id, content) VALUES ($tenant_id, '$content')");
    echo "<div class='success'>Complaint/request submitted!</div>";
}

// List complaints
echo "<h2><i class='fa fa-comments'></i> My Complaints/Requests</h2>";
echo "<form method='post'>
        <textarea name='content' placeholder='Enter your complaint or request here...' required></textarea>
        <button type='submit' name='complaint'><i class='fa fa-paper-plane'></i> Submit</button>
    </form>";
echo "<table>
    <tr><th>Complaint/Request</th><th>Date</th><th>Response</th></tr>";
$res = $conn->query("SELECT content, created_at, response FROM complaints WHERE tenant_id=$tenant_id ORDER BY created_at DESC");
while ($row = $res->fetch_assoc()) {
    echo "<tr>
        <td>{$row['content']}</td>
        <td>{$row['created_at']}</td>
        <td>{$row['response']}</td>
    </tr>";
}
echo "</table>";
include 'footer.php';
?>