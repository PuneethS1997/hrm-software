


<?php require '../app/views/layouts/header.php'; ?>
<?php require '../app/views/layouts/sidebar.php'; ?>


<div id="main-content" class="main-content">
  <div class="content-wrapper container-fluid">

    <div class="page-toolbar mb-4">
      <div>
        <h1 class="page-title">Attendance</h1>
        <span class="text-muted">Daily attendance overview</span>
      </div>
    </div>

    <form class="row g-2 mb-3">
      <div class="col-md-3">
        <input type="date" name="date" class="form-control" value="<?= $_GET['date'] ?? date('Y-m-d') ?>">
      </div>

      <div class="col-md-3">
        <select name="user_id" class="form-select">
          <option value="">All Employees</option>

          <?php foreach ((new User())->allEmployees() as $u): ?>
            <option value="<?= $u['id'] ?>"
              <?= ($_GET['user_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($u['name']) ?>
            </option>
          <?php endforeach; ?>

        </select>

      </div>

      <div class="col-md-2">
        <button class="btn btn-primary">Filter</button>
      </div>
    </form>

    <table id="attendanceTable" class="table bitrix-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Status</th>
          <th>Login</th>
          <th>Logout</th>
          <th>Total Hours</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($records as $row): ?>
          <tr>
            <td><?= $row['name'] ?></td>

            <td>
              <?php
              $badge = match ($row['status']) {
                'active' => 'success',
                'logged_out' => 'warning',
                default => 'secondary'
              };
              ?>
              <span class="badge bg-<?= $badge ?>">
                <?= ucfirst($row['status']) ?>
              </span>
            </td>

            <td><?= $row['login_time'] ? date('H:i', strtotime($row['login_time'])) : '-' ?></td>
            <td><?= $row['logout_time'] ? date('H:i', strtotime($row['logout_time'])) : '-' ?></td>
            <td><?= $row['total_hours'] ?? '0.00' ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>


    <!-- live attendance status -->
    <table class="table">
  <thead>
    <tr>
      <th>Employee</th>
      <th>Status</th>
      <th>Login</th>
      <th>Working</th>
    </tr>
  </thead>
  <tbody id="attendanceBody">
    <td>
  <span class="status status-live">🟢 LIVE</span>
</td>
</tbody>
</table>

  </div>
</div>
<?php require '../app/views/layouts/footer.php'; ?>