<?php require '../app/views/layouts/header.php'; ?>
<?php require '../app/views/layouts/sidebar.php'; ?>

<!-- 🔥 THIS DIV IS THE FIX -->
<div id="main-content" class="main-content">

    <div class="content-wrapper container-fluid">

<div class="container mt-4">
  <h4>Pending Leave Requests</h4>

  <table class="table table-striped">
    <thead>
      <tr>
        <th>Employee</th>
        <th>Type</th>
        <th>Dates</th>
        <th>Days</th>
        <th>Reason</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($leaves as $l): ?>
      <tr>
        <td><?= $l['name'] ?></td>
        <td><?= $l['leave_type'] ?></td>
        <td><?= $l['start_date'] ?> → <?= $l['end_date'] ?></td>
        <td><?= $l['total_days'] ?></td>
        <td><?= $l['reason'] ?></td>
        <td>
          <form method="POST" action="<?= BASE_URL ?>/leave/action">
            <input type="hidden" name="id" value="<?= $l['id'] ?>">
            <input type="text" name="remark" class="form-control mb-1" placeholder="Remark">
            <?php if ($leave['requested_days'] > $leave['balance']) : ?>
              <div class="alert alert-danger small">
                ❌ Insufficient leave balance
              </div>
            <?php endif; ?>


            <!-- <button name="status" value="approved" class="btn btn-success btn-sm">
              Approve
            </button> -->
            <button class="btn btn-success"
              <?= $leave['requested_days'] > $leave['balance'] ? 'disabled' : '' ?>>
              Approve
            </button>

            <button name="status" value="rejected" class="btn btn-danger btn-sm">
              Reject
            </button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
      </div>
      </div>

<?php require '../app/views/layouts/footer.php'; ?>
