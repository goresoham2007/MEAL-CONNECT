<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!empty($_SESSION['owner_id'])) { header('Location: dashboard.php'); exit; }

$errors = [];
$phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = 'Phone number must be exactly 10 digits';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, full_name, password_hash FROM owners WHERE phone=?");
        mysqli_stmt_bind_param($stmt, 's', $phone);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $owner = mysqli_fetch_assoc($res);
        if ($owner && password_verify($password, $owner['password_hash'])) {
            $_SESSION['owner_id'] = $owner['id'];
            $_SESSION['owner_name'] = $owner['full_name'];
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Invalid phone number or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Mess Owner Login — MealConnect</title>
<?php include __DIR__ . '/../includes/head.php'; ?>
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-side" style="background:var(--ink)">
      <span class="mark" style="background:#fff2"><svg viewBox="0 0 24 24" fill="none"><path d="M6 3v8a3 3 0 003 3v7M6 3v8M9 3v8M12 3v6a2 2 0 002 2h1v9m0-13V3" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
      <h2>Owner Dashboard</h2>
      <p>Manage your mess listing, students and attendance in one place.</p>
      <div style="background:#ffffff26;border-radius:12px;padding:14px 16px;font-size:.85rem;margin-top:10px">
        <b>Demo login</b><br>Phone: 9876500001<br>Password: Owner@123
      </div>
    </div>
    <div class="auth-form">
      <div class="auth-tabs">
        <a href="login.php" class="active">Login</a>
        <a href="register.php">Register</a>
      </div>
      <?php if ($errors): ?>
        <div class="alert alert-error"><?php foreach ($errors as $e) echo h($e) . '<br>'; ?></div>
      <?php endif; ?>
      <form method="post">
        <div class="field"><label>Phone Number</label>
          <input type="text" name="phone" maxlength="10" value="<?= h($phone) ?>" placeholder="10-digit mobile number" required></div>
        <div class="field"><label>Password</label>
          <input type="password" name="password" placeholder="Your password" required></div>
        <button type="submit" class="btn btn-dark btn-block">Login</button>
      </form>
      <p style="text-align:center;font-size:.85rem;color:var(--ink-soft);margin-top:16px">New mess owner? <a href="register.php" style="color:var(--orange);font-weight:600">Create an account</a></p>
      <p style="text-align:center;font-size:.85rem;color:var(--ink-soft)">Student? <a href="../student/login.php" style="color:var(--orange);font-weight:600">Login here</a></p>
    </div>
  </div>
</div>
</body>
</html>
