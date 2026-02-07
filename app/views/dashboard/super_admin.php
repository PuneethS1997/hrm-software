<?php require '../app/views/layouts/header.php'; ?>
<?php require '../app/views/layouts/sidebar.php'; ?>


<!-- MAIN CONTENT WRAPPER -->
<div id="main-content" class="main-content">
    <div class="content-wrapper p-4">

        <h2>👑 Super Admin Dashboard</h2>



        <div class="row g-4">

        <div class="col-12 col-sm-6 col-lg-3">
                <div class="kpi-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="kpi-title">Total Employees</div>
                            <div class="kpi-value"><?= $data['employees'] ?></div>
                        </div>
                        <div class="kpi-icon bg-blue">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="kpi-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="kpi-title">Today's Attendance</div>
                            <div class="kpi-value"><?= $data['attendance'] ?></div>
                        </div>
                        <div class="kpi-icon bg-green">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="kpi-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="kpi-title">Pending Leaves</div>
                            <div class="kpi-value"><?= $data['leaves'] ?></div>
                        </div>
                        <div class="kpi-icon bg-orange">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <div class="kpi-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="kpi-title">Monthly Payroll</div>
                            <div class="kpi-value">₹<?= number_format($data['payroll']) ?></div>
                        </div>
                        <div class="kpi-icon bg-purple">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                    </div>
                </div>
            </div>
            <canvas id="attendanceChart"></canvas>

        </div>

    </div>
</div>

<?php require '../app/views/layouts/footer.php'; ?>