<?php
include 'config.php'; session_start();
if($_SERVER["REQUEST_METHOD"]=="POST") {
  $username = $conn->real_escape_string($_POST['username']);
  $role = $_POST['role'];
  $sql = "SELECT * FROM users WHERE username='$username' AND role='$role'";
  $res = $conn->query($sql);
  if($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    if(password_verify($_POST['password'], $row['password'])) {
      $_SESSION['userid'] = $row['id'];
      $_SESSION['role'] = $row['role'];
      $_SESSION['username'] = $row['username'];
      if($role=="admin") header("Location: admin.php");
      else header("Location: tenant.php");
      exit;
    }
  }
  header("Location: index.php");
}
?>