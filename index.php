<?php
include 'config.php'; include 'header.php';
if(isset($_SESSION['role'])) {
  if($_SESSION['role']=='admin') header("Location: admin.php");
  else header("Location: tenant.php");
  exit;
}
?>
<div class="centerbox">
  <h2>Login</h2>
  <form method="post" action="login.php">
    <input name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <select name="role">
      <option value="admin">Admin</option>
      <option value="tenant">Tenant</option>
    </select>
    <button type="submit"><i class="fa fa-sign-in-alt"></i> Login</button>
  </form>
  <div>
    <a href="register.php"><i class="fa fa-user-plus"></i> Register as Tenant</a>
  </div>
</div>
<?php include 'footer.php'; ?>