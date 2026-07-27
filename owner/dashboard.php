<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_owner_login();

$ownerId = $_SESSION['owner_id'];
$messRes = mysqli_query($conn, "SELECT * FROM mess WHERE owner_id=$ownerId ORDER BY created_at DESC");
$messList = [];
while ($m = mysqli_fetch_assoc($messRes)) $messList[] = $m;
$messIds = array_column($messList, 'id');
$messIdsCsv = $messIds ? implode(',', $messIds) : '0';

$activeStudents = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT student_id) c FROM subscriptions WHERE mess_id IN ($messIdsCsv) AND status='Active' AND expiry_date >= CURDATE()"))['c'];
$totalRevenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount),0) s FROM subscriptions WHERE mess_id IN ($messIdsCsv)"))['s'];
$avgRating = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(AVG(rating),0) a FROM mess WHERE owner_id=$ownerId"))['a'];
$enquiryCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM enquiries WHERE mess_id IN ($messIdsCsv)"))['c'];

// Per-mess enquiry counts, so each listing below can link straight to its enquiries.
$enquiryCounts = [];
$ecRes = mysqli_query($conn, "SELECT mess_id, COUNT(*) c FROM enquiries WHERE mess_id IN ($messIdsCsv) GROUP BY mess_id");
while ($ec = mysqli_fetch_assoc($ecRes)) $enquiryCounts[$ec['mess_id']] = (int)$ec['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Owner Dashboard — MealConnect</title>
<?php include __DIR__ . '/../includes/head.php'; ?>
</head>
<body>
<?php include __DIR__ . '/../includes/navbar_owner.php'; ?>

<div class="container" style="padding-bottom:60px">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-top:24px;flex-wrap:wrap;gap:10px">
    <h1 style="margin:0">Owner Dashboard</h1>
    <a href="add_mess.php" class="btn btn-primary">+ List a New Mess</a>
  </div>

  <div class="kpi-row">
    <div class="kpi"><b><?= count($messList) ?></b><span>Mess Listed</span></div>
    <div class="kpi"><b><?= (int)$activeStudents ?></b><span>Active Students</span></div>
    <div class="kpi"><b>₹<?= number_format($totalRevenue) ?></b><span>Total Revenue Collected</span></div>
    <div class="kpi"><b><?= (int)$enquiryCount ?></b><span>Enquiries Received</span></div>
  </div>

  <h2 style="font-size:1.2rem">Your Mess Listings</h2>
  <?php if (empty($messList)): ?>
    <div class="panel">
      <p style="color:var(--ink-soft)">You haven't listed any mess yet.</p>
      <a href="add_mess.php" class="btn btn-primary">+ List your first Mess</a>
    </div>
  <?php else: foreach ($messList as $m): ?>
    <div class="owner-mess-row">
      <img src="<?= h($m['cover_image']) ?>" alt="<?= h($m['name']) ?>" onerror="this.onerror=null;this.src='/mealconnect/assets/images/food-thali-1.svg'">
      <div style="flex:1;min-width:200px">
        <b><?= h($m['name']) ?></b>
        <div class="loc">📍 <?= h($m['location_area']) ?>, Pune · <?= h($m['type']) ?> · ★ <?= h($m['rating']) ?></div>
        <div class="tag-row"><span class="tag">₹<?= number_format($m['price_per_month']) ?>/month</span><span class="tag"><?= h($m['veg_type']) ?></span></div>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap">
        <a href="manage_mess.php?id=<?= (int)$m['id'] ?>" class="btn btn-sm btn-outline">Manage Students</a>
        <a href="manage_mess.php?id=<?= (int)$m['id'] ?>&tab=enquiries" class="btn btn-sm btn-outline">Enquiries<?= !empty($enquiryCounts[$m['id']]) ? ' (' . $enquiryCounts[$m['id']] . ')' : '' ?></a>
        <a href="../student/mess_detail.php?id=<?= (int)$m['id'] ?>" class="btn btn-sm btn-dark">Preview</a>
      </div>
    </div>
  <?php endforeach; endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
