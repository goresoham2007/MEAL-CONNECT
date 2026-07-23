<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!empty($_SESSION['owner_id'])) { header('Location: dashboard.php'); exit; }

$errors = [];
$full_name = $phone = $business_name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $business_name = trim($_POST['business_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!preg_match('/^[0-9]{10}$/', $phone)) $errors[] = 'Phone number must be exactly 10 digits';
    if (!preg_match('/^[A-Za-z ]{2,50}$/', $full_name)) $errors[] = 'Enter a valid full name';
    if (trim($business_name) === '') $errors[] = 'Business / mess name is required';
    if (!(strlen($password) >= 8 && strlen($password) <= 32
        && preg_match('/[A-Z]/', $password) && preg_match('/[0-9]/', $password) && preg_match('/[!@#$%^&*]/', $password))) {
        $errors[] = 'Password must include uppercase, number, and special character';
    }
    if ($password !== $confirm) $errors[] = 'Passwords do not match';

    if (empty($errors)) {
        $check = mysqli_query($conn, "SELECT id FROM owners WHERE phone='" . mysqli_real_escape_string($conn, $phone) . "'");
        if (mysqli_num_rows($check) > 0) $errors[] = 'This phone number is already registered. Please login instead.';
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = mysqli_prepare($conn, "INSERT INTO owners (full_name, phone, password_hash, business_name) VALUES (?,?,?,?)");
        mysqli_stmt_bind_param($stmt, 'ssss', $full_name, $phone, $hash, $business_name);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['owner_id'] = mysqli_insert_id($conn);
            $_SESSION['owner_name'] = $full_name;
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Something went wrong. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Mess Owner Register — MealConnect</title>
<?php include __DIR__ . '/../includes/head.php'; ?>
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-side" style="background:var(--ink)">
      <span class="mark" style="background:#fff2"><svg viewBox="0 0 24 24" fill="none"><path d="M6 3v8a3 3 0 003 3v7M6 3v8M9 3v8M12 3v6a2 2 0 002 2h1v9m0-13V3" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
      <h2>Grow your mess business</h2>
      <p>List your mess, cloud kitchen or startup and reach thousands of students across Pune.</p>
      <ul>
        <li>Add your mess &amp; weekly menu</li>
        <li>Manage joined students</li>
        <li>Mark daily attendance</li>
        <li>Track payments &amp; feedback</li>
      </ul>
    </div>
    <div class="auth-form">
      <div class="auth-tabs">
        <a href="login.php">Login</a>
        <a href="register.php" class="active">Register</a>
      </div>
      <?php if ($errors): ?>
        <div class="alert alert-error"><?php foreach ($errors as $e) echo h($e) . '<br>'; ?></div>
      <?php endif; ?>
      <form method="post">
        <div class="field"><label>Full Name</label>
          <input type="text" name="full_name" value="<?= h($full_name) ?>" placeholder="Owner name" required></div>
        <div class="field"><label>Business / Mess Name</label>
          <input type="text" name="business_name" value="<?= h($business_name) ?>" placeholder="e.g. Deshmukh Home Mess" required></div>
        <div class="field"><label>Phone Number (Username)</label>
          <input type="text" name="phone" maxlength="10" value="<?= h($phone) ?>" placeholder="10-digit mobile number" required></div>
        <div class="field"><label>Password</label>
          <input type="password" name="password" placeholder="Create a strong password" required></div>
        <div class="field"><label>Confirm Password</label>
          <input type="password" name="confirm_password" placeholder="Re-enter password" required></div>
        <button type="submit" class="btn btn-dark btn-block">Create Owner Account</button>
      </form>
      <p style="text-align:center;font-size:.85rem;color:var(--ink-soft);margin-top:16px">Are you a student? <a href="../student/register.php" style="color:var(--orange);font-weight:600">Register here</a></p>
    </div>
  </div>
</div>
</body>
</html>
