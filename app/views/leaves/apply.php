<?php require '../app/views/layouts/header.php'; ?>
<?php require '../app/views/layouts/sidebar.php'; ?>

<!-- 🔥 THIS DIV IS THE FIX -->
<div id="main-content" class="main-content">

    <div class="content-wrapper container-fluid">

<div class="container mt-4">
  <h4>Apply Leave</h4>

  <form method="POST">
    <div class="row">

      <div class="col-md-4 mb-3">
        <label>Leave Type</label>
        <select name="leave_type_id" class="form-control" required>
  <option value="">Select</option>

  <?php foreach ($leaveTypes as $type): ?>
    <option value="<?= $type['id'] ?>">
      <?= htmlspecialchars($type['name']) ?>
    </option>
  <?php endforeach; ?>

</select>

      </div>

      <div class="col-md-4 mb-3">
        <label>Start Date</label>
        <input type="date" name="start_date" id="start" class="form-control" required>
      </div>

      <div class="col-md-4 mb-3">
        <label>End Date</label>
        <input type="date" name="end_date" id="end" class="form-control" required>
      </div>

      <div class="col-md-4 mb-3">
        <label>Total Days</label>
        <input type="number" name="total_days" id="days" class="form-control" readonly>
      </div>

      <div class="col-md-8 mb-3">
        <label>Reason</label>
        <textarea name="reason" class="form-control"></textarea>
      </div>

    </div>

    <button class="btn btn-primary">Apply Leave</button>
  </form>
</div>
          </div>
          </div>

<script>
function calcDays() {
  let s = new Date(document.getElementById('start').value);
  let e = new Date(document.getElementById('end').value);
  if (s && e && e >= s) {
    document.getElementById('days').value =
      (e - s) / (1000 * 60 * 60 * 24) + 1;
  }
}
document.getElementById('start').onchange = calcDays;
document.getElementById('end').onchange = calcDays;
</script>

<?php require '../app/views/layouts/footer.php'; ?>
