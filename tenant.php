<?php
<?php
session_start();
include 'config.php'; 
include 'header.php';
if(!isset($_SESSION['role'])||$_SESSION['role']!='tenant') header("Location: index.php");
$uid = $_SESSION['userid'];
$ten = $conn->query("SELECT t.id AS tid, r.number, r.type, r.price FROM tenants t 
  LEFT JOIN rooms r ON t.room_id=r.id WHERE t.user_id=$uid")->fetch_assoc();

if ($ten) {
    $tenant_id = $ten['tid'];
} else {
    // Optionally: show an error, redirect, or use a default
    $tenant_id = 0;
}
$tab = $_GET['tab'] ?? 'profile';
?>
?>
<h2><i class="fa-solid fa-user"></i> Tenant Panel</h2>
<div class="tabmenu">
  <button onclick="location='?tab=profile'" class="<?=$tab=='profile'?'active':''?>"><i class="fa fa-id-badge"></i> My Profile</button>
  <button onclick="location='?tab=rooms'" class="<?=$tab=='rooms'?'active':''?>"><i class="fa fa-door-open"></i> Available Rooms</button>
  <button onclick="location='?tab=pay'" class="<?=$tab=='pay'?'active':''?>"><i class="fa fa-money-bill"></i> ₵ Payments
  <button onclick="location='?tab=requests'" class="<?=$tab=='requests'?'active':''?>"><i class="fa fa-comments"></i> My Requests</button>
</div>
<div class="tabbody">
<?php
if($tab=='profile'){
  echo "<div class='tenant-info'>
    <p><strong>Name:</strong> {$_SESSION['username']}</p>
    <p><strong>Room:</strong> ".($ten['number']??"Not assigned")."</p>
    <p><strong>Room Type:</strong> ".($ten['type']??"-")."</p>
    <p><strong>Rent:</strong> $".($ten['price']??"-")."</p>
  </div>";
}
elseif($tab=='rooms'){
  echo "<table><tr><th>Room #</th><th>Type</th><th>Price</th><th>Status</th></tr>";
  $r = $conn->query("SELECT * FROM rooms");
  while($row = $r->fetch_assoc()){
    echo "<tr>
      <td>{$row['number']}</td>
      <td>{$row['type']}</td>
      <td>\${$row['price']}</td>
      <td>{$row['status']}</td>
    </tr>";
  }
  echo "</table>";
}
elseif($tab=='pay'){
  if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['pay'])){
    $amount = $_POST['amount'];
    $conn->query("INSERT INTO payments (tenant_id,amount,payment_date,status) VALUES ($tenant_id,'$amount',CURDATE(),'paid')");
    echo "<div class='success'>Payment recorded!</div>";
  }
  echo "<form method='post' class='rowform'>
    <input type='number' name='amount' placeholder='Amount' required>
    <button name='pay'><i class='fa fa-credit-card'></i> Pay Rent</button>
  </form>";
  echo "<table><tr><th>Amount</th><th>Date</th><th>Status</th></tr>";
  $p = $conn->query("SELECT amount,payment_date,status FROM payments WHERE tenant_id=$tenant_id ORDER BY payment_date DESC");
  while($row = $p->fetch_assoc()){
    echo "<tr>
      <td>\${$row['amount']}</td>
      <td>{$row['payment_date']}</td>
      <td>{$row['status']}</td>
    </tr>";
  }
  echo "</table>";
}
elseif($tab=='requests'){
  if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['complaint'])){
    $content = $conn->real_escape_string($_POST['content']);
    $conn->query("INSERT INTO complaints (tenant_id,content) VALUES ($tenant_id,'$content')");
    echo "<div class='success'>Request sent!</div>";
  }
  echo "<form method='post' class='rowform'>
    <textarea name='content' placeholder='Your complaint/request...' required></textarea>
    <button name='complaint'><i class='fa fa-paper-plane'></i> Submit</button>
  </form>";
  echo "<table><tr><th>Request</th><th>Date</th><th>Response</th></tr>";
  $c = $conn->query("SELECT content,created_at,response FROM complaints WHERE tenant_id=$tenant_id ORDER BY created_at DESC");
  while($row = $c->fetch_assoc()){
    echo "<tr>
      <td>{$row['content']}</td>
      <td>{$row['created_at']}</td>
      <td>{$row['response']}</td>
    </tr>";
  }
  echo "</table>";
}
?>
</div>
<?php include 'footer.php'; ?>