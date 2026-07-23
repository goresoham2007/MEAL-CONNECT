<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_owner_login();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $type = $_POST['type'];
    $veg_type = $_POST['veg_type'];
    $location_area = trim($_POST['location_area']);
    $address = trim($_POST['address']);
    $latitude = (float)$_POST['latitude'];
    $longitude = (float)$_POST['longitude'];
    $description = trim($_POST['description']);
    $defaults = [
        'https://commons.wikimedia.org/wiki/Special:FilePath/South_Indian_Thali_from_Hyderabad.JPG?width=600',
        'https://commons.wikimedia.org/wiki/Special:FilePath/Rajasthani_Food.JPG?width=600',
        'https://commons.wikimedia.org/wiki/Special:FilePath/A_Crispy_Dosa.jpg?width=600',
        'https://commons.wikimedia.org/wiki/Special:FilePath/Indian_Thali_(Gujrati).jpg?width=600',
        'https://commons.wikimedia.org/wiki/Special:FilePath/Hyderabad_Veg_Thali.jpg?width=600',
        'https://commons.wikimedia.org/wiki/Special:FilePath/Yummy_Andhra_Thali.jpg?width=600',
    ];
    $cover_image = trim($_POST['cover_image']) ?: $defaults[array_rand($defaults)];
    $price = (int)$_POST['price_per_month'];
    $tags = trim($_POST['tags']);
    $facilities = trim($_POST['facilities']);
    $owner_contact = trim($_POST['owner_contact']);
    $budget = isset($_POST['budget_friendly']) ? 1 : 0;

    if ($name === '' || $address === '' || $price <= 0) $errors[] = 'Please fill all required fields correctly.';

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO mess (owner_id, name, type, veg_type, location_area, address, latitude, longitude, description, cover_image, price_per_month, tags, facilities, owner_name, owner_contact, budget_friendly)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $ownerId = $_SESSION['owner_id'];
        $ownerName = $_SESSION['owner_name'];
        mysqli_stmt_bind_param($stmt, 'isssssddssisssi',
            $ownerId,$name,$type,$veg_type,$location_area,$address,$latitude,$longitude,$description,$cover_image,$price,$tags,$facilities,$ownerName,$owner_contact,$budget);
        mysqli_stmt_execute($stmt);
        $messId = mysqli_insert_id($conn);

        // Auto-generate Monthly / Quarterly / Half-Yearly plans
        mysqli_query($conn, "INSERT INTO plans (mess_id, plan_name, duration_days, price, meals_per_day, menu_type, savings_note) VALUES
            ($messId,'Monthly',30,$price,'2 Meals/day','$veg_type',NULL),
            ($messId,'Quarterly',90,".round($price*3*0.9).",'2 Meals/day','$veg_type','Save 10%'),
            ($messId,'Half-Yearly',180,".round($price*6*0.85).",'2 Meals/day','$veg_type','Save 15% + 1 Week Free')");

        // Auto-generate 2-day / 3-day trial pricing
        $trial2 = round($price/22*2);
        $trial3 = round($price/22*3);
        mysqli_query($conn, "INSERT INTO trial_plans (mess_id, trial_days, price) VALUES ($messId,2,$trial2),($messId,3,$trial3)");

        header('Location: dashboard.php');
        exit;
    }
}

