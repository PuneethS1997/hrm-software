<?php require '../app/views/layouts/header.php'; ?>
<?php require '../app/views/layouts/sidebar.php'; ?>

<!-- 🔥 THIS DIV IS THE FIX -->
<div id="main-content" class="main-content">

    <div class="content-wrapper container-fluid">
        <div class="card shadow-sm border-0 p-4">
            <h4 class="mb-4">Add Employee</h4>

            <form method="POST" action="/crm-hrms/public/employees/store" class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Employee ID</label>
                    <input name="employee_code" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input name="name" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input name="phone" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Job Role</label>
                    <input name="job_role" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Department</label>
                    <input name="department" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Joining Date</label>
                    <input type="date" name="joining_date" class="form-control" required>
                </div>


                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="col-12 mt-3">
                    <button class="btn btn-primary px-4">Save Employee</button>
                </div>

            </form>
        </div>
    </div>

</div>
<!-- 🔥 END FIX -->





<?php require '../app/views/layouts/footer.php'; ?>