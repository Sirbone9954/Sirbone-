<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Las Vegas Hostel Rent Management</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="main.js" defer></script>
</head>
<body>
<header>
  <h1><i class="fa-solid fa-hotel"></i> Las Vegas Hostel</h1>
  <?php if(isset($_SESSION['role'])): ?>
    <nav>
      <select id="panel-nav" onchange="location=this.value">
        <?php if($_SESSION['role']=='admin'): ?>
          <option value="admin.php" <?=basename($_SERVER['PHP_SELF'])=='admin.php'?'selected':''?>>Admin Dashboard</option>
        <?php else: ?>
          <option value="tenant.php" <?=basename($_SERVER['PHP_SELF'])=='tenant.php'?'selected':''?>>Tenant Panel</option>
        <?php endif; ?>
        <option value="logout.php">Logout</option>
      </select>
    </nav>
  <?php endif; ?>
</header>
<main>