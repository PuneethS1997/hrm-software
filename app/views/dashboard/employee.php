<?php require '../app/views/layouts/header.php'; ?>
<?php require '../app/views/layouts/sidebar.php'; ?>

<!-- MAIN CONTENT WRAPPER -->
<div id="main-content" class="main-content">
    <div class="content-wrapper p-4">
        <h2>👨‍💻 Employee Dashboard</h2>
        <div class="row g-4">
          <div class="col-lg-8">

            <div class="row">

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="kpi-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="kpi-title">My Attendance</div>
                            <div class="kpi-value"><?= $data['attendance'] ?></div>
                        </div>
                        <div class="kpi-icon bg-green">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="kpi-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="kpi-title">My Leaves</div>
                            <div class="kpi-value"> Used: <strong><?= $summary['used'] ?? 0 ?></strong> |
                    Balance: <strong><?= $summary['balance'] ?? 0 ?></strong></div>
                        </div>
                        <div class="kpi-icon bg-orange">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                    </div>
                </div>
            </div>


       


            <div class="col-12 col-sm-6 col-lg-3">
                <div class="kpi-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="kpi-title">My Salary</div>
                            <div class="kpi-value">₹<?= number_format($data['payroll']) ?></div>
                        </div>
                        <div class="kpi-icon bg-purple">
                            <i class="bi bi-wallet2"></i>
                        </div>
                    </div>
                </div>
            </div>
            </div>

           </div>
           
        </div>
    </div>
</div>

<?php require '../app/views/layouts/footer.php'; ?>