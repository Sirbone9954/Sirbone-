<?php
include 'config.php'; include 'header.php';
if(!isset($_SESSION['role'])||$_SESSION['role']!='admin') header("Location: index.php");

function tabActive($tab) {
  return (isset($_GET['tab']) && $_GET['tab']==$tab) ? 'active' : '';
}
$tab = $_GET['tab'] ?? 'rooms';
?>
<h2><i class="fa-solid fa-user-shield"></i> Admin Panel</h2>
<div class="tabmenu">
  <button onclick="location='?tab=rooms'" class="<?=tabActive('rooms')?>"><i class="fa fa-door-open"></i> Rooms</button>
  <button onclick="location='?tab=tenants'" class="<?=tabActive('tenants')?>"><i class="fa fa-users"></i> Tenants</button>
  <button onclick="location='?tab=payments'" class="<?=tabActive('payments')?>"><i class="fa fa-money-bill"></i> ₵ Payments
  <button onclick="location='?tab=complaints'" class="<?=tabActive('complaints')?>"><i class="fa fa-comments"></i> Requests</button>
</div>
<div class="tabbody">
<?php
if($tab=='rooms'){
  // Room CRUD (Add/Edit/Delete)
  if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['addroom'])) {
    $conn->query("INSERT INTO rooms (number,type,price) VALUES ('{$_POST['number']}','{$_POST['type']}','{$_POST['price']}')");
    echo "<div class='success'>Room added!</div>";
  }
  if(isset($_GET['del'])) {
    $rid = intval($_GET['del']);
    $conn->query("DELETE FROM rooms WHERE id=$rid");
    echo "<div class='success'>Room deleted!</div>";
  }
  ?>
  <form method="post" class="rowform">
    <input name="number" placeholder="Room #" required>
    <input name="type" placeholder="Type" required>
    <input name="price" type="number" placeholder="Price" required>
    <button name="addroom"><i class="fa fa-plus"></i> Add Room</button>
  </form>
  <table>
    <tr><th>#</th><th>Type</th><th>Price</th><th>Status</th><th>Actions</th></tr>
    <?php
    $r = $conn->query("SELECT * FROM rooms");
    while($row = $r->fetch_assoc()){
      echo "<tr>
        <td>{$row['number']}</td>
        <td>{$row['type']}</td>
        <td>\${$row['price']}</td>
        <td>{$row['status']}</td>
        <td>
          <a href='?tab=rooms&del={$row['id']}' onclick='return confirm(\"Delete?\")'><i class='fa fa-trash'></i></a>
        </td>
      </tr>";
    }
    ?>
  </table>
  <?php
}
elseif($tab=='tenants'){
  // View tenants and assign rooms
  if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['assignroom'])) {
    $tid = intval($_POST['tenant_id']);
    $rid = intval($_POST['room_id']);
    $conn->query("UPDATE tenants SET room_id=$rid WHERE id=$tid");
    $conn->query("UPDATE rooms SET status='occupied' WHERE id=$rid");
    echo "<div class='success'>Room assigned!</div>";
  }
  ?>
  <table>
    <tr><th>Name</th><th>Email</th><th>Room</th><th>Assign Room</th></tr>
    <?php
    $t = $conn->query("SELECT t.id AS tid, u.full_name, u.email, r.number AS roomnum FROM tenants t
      INNER JOIN users u ON t.user_id=u.id
      LEFT JOIN rooms r ON t.room_id=r.id");
    while($row = $t->fetch_assoc()){
      echo "<tr>
        <td>{$row['full_name']}</td>
        <td>{$row['email']}</td>
        <td>{$row['roomnum']}</td>
        <td>
          <form method='post' style='display:inline'>
            <input type='hidden' name='tenant_id' value='{$row['tid']}'>
            <select name='room_id'>
              <option value=''>Select</option>";
      $avail = $conn->query("SELECT id,number FROM rooms WHERE status='available'");
      while($ar = $avail->fetch_assoc())
        echo "<option value='{$ar['id']}'>{$ar['number']}</option>";
      echo "</select>
            <button name='assignroom'><i class='fa fa-check'></i></button>
          </form>
        </td>
      </tr>";
    }
    ?>
  </table>
  <?php
}
elseif($tab=='payments'){
  // Payment tracking
  echo "<table><tr><th>Tenant</th><th>Room</th><th>Amount</th><th>Date</th><th>Status</th></tr>";
  $p = $conn->query("SELECT u.full_name, r.number, pm.amount, pm.payment_date, pm.status 
    FROM payments pm
    INNER JOIN tenants t ON pm.tenant_id=t.id
    INNER JOIN users u ON t.user_id=u.id
    LEFT JOIN rooms r ON t.room_id=r.id
    ORDER BY pm.payment_date DESC");
  while($row=$p->fetch_assoc()){
    echo "<tr>
      <td>{$row['full_name']}</td>
      <td>{$row['number']}</td>
      <td>\${$row['amount']}</td>
      <td>{$row['payment_date']}</td>
      <td>{$row['status']}</td>
    </tr>";
  }
  echo "</table>";
}
elseif($tab=='complaints'){
  // Complaints/requests
  echo "<table><tr><th>Tenant</th><th>Request</th><th>Date</th><th>Response</th></tr>";
  $c = $conn->query("SELECT u.full_name, c.content, c.created_at, c.response
    FROM complaints c
    INNER JOIN tenants t ON c.tenant_id=t.id
    INNER JOIN users u ON t.user_id=u.id
    ORDER BY c.created_at DESC");
  while($row = $c->fetch_assoc()){
    echo "<tr>
      <td>{$row['full_name']}</td>
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