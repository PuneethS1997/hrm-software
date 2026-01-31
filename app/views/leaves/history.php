<?php require '../app/views/layouts/header.php'; ?>
<?php require '../app/views/layouts/sidebar.php'; ?>

<!-- 🔥 THIS DIV IS THE FIX -->
<div id="main-content" class="main-content">

    <div class="content-wrapper container-fluid">

<div class="container mt-4">

   <!-- Toolbar -->
   <div class="page-toolbar">
      <div class="toolbar-left">
        <h1 class="page-title">My Leave History</h1>
        <!-- <span class="page-subtitle">Manage company employees</span> -->
      </div>

      <div class="toolbar-right">
       
      <button  class="btn btn-primary"  data-bs-toggle="offcanvas"
        data-bs-target="#applyLeaveCanvas" aria-controls="offcanvasRight">
  + Apply Leave
</button>

        </a>
      </div>
    </div>

  <table id="employeeTable" class="table bitrix-table">
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
        <td><?= $l['leave_type'] ?></td>
        <td><?= $l['start_date'] ?> → <?= $l['end_date'] ?></td>
        <td><?= $l['total_days'] ?></td>
        <td>
          <span class="badge bg-<?= 
            $l['status']=='approved'?'success':
            ($l['status']=='rejected'?'danger':'warning')
          ?>">
            <?= ucfirst($l['status']) ?>
          </span>
        </td>
        <td><?= $l['admin_remark'] ?? '-' ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

new FullCalendar.Calendar(calendarEl, {
  events: '/leave/calendar'
}).render();

<div id="toast" class="toast-msg"></div>




      </div>
      </div>

      <div class="offcanvas offcanvas-end" tabindex="-1" id="applyLeaveCanvas">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Apply Leave</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body">
    

    <form id="leaveForm">
      <div class="form-group">
        <label>Leave Type</label>
        <select id="leaveType" name="leave_type_id" class="form-control" required>
          <option value="">Select</option>
          <?php foreach ($leaveTypes as $type): ?>
            <option value="<?= $type['id'] ?>" data-balance="<?= $leaveBalance[$type['id']] ?? 0 ?>">
              <?= htmlspecialchars($type['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label>Start Date</label>
        <input type="date" name="start_date" id="start" class="form-control" required>
      </div>

      <div class="form-group">
        <label>End Date</label>
        <input type="date" name="end_date" id="end" class="form-control" required>
      </div>

      <div class="col-md-4 mb-3">
        <label>Total Days</label>
        <input type="number" name="total_days" id="days" class="form-control" readonly>
      </div>

      <div class="form-group">
        <label>Reason</label>
        <textarea name="reason" class="form-control"></textarea>
      </div>

      <button class="btn btn-success">Apply</button>
    </form>

    <div id="leaveWarning" class="alert alert-warning d-none"></div>


    <div class="alert alert-info py-2 d-none" id="leaveBalanceBox"></div>
<div class="alert alert-danger py-2 d-none" id="leaveError"></div>

<input type="hidden" name="total_days" id="totalDays">

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
