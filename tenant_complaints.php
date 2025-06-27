<?php
session_start(); // Always the very first line!

include 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tenant') {
    header("Location: index.php");
    exit;
}
include 'header.php';

$uid = intval($_SESSION['userid']);

// Get tenant id
$tenRes = $conn->query("SELECT id FROM tenants WHERE user_id=$uid");
$ten = $tenRes ? $tenRes->fetch_assoc() : null;
if (!$ten) {
    echo "<div class='error'>Tenant not found.</div>";
    include 'footer.php';
    exit;
}
$tenant_id = intval($ten['id']);

// Submit complaint
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['complaint'])) {
    $content = trim($_POST['content']);
    if ($content !== '') {
        $contentEsc = $conn->real_escape_string($content);
        $result = $conn->query("INSERT INTO complaints (tenant_id, content) VALUES ($tenant_id, '$contentEsc')");
        if ($result) {
            echo "<div class='success'>Complaint/request submitted!</div>";
        } else {
            echo "<div class='error'>Failed to submit your complaint. Please try again.</div>";
        }
    } else {
        echo "<div class='error'>Complaint/request cannot be empty.</div>";
    }
}

// List complaints
echo "<h2><i class='fa fa-comments'></i> My Complaints/Requests</h2>";
?>
<form method='post'>
    <textarea name='content' placeholder='Enter your complaint or request here...' required></textarea>
    <button type='submit' name='complaint'><i class='fa fa-paper-plane'></i> Submit</button>
</form>
<table>
    <tr><th>Complaint/Request</th><th>Date</th><th>Response</th></tr>
<?php
$res = $conn->query("SELECT content, created_at, response FROM complaints WHERE tenant_id=$tenant_id ORDER BY created_at DESC");
while ($row = $res->fetch_assoc()) {
    echo "<tr>
        <td>" . htmlspecialchars($row['content']) . "</td>
        <td>" . htmlspecialchars($row['created_at']) . "</td>
        <td>" . ($row['response'] ? htmlspecialchars($row['response']) : '-') . "</td>
    </tr>";
}
?>
</table>
<?php include 'footer.php'; ?>