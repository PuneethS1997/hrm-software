<?php require '../app/views/layouts/header.php'; ?>
<?php require '../app/views/layouts/sidebar.php'; ?>

<div id="list-main-content" class="main-content">
  <div class="content-wrapper container-fluid">

    <!-- Toolbar -->
    <div class="page-toolbar">
      <div class="toolbar-left">
        <h1 class="page-title">Employees</h1>
        <span class="page-subtitle">Manage company employees</span>
      </div>

      <div class="toolbar-right">
        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
          <i class="bi bi-upload"></i> Import
        </button>
        <a href="/crm-hrms/public/employees/create"><button class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add employee
          </button>
        </a>
      </div>
    </div>


    <!-- <button id="bulkDeleteBtn" class="btn btn-danger btn-sm deleteEmployeeBtn">
  🗑 Delete Selected
</button> -->
    <button class="btn btn-danger" id="bulkDeleteBtn"><i class="bi bi-trash"></i>Select to Move to Trash </button>


    <!-- Content Card -->
    <div class="card">
      <div class="card-body">
        <!-- Skeleton Loader -->
        <!-- <div id="tableSkeleton">
          <div class="skeleton-row"></div>
          <div class="skeleton-row"></div>
          <div class="skeleton-row"></div>
          <div class="skeleton-row"></div>
          <div class="skeleton-row"></div>
        </div> -->
        <table id="employeeTable" class="table bitrix-table">
          <thead class="table-light">
            <tr>
              <th>
                <input type="checkbox" id="selectAll">
              </th>
              <th>Avatar</th>
              <th>Employee Code</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Role</th>
              <th>Department</th>
              <th>Status</th>
              <th>Enable/Disable</th>
              <th class="text-end">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($employees as $employee): ?>

              <tr id="employeeRow<?= $employee['id'] ?>">
                <!-- <td>
  <input type="checkbox" class="row-check" value="<?= $employee['id']; ?>">
</td> -->
                <td><input type="checkbox" class="rowCheck" value="<?= $employee['id']; ?>"></td>


                <div class="employee-cell">
                  <div>
                    <td>
                      <div class="avatar ">A</div>
                    </td>
                    <td>
                      <div class="emp_code"><?= $employee['employee_code'] ?></div>
                    </td>

                    <td>
                      <div class="emp_name"><?= $employee['name'] ?></div>
                    </td>
                    <td>
                      <div class="emp_email"><?= $employee['email'] ?></div>
                    </td>
                    <td>
                      <div class="emp_phone"><?= $employee['phone'] ?></div>
                    </td>
                    <td>
                      <div class="emp_role"><?= $employee['job_role'] ?></div>
                    </td>
                    <td>
                      <div class="emp_department"><?= $employee['department'] ?></div>
                    </td>


                  </div>
                </div>

                <td>
                  <?php if ($employee['status'] == 1): ?>
                    <span class="status-badge active">Active</span>
                  <?php else: ?>
                    <span class="status-badge inactive">Inactive</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="/crm-hrms/public/employees/toggle/<?= $employee['id'] ?>"
                    class="btn btn-sm <?= $employee['status'] ? 'btn-danger' : 'btn-success' ?>"
                    onclick="return confirm('Are you sure?')">
                    <?= $employee['status'] ? 'Deactivate' : 'Activate' ?>
                  </a>
                </td>

                <td class="text-end">
                  <button class="editEmployeeBtn icon-btn edit" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight" data-id="<?= $employee['id']; ?>"><i class="bi bi-pencil"></i></button>
                  <button class="deleteBtn icon-btn delete" data-id="<?= $employee['id']; ?>"><i class="bi bi-trash"></i></button>
                </td>
                <!-- <td><button
                  class="btn btn-sm btn-warning editEmployeeBtn"
                  data-id="<?= $employee['id'] ?>">
                  Edit
                </button>
                 <button
  class="btn btn-danger btn-sm delete-btn deleteEmployeeBtn"
  data-id="<?= $employee['id']; ?>">
  🗑
</button> -->
                <!-- <button class="deleteBtn" data-id="<?= $employee['id']; ?>">Delete</button> -->


                </td>

              </tr>
            <?php endforeach; ?>

          </tbody>
        </table>
      </div>
    </div>
    <!-- Toast Container -->

    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1100">
      <div id="toastContainer"></div>
    </div>

    <!-- emplyoee edit form -->

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel" style="
    border-radius: 20px;
">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasRightLabel">Edit Employee</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <form id="editEmployeeForm">
          <input type="hidden" name="id" id="edit_id">




          <div class="row g-3">
            <div class="col-md-6">
              <label>Employee Code</label>
              <input name="employee_code" id="edit_employee_code" class="form-control">
            </div>

            <div class="col-md-6">
              <label>Name</label>
              <input name="name" id="edit_name" class="form-control">
            </div>

            <div class="col-md-6">
              <label>Email</label>
              <input name="email" id="edit_email" class="form-control">
            </div>

            <div class="col-md-6">
              <label>Phone</label>
              <input name="phone" id="edit_phone" class="form-control">
            </div>

            <div class="col-md-6">
              <label>Job Role</label>
              <input name="job_role" id="edit_job_role" class="form-control">
            </div>

            <div class="col-md-6">
              <label>Department</label>
              <input name="department" id="edit_department" class="form-control">
            </div>
            <div class="col-md-6">
              <label>Joining Date</label>
              <input type="date" name="joining_date" id="edit_joining_date" class="form-control">
            </div>

          </div>



          <div class="modal-footer">
            <button type="button" id="updateEmployeeBtn" class="btn btn-primary">
              Update
            </button>
          </div>
        </form>
      </div>
    </div>

  

    <div class="modal fade" id="bulkUploadModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div id="csvDropZone" class="csv-dropzone">
            <i class="bi bi-cloud-upload fs-1"></i>
            <p class="mb-1">Drag & drop CSV file here</p>
            <small class="text-muted">or click to browse</small>

            <input type="file" id="csvFileInput" accept=".csv" hidden>
          </div>

          <div id="selectedFile" class="mt-2 d-none">
            <i class="bi bi-file-earmark-text"></i>
            <span id="fileName"></span>
          </div>

          <div class="progress mt-3 d-none" id="uploadProgressWrap" style="height:8px;">
            <div id="uploadProgressBar" class="progress-bar bg-primary" style="width:0%"></div>
          </div>

          <div class="small text-muted mt-1 d-none" id="uploadProgressText">
            Uploading… 0%
          </div>

          <button id="startUploadBtn" class="btn btn-primary mt-3 d-none">
            Upload CSV
          </button>

        </div>
      </div>
    </div>



    <!-- delete option -->
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">Confirm Delete</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <p id="deleteMessage">
              Are you sure you want to delete this employee?
            </p>
          </div>

          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-danger" id="confirmDeleteBtn">
              Yes, Delete
            </button>
          </div>

        </div>
      </div>
    </div>


    <!--  -->
  </div>
</div>



<?php require '../app/views/layouts/footer.php'; ?>