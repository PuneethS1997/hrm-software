<?php require '../app/views/layouts/header.php'; ?>
<?php require '../app/views/layouts/sidebar.php'; ?>

<div id="list-main-content" class="main-content">
    <div class="content-wrapper container-fluid">

        <div class="card">
            <div class="card-body text-center">

                <h5>Today's Attendance</h5>

                <?php if (!$data): ?>
                    <button id="checkInBtn" class="btn btn-success">
                        ⏱ Check In
                    </button>

                <?php elseif ($data['login_time'] && !$data['logout_time']): ?>
                    <p>Checked in at <?= date('H:i', strtotime($data['login_time'])) ?></p>
                    <button id="checkOutBtn" class="btn btn-danger">Check Out</button>

                <?php else: ?>
                    <p>Completed</p>
                    <p>Total Hours: <?= $data['total_hours'] ?></p>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
<?php require '../app/views/layouts/footer.php'; ?>