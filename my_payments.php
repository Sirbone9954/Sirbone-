<?php
include 'config.php';
include 'header.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'tenant') header("Location: index.php");
$uid = $_SESSION['userid'];

// Get tenant id
$tenRes = $conn->query("SELECT id FROM tenants WHERE user_id=$uid");
$ten = $tenRes->fetch_assoc();
$tenant_id = $ten['id'];

// Handle payment (simulate, no real payment gateway)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pay'])) {
    $amount = $_POST['amount'];
    $conn->query("INSERT INTO payments (tenant_id, amount, payment_date, status) VALUES ($tenant_id, '$amount', CURDATE(), 'paid')");
    echo "<div class='success'>Payment recorded!</div>";
}

// Get payment history
echo "<h2><i class='fa fa-money-bill'></i> My Rent Payments</h2>";
echo "<form method='post'>
        <input type='number' name='amount' placeholder='Amount' required>
        <button type='submit' name='pay'><i class='fa fa-credit-card'></i> Pay Rent</button>
    </form>";
echo "<table>
    <tr><th>Amount</th><th>Date</th><th>Status</th></tr>";
$res = $conn->query("SELECT amount, payment_date, status FROM payments WHERE tenant_id=$tenant_id ORDER BY payment_date DESC");
while ($row = $res->fetch_assoc()) {
    echo "<tr>
        <td>\${$row['amount']}</td>
        <td>{$row['payment_date']}</td>
        <td>{$row['status']}</td>
    </tr>";
}
echo "</table>";
include 'footer.php';
?>