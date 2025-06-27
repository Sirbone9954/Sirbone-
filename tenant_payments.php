<?php
session_start(); // MUST be first line!

include 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tenant') {
    header("Location: index.php");
    exit;
}
include 'header.php';

$uid = intval($_SESSION['userid']);

// Get tenant id safely
$tenRes = $conn->query("SELECT id FROM tenants WHERE user_id=$uid");
$ten = $tenRes ? $tenRes->fetch_assoc() : null;
if (!$ten) {
    echo "<div class='error'>Tenant not found. Please contact administration.</div>";
    include 'footer.php';
    exit;
}
$tenant_id = intval($ten['id']);

// Handle payment (simulate, no real payment gateway)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pay'])) {
    $amount = floatval($_POST['amount']);
    if ($amount > 0) {
        $stmt = $conn->prepare("INSERT INTO payments (tenant_id, amount, payment_date, status) VALUES (?, ?, CURDATE(), 'paid')");
        $stmt->bind_param("id", $tenant_id, $amount);
        if ($stmt->execute()) {
            echo "<div class='success'>Payment recorded!</div>";
        } else {
            echo "<div class='error'>Failed to record payment. Please try again.</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='error'>Invalid payment amount.</div>";
    }
}

// Get payment history
echo "<h2><i class='fa fa-money-bill'></i> My Rent Payments</h2>";
?>
<form method='post'>
    <input type='number' name='amount' placeholder='Amount' min='1' step='0.01' required>
    <button type='submit' name='pay'><i class='fa fa-credit-card'></i> Pay Rent</button>
</form>
<table>
    <tr><th>Amount</th><th>Date</th><th>Status</th></tr>
<?php
$res = $conn->query("SELECT amount, payment_date, status FROM payments WHERE tenant_id=$tenant_id ORDER BY payment_date DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        echo "<tr>
            <td>$" . htmlspecialchars($row['amount']) . "</td>
            <td>" . htmlspecialchars($row['payment_date']) . "</td>
            <td>" . htmlspecialchars($row['status']) . "</td>
        </tr>";
    }
}
?>
</table>
<?php include 'footer.php'; ?>