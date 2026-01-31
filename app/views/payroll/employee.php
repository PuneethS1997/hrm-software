<div class="container mt-4">
  <div class="card shadow-sm border-0">
    <div class="card-header fw-semibold bg-white">
      Salary Slip – <?= date('F Y', strtotime($data['month'].'-01')) ?>
    </div>
    <div class="card-body">
      <p><strong>Name:</strong> <?= $_SESSION['user']['name'] ?></p>
      <p><strong>Basic Salary:</strong> <?= $data['basic_salary'] ?></p>
      <p><strong>Allowances:</strong> <?= $data['allowances'] ?></p>
      <p><strong>Deductions:</strong> <?= $data['deductions'] ?></p>
      <p><strong>Total Salary:</strong> <?= $data['total_salary'] ?></p>

      <a href="/payroll/pdf?user_id=<?= $_SESSION['user']['id'] ?>&month=<?= $data['month'] ?>" class="btn btn-primary mt-3">
        Download PDF
      </a>
    </div>
  </div>
</div>
