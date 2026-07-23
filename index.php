<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

if (!empty($_SESSION['student_id'])) { header('Location: student/dashboard.php'); exit; }
if (!empty($_SESSION['owner_id']))   { header('Location: owner/dashboard.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>MealConnect — Find your mess, cloud kitchen or tiffin service across Pune</title>
<?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body>

<header class="navbar">
  <div class="container navbar-inner">
    <a href="/mealconnect/index.php" class="logo">
      <span class="mark"><svg viewBox="0 0 24 24" fill="none"><path d="M6 3v8a3 3 0 003 3v7M6 3v8M9 3v8M12 3v6a2 2 0 002 2h1v9m0-13V3" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
      Meal<span class="grad">Connect</span>
    </a>
    <div style="flex:1"></div>
    <a href="student/login.php" class="btn btn-outline btn-sm">Student Login</a>
    <a href="owner/login.php" class="btn btn-dark btn-sm">Mess Owner Login</a>
  </div>
</header>

<section class="hero">
  <div class="container hero-inner">
    <div>
      <h1>Find home-style mess &amp; tiffin services, anywhere in Pune.</h1>
      <p>MEAL-CONNECT connects students with verified mess, cloud kitchens and small food startups — from Narhe to Katraj, Hinjewadi to Chakan. Browse menus, try a 2-day trial, and subscribe monthly.</p>
      <div style="display:flex;gap:14px;margin-top:26px;flex-wrap:wrap">
        <a href="student/register.php" class="btn btn-primary">Get Started as Student</a>
        <a href="owner/register.php" class="btn btn-outline">List your Mess</a>
      </div>
      <div class="hero-stats">
        <div><b>26+</b><span>Mess &amp; Kitchens Listed</span></div>
        <div><b>20+</b><span>Pune Localities</span></div>
        <div><b>4.2★</b><span>Avg. Student Rating</span></div>
      </div>
    </div>
    <div style="flex-shrink:0">
      <img src="https://commons.wikimedia.org/wiki/Special:FilePath/Yummy_Andhra_Thali.jpg?width=500" style="border-radius:24px;box-shadow:0 20px 50px rgba(43,27,18,.2);width:380px;height:420px;object-fit:cover" alt="Home style thali" onerror="this.onerror=null;this.src='/mealconnect/assets/images/hero-thali.svg'">
    </div>
  </div>
</section>

<section class="container" style="padding:44px 24px">
  <div class="tiffin-divider"><div class="bar"></div><div class="bar"></div><div class="bar"></div></div>
  <h2 style="text-align:center">Why students choose MealConnect</h2>
  <div class="grid" style="grid-template-columns:repeat(3,1fr)">
    <div class="panel" style="text-align:center">
      <div style="font-size:2rem">🍱</div>
      <h3>Verified Listings</h3>
      <p style="color:var(--ink-soft);font-size:.9rem">Real menus, real prices, real ratings from students who've actually eaten there.</p>
    </div>
    <div class="panel" style="text-align:center">
      <div style="font-size:2rem">🗺️</div>
      <h3>Search all over Pune</h3>
      <p style="color:var(--ink-soft);font-size:.9rem">Narhe, Katraj, Hinjewadi, Wakad, Kothrud, Chakan and 15+ more localities — not just one area.</p>
    </div>
    <div class="panel" style="text-align:center">
      <div style="font-size:2rem">🎟️</div>
      <h3>Try before you subscribe</h3>
      <p style="color:var(--ink-soft);font-size:.9rem">2-day and 3-day trial meals let you taste the food before committing to a monthly plan.</p>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
