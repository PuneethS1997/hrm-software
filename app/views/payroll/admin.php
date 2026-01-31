<div class="container mt-4">
  <h5 class="mb-3">Payrolls – <?= date('F Y', strtotime($month.'-01')) ?></h5>

  <a href="/payroll/generate" class="btn btn-success mb-3">Generate Payroll</a>

  <div class="card shadow-sm border-0">
    <div class="card-body table-responsive">
      <table class="table table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>Employee</th>
            <th>Basic Salary</th>
            <th>Allowances</th>
            <th>Deductions</th>
            <th>Total Salary</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($data as $row): ?>
          <tr>
            <td><?= $row['name'] ?></td>
            <td><?= $row['basic_salary'] ?></td>
            <td><?= $row['allowances'] ?></td>
            <td><?= $row['deductions'] ?></td>
            <td><?= $row['total_salary'] ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
