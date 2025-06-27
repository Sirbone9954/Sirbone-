<?php
include 'config.php'; include 'header.php';
$msg = "";
if($_SERVER["REQUEST_METHOD"]=="POST") {
  $username = $conn->real_escape_string($_POST['username']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $full_name = $conn->real_escape_string($_POST['full_name']);
  $email = $conn->real_escape_string($_POST['email']);
  $exists = $conn->query("SELECT * FROM users WHERE username='$username'");
  if($exists->num_rows) $msg = "Username exists!";
  else {
    $conn->query("INSERT INTO users (username,password,role,full_name,email) VALUES ('$username','$password','tenant','$full_name','$email')");
    $uid = $conn->insert_id;
    $conn->query("INSERT INTO tenants (user_id) VALUES ($uid)");
    $msg = "Registered! <a href='index.php'>Login</a>";
  }
}
?>
<div class="centerbox">
  <h2>Tenant Registration</h2>
  <form method="post">
    <input name="username" placeholder="Username" required>
    <input name="full_name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit"><i class="fa fa-user-plus"></i> Register</button>
  </form>
  <div><?=$msg?></div>
  <a href="index.php"><i class="fa fa-user-plus"></i> Back to Login</a>
</div>
<?php include 'footer.php'; ?>