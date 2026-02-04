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

        <button class="btn btn-info" data-bs-toggle="offcanvas"
          data-bs-target="#leaveCalendarCanvas">
          📅 Calendar View
          </button>

      </div>

      <div class="offcanvas offcanvas-end" id="leaveCalendarCanvas">
        <div class="offcanvas-header">
        <h5>My Leave Calendar</h5>
        <button class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>

        <div class="offcanvas-body">


        <div id="employeeCalendar"></div>
        </div>
        </div>



      <!-- Leave Summary -->
      <div class="alert alert-info mb-3">
        <b>Total Leaves Summary</b>
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
              <option value="<?= $type['id'] ?>">
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


<div id="toast" class="toast-msg"></div>
<div class="alert alert-info mt-2 d-none" id="unpaidInfo">
  ℹ️ Your paid leave balance is exhausted.
  Only <strong>Unpaid Leave</strong> is available.
</div>
  


<script>
function calcDays() {
  const s = new Date(document.getElementById('start').value);
  const e = new Date(document.getElementById('end').value);

  if (s && e && e >= s) {
    const days = Math.floor((e - s) / (1000*60*60*24)) + 1;
    document.getElementById('days').value = days;
    validateBalance();
  }
}

function validateBalance() {

  const typeEl = document.getElementById('leaveType');
  if (!typeEl.value) return;

  const days = parseInt(document.getElementById('days').value || 0);

  fetch(`<?= BASE_URL ?>/leaves/balance?leave_type_id=${typeEl.value}`)
    .then(res => res.json())
    .then(data => {

      const balance = parseInt(data.balance || 0);

      const box = document.getElementById('leaveBalanceBox');
      const err = document.getElementById('leaveError');
      const btn = document.querySelector('#leaveForm button');

      box.classList.remove('d-none');
      box.innerHTML = `🟢 Available Balance: <b>${balance}</b> days`;

      if (days > balance && typeEl.value != 4) {
        err.classList.remove('d-none');
        err.innerHTML = '❌ Insufficient leave balance';
        btn.disabled = true;
      } else {
        err.classList.add('d-none');
        btn.disabled = false;
      }

    })
    .catch(() => {
      document.getElementById('leaveError').classList.remove('d-none');
      document.getElementById('leaveError').innerHTML =
        '⚠ Unable to check leave balance';
    });
}

document.getElementById('leaveType').addEventListener('change', validateBalance);
document.getElementById('start').addEventListener('change', calcDays);
document.getElementById('end').addEventListener('change', calcDays);
</script>



<script>

function showToasts(message, type = 'success') {
  const toast = document.getElementById('toast');
  if (!toast) return;

  toast.textContent = message;
  toast.className = 'toast-msg show';

  if (type === 'success') toast.classList.add('toast-success');
  if (type === 'error') toast.classList.add('toast-error');
  if (type === 'warning') toast.classList.add('toast-warning');

  setTimeout(() => {
    toast.className = 'toast-msg';
  }, 3000);
}

document.getElementById('leaveForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch('/crm-hrms/public/leaves/apply', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    // console.log(data);
  // showToast('Leave applied successfully ✅', 'success');

    if (data.success) {
      showToasts('Leave applied successfully ✅', 'success');

      // optional: reset form
      this.reset();

      // optional: close offcanvas
      const canvas = bootstrap.Offcanvas.getInstance(
        document.getElementById('applyLeaveCanvas')
      );
      canvas?.hide();

    } else {
      showToasts(data.message || 'Something went wrong ❌', 'error');
    }
  })
  .catch(() => {
    showToasts('Server error ❌', 'error');
  });
});


const leaveTypeSelect = document.getElementById('leaveType');
const unpaidInfo = document.getElementById('unpaidInfo');

leaveTypeSelect.addEventListener('change', function () {
  const leaveTypeId = this.value;
  if (!leaveTypeId) return;

  fetch(`/crm-hrms/public/leaves/balance?leave_type_id=${leaveTypeId}`)
    .then(res => res.json())
    .then(data => {
      // If balance is zero AND not unpaid leave (assuming unpaid id = 4)
      if (data.balance <= 0 && leaveTypeId != 4) {
        // Auto switch to unpaid
        leaveTypeSelect.value = 4;

        unpaidInfo.classList.remove('d-none');
        showToast(
          'Paid leave exhausted. Switching to Unpaid Leave.',
          'warning'
        );
      } else {
        unpaidInfo.classList.add('d-none');
      }
    });
});

</script>

<script>
document.getElementById('leaveCalendarCanvas')
.addEventListener('shown.bs.offcanvas', function () {

if(window.empCalendarLoaded) return;

Promise.all([
fetch("<?= BASE_URL ?>/leave/enterpriseCalendar").then(r=>r.json()),
fetch("<?= BASE_URL ?>/holiday/calendar").then(r=>r.json())
])
.then(([leaves,holidays])=>{

new FullCalendar.Calendar(calendarEl,{
events:[...leaves,...holidays]
}).render();

});


const calendar = new FullCalendar.Calendar(
document.getElementById('employeeCalendar'),
{
initialView:'dayGridMonth',
height:'auto',

events:'<?= BASE_URL ?>/leave/enterpriseCalendar',

eventClick:function(info){
showToast(
info.event.title + ' ('+
info.event.extendedProps.status+')'
);
}
});

calendar.render();
window.empCalendarLoaded=true;
});
</script>




<?php require '../app/views/layouts/footer.php'; ?>
