<?php require '../app/views/layouts/header.php'; ?>
<?php require '../app/views/layouts/sidebar.php'; ?>

<div id="main-content" class="main-content">
  <div class="content-wrapper container-fluid">

    <div class="container mt-4">

      <!-- Toolbar -->
      <div class="page-toolbar mb-3 d-flex justify-content-between align-items-center">
        <h4 class="page-title">My Leave History</h4>

        <button class="btn btn-primary"
                data-bs-toggle="offcanvas"
                data-bs-target="#applyLeaveCanvas">
          + Apply Leave
        </button>
      </div>

      <!-- Leave Summary -->
      <div class="alert alert-info mb-3">
        <b>Total:</b> <?= $summary['total'] ?>
        | <b>Used:</b> <?= $summary['used'] ?>
        | <b>Balance:</b> <?= $summary['balance'] ?>
      </div>

      <!-- History Table -->
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Type</th>
            <th>Dates</th>
            <th>Days</th>
            <th>Status</th>
            <th>Remark</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($leaves as $l): ?>
          <tr>
            <td><?= htmlspecialchars($l['leave_type']) ?></td>
            <td><?= $l['start_date'] ?> → <?= $l['end_date'] ?></td>
            <td><?= $l['total_days'] ?></td>
            <td>
              <span class="badge bg-<?=
                $l['status'] === 'approved' ? 'success' :
                ($l['status'] === 'rejected' ? 'danger' : 'warning')
              ?>">
                <?= ucfirst($l['status']) ?>
              </span>
            </td>
            <td><?= $l['admin_remark'] ?: '-' ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

    </div>
  </div>
</div>

<!-- APPLY LEAVE OFFCANVAS -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="applyLeaveCanvas">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Apply Leave</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body">

  <form id="leaveForm">

      <div class="mb-3">
        <label class="form-label">Leave Type</label>
        <select id="leaveType" name="leave_type_id" class="form-control" required>
          <option value="">Select</option>
          <?php foreach ($leaveTypes as $type): ?>
            <option value="<?= $type['id'] ?>"
                    data-balance="<?= $summary['balance'] ?>">
              <?= htmlspecialchars($type['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Start Date</label>
        <input type="date" id="start" name="start_date" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">End Date</label>
        <input type="date" id="end" name="end_date" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Total Days</label>
        <input type="number" id="days" name="total_days" class="form-control" readonly>
      </div>

      <div class="mb-3">
        <label class="form-label">Reason</label>
        <textarea name="reason" class="form-control"></textarea>
      </div>

      <div id="leaveBalanceBox" class="alert alert-info d-none"></div>
      <div id="leaveError" class="alert alert-danger d-none"></div>

      <button type="submit" class="btn btn-success w-100">
        Apply Leave
      </button>

    </form>
  </div>
</div>


<div id="toast"
     class="position-fixed bottom-0 end-0 p-3"
     style="z-index: 9999; display:none;">
  <div class="toast align-items-center text-bg-success border-0 show">
    <div class="d-flex">
      <div class="toast-body">
        ✅ Leave applied successfully
      </div>
      <button type="button"
              class="btn-close btn-close-white me-2 m-auto"
              onclick="hideToast()"></button>
    </div>
  </div>
</div>

<script>
function calcDays() {
  const s = new Date(document.getElementById('start').value);
  const e = new Date(document.getElementById('end').value);

  if (s && e && e >= s) {
    const days = Math.floor((e - s) / (1000 * 60 * 60 * 24)) + 1;
    document.getElementById('days').value = days;
    validateBalance();
  }
}

function validateBalance() {
  const type = document.getElementById('leaveType');
  if (!type.value) return;

  const balance = parseInt(type.selectedOptions[0].dataset.balance);
  const days = parseInt(document.getElementById('days').value || 0);

  const box = document.getElementById('leaveBalanceBox');
  const err = document.getElementById('leaveError');
  const btn = document.querySelector('#leaveForm button');

  box.classList.remove('d-none');
  box.innerHTML = `🟢 Available Balance: <b>${balance}</b> days`;

  if (days > balance) {
    err.classList.remove('d-none');
    err.innerHTML = '❌ Insufficient leave balance';
    btn.disabled = true;
  } else {
    err.classList.add('d-none');
    btn.disabled = false;
  }
}

document.getElementById('start').onchange = calcDays;
document.getElementById('end').onchange = calcDays;
document.getElementById('leaveType').onchange = validateBalance;
</script>

<script>
document.getElementById('leaveForm').addEventListener('submit', function (e) {
  e.preventDefault(); // 🚫 stop page reload

  const form = this;
  const formData = new FormData(form);

  fetch("<?= BASE_URL ?>/leaves/apply", {
    method: "POST",
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      showToast();

      // reset form
      form.reset();
      document.getElementById('days').value = '';

      // close offcanvas after 1s
      setTimeout(() => {
        const canvas = bootstrap.Offcanvas.getInstance(
          document.getElementById('applyLeaveCanvas')
        );
        canvas.hide();
      }, 1000);

      // reload page to refresh history (optional)
      setTimeout(() => location.reload(), 1500);
    }
  })
  .catch(err => {
    alert("Something went wrong");
    console.error(err);
  });
});

function showToast() {
  document.getElementById('toast').style.display = 'block';
}

function hideToast() {
  document.getElementById('toast').style.display = 'none';
}
</script>



<?php require '../app/views/layouts/footer.php'; ?>
