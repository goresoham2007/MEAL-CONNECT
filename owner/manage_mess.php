<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_owner_login();

$messId = (int)($_GET['id'] ?? 0);
$mess = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM mess WHERE id=$messId AND owner_id={$_SESSION['owner_id']}"));
if (!$mess) { header('Location: dashboard.php'); exit; }

// Mark attendance (AJAX-free simple POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_attendance'])) {
    $studentId = (int)$_POST['student_id'];
    $status = $_POST['status'];
    $date = date('Y-m-d');
    if (in_array($status, ['Present','Absent','Holiday'])) {
        $stmt = mysqli_prepare($conn, "INSERT INTO attendance (student_id, mess_id, attendance_date, status) VALUES (?,?,?,?)
            ON DUPLICATE KEY UPDATE status=VALUES(status)");
        mysqli_stmt_bind_param($stmt, 'iiss', $studentId, $messId, $date, $status);
        mysqli_stmt_execute($stmt);
    }
    header("Location: manage_mess.php?id=$messId&tab=attendance");
    exit;
}

$tab = $_GET['tab'] ?? 'students';

$studentsRes = mysqli_query($conn, "SELECT s.id, s.full_name, s.phone, s.college_name, sub.join_date, sub.expiry_date, sub.plan_label
    FROM subscriptions sub JOIN students s ON s.id=sub.student_id
    WHERE sub.mess_id=$messId AND sub.status='Active' AND sub.expiry_date >= CURDATE()
    ORDER BY sub.join_date DESC");
$students = [];
while ($s = mysqli_fetch_assoc($studentsRes)) $students[] = $s;

// Today's attendance map
$today = date('Y-m-d');
$attRes = mysqli_query($conn, "SELECT student_id, status FROM attendance WHERE mess_id=$messId AND attendance_date='$today'");
$attToday = [];
while ($a = mysqli_fetch_assoc($attRes)) $attToday[$a['student_id']] = $a['status'];

$enqRes = mysqli_query($conn, "SELECT e.*, s.full_name, s.phone FROM enquiries e JOIN students s ON s.id=e.student_id WHERE e.mess_id=$messId ORDER BY e.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Manage <?= h($mess['name']) ?> — MealConnect</title>
<?php include __DIR__ . '/../includes/head.php'; ?>
</head>
<body>
<?php include __DIR__ . '/../includes/navbar_owner.php'; ?>

<div class="container" style="padding-bottom:60px">
  <a href="dashboard.php" style="color:var(--orange);font-weight:600;font-size:.85rem;display:inline-block;margin-top:20px">← Back to Dashboard</a>
  <h1><?= h($mess['name']) ?></h1>

  <div class="chip-row">
    <a href="?id=<?= $messId ?>&tab=students" class="chip <?= $tab==='students'?'active':'' ?>">Joined Students (<?= count($students) ?>)</a>
    <a href="?id=<?= $messId ?>&tab=attendance" class="chip <?= $tab==='attendance'?'active':'' ?>">Mark Attendance</a>
    <a href="?id=<?= $messId ?>&tab=enquiries" class="chip <?= $tab==='enquiries'?'active':'' ?>">Enquiries (<?= mysqli_num_rows($enqRes) ?>)</a>
  </div>

  <?php if ($tab === 'students'): ?>
    <div class="panel">
      <?php if (empty($students)): ?>
        <p style="color:var(--ink-soft)">No students have joined this mess yet.</p>
      <?php else: ?>
      <table class="data-table">
        <thead><tr><th>Name</th><th>Phone</th><th>College</th><th>Plan</th><th>Join Date</th><th>Expiry</th></tr></thead>
        <tbody>
        <?php foreach ($students as $s): ?>
          <tr>
            <td><?= h($s['full_name']) ?></td>
            <td class="mono"><?= h($s['phone']) ?></td>
            <td><?= h($s['college_name'] ?: '—') ?></td>
            <td><?= h($s['plan_label']) ?></td>
            <td class="mono"><?= h($s['join_date']) ?></td>
            <td class="mono"><?= h($s['expiry_date']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>

  <?php elseif ($tab === 'attendance'): ?>
    <div class="panel">
      <h3>Mark today's attendance — <span class="mono"><?= date('d M Y') ?></span></h3>
      <?php if (empty($students)): ?>
        <p style="color:var(--ink-soft)">No active students to mark attendance for.</p>
      <?php else: foreach ($students as $s):
        $cur = $attToday[$s['id']] ?? null;
      ?>
        <div class="owner-mess-row">
          <div style="width:44px;height:44px;border-radius:50%;background:var(--cream-2);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--orange)"><?= h(initials($s['full_name'])) ?></div>
          <div style="flex:1"><b><?= h($s['full_name']) ?></b><div class="loc mono"><?= h($s['phone']) ?></div></div>
          <div class="attend-toggle">
            <form method="post"><input type="hidden" name="mark_attendance" value="1"><input type="hidden" name="student_id" value="<?= (int)$s['id'] ?>"><input type="hidden" name="status" value="Present">
              <button type="submit" class="p <?= $cur==='Present'?'sel':'' ?>">Present</button></form>
            <form method="post"><input type="hidden" name="mark_attendance" value="1"><input type="hidden" name="student_id" value="<?= (int)$s['id'] ?>"><input type="hidden" name="status" value="Absent">
              <button type="submit" class="a <?= $cur==='Absent'?'sel':'' ?>">Absent</button></form>
            <form method="post"><input type="hidden" name="mark_attendance" value="1"><input type="hidden" name="student_id" value="<?= (int)$s['id'] ?>"><input type="hidden" name="status" value="Holiday">
              <button type="submit" class="h <?= $cur==='Holiday'?'sel':'' ?>">Holiday</button></form>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>

  <?php elseif ($tab === 'enquiries'): ?>
    <div class="panel">
      <?php if (mysqli_num_rows($enqRes) === 0): ?>
        <p style="color:var(--ink-soft)">No enquiries yet.</p>
      <?php else: while ($e = mysqli_fetch_assoc($enqRes)): ?>
        <div class="review-item">
          <div style="display:flex;justify-content:space-between"><b><?= h($e['full_name']) ?></b><span class="mono" style="font-size:.78rem;color:var(--ink-soft)"><?= h($e['phone']) ?></span></div>
          <p style="color:var(--ink-soft);font-size:.88rem;margin:4px 0 0"><?= h($e['message']) ?></p>
          <div style="font-size:.72rem;color:var(--ink-soft);margin-top:4px"><?= h(date('d M Y, h:i A', strtotime($e['created_at']))) ?></div>
        </div>
      <?php endwhile; endif; ?>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