$areaCoords = [
    'Narhe'=>[18.4529,73.8087],'Katraj'=>[18.4576,73.8677],'Chakan'=>[18.7550,73.8580],
    'Hinjewadi'=>[18.5913,73.7389],'Wakad'=>[18.5993,73.7629],'Kothrud'=>[18.5074,73.8077],
    'Hadapsar'=>[18.5089,73.9260],'Viman Nagar'=>[18.5679,73.9143],'Kharadi'=>[18.5515,73.9430],
    'Baner'=>[18.5590,73.7868],'Aundh'=>[18.5590,73.8070],'Pune Camp'=>[18.5122,73.8792],
    'Warje'=>[18.4783,73.8067],'Bibwewadi'=>[18.4720,73.8620],'Dhanori'=>[18.5786,73.8992],
    'Wagholi'=>[18.5786,73.9880],'Magarpatta'=>[18.5150,73.9280],'Karve Nagar'=>[18.4890,73.8130],
    'Sinhagad Road'=>[18.4600,73.8280],'Pashan'=>[18.5320,73.7870],'Deccan'=>[18.5158,73.8412],
    'Shivajinagar'=>[18.5308,73.8474],'Swargate'=>[18.5010,73.8620],'Kondhwa'=>[18.4650,73.8930],
    'Balewadi'=>[18.5730,73.7690],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>List a New Mess — MealConnect</title>
<?php include __DIR__ . '/../includes/head.php'; ?>
</head>
<body>
<?php include __DIR__ . '/../includes/navbar_owner.php'; ?>

<div class="container" style="padding-bottom:60px;max-width:760px">
  <h1 style="margin-top:24px">List a New Mess / Cloud Kitchen / Startup</h1>
  <?php if ($errors): ?><div class="alert alert-error"><?php foreach ($errors as $e) echo h($e).'<br>'; ?></div><?php endif; ?>
  <div class="panel">
    <form method="post">
      <div class="field"><label>Mess / Kitchen Name *</label><input type="text" name="name" required></div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <div class="field"><label>Type *</label>
          <select name="type" required>
            <option>Mess</option><option>Cloud Kitchen</option><option>Startup</option>
          </select>
        </div>
        <div class="field"><label>Veg / Non-Veg *</label>
          <select name="veg_type" required>
            <option value="Pure Veg">Pure Veg</option>
            <option value="Non-Veg Special">Non-Veg Special</option>
            <option value="Both">Both</option>
          </select>
        </div>
      </div>

      <div class="field"><label>Locality (Area in Pune) *</label>
        <select name="location_area" id="areaSelect" required>
          <option value="">Select area</option>
          <?php foreach ($areaCoords as $area => $coord): ?>
            <option value="<?= h($area) ?>" data-lat="<?= $coord[0] ?>" data-lng="<?= $coord[1] ?>"><?= h($area) ?></option>
          <?php endforeach; ?>
        </select>
        <div class="hint">MealConnect lists mess all over Pune — not just one locality.</div>
      </div>

      <div class="field"><label>Full Address *</label><textarea name="address" rows="2" required></textarea></div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <div class="field"><label>Latitude *</label><input type="text" name="latitude" id="latInput" required></div>
        <div class="field"><label>Longitude *</label><input type="text" name="longitude" id="lngInput" required></div>
      </div>

      <div class="field"><label>Description</label><textarea name="description" rows="3" placeholder="Tell students what makes your mess special..."></textarea></div>
      <div class="field"><label>Cover Image URL</label><input type="text" name="cover_image" placeholder="https://... (leave blank for a default food image)"></div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <div class="field"><label>Price per Month (₹) *</label><input type="number" name="price_per_month" min="500" required></div>
        <div class="field"><label style="opacity:0;display:block">.</label>
          <label style="display:flex;align-items:center;gap:8px;font-weight:600;color:var(--ink-soft);font-size:.9rem"><input type="checkbox" name="budget_friendly" style="width:auto"> Mark as Budget Friendly</label>
        </div>
      </div>

      <div class="field"><label>Tags (comma separated)</label><input type="text" name="tags" placeholder="Home-style, 2 Meals, Unlimited Roti"></div>
      <div class="field"><label>Facilities (comma separated)</label><input type="text" name="facilities" placeholder="RO Water, Veg/Non-Veg Separate, AC Dining"></div>
      <div class="field"><label>Owner Contact Number *</label><input type="text" name="owner_contact" maxlength="10" required></div>

      <button type="submit" class="btn btn-primary btn-block">List this Mess</button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script>
document.getElementById('areaSelect').addEventListener('change', function(){
  const opt = this.options[this.selectedIndex];
  if (opt.dataset.lat) {
    document.getElementById('latInput').value = opt.dataset.lat;
    document.getElementById('lngInput').value = opt.dataset.lng;
  }
});
</script>
</body>
</html>
