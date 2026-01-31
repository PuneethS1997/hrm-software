<?php require '../app/views/layouts/header.php'; ?>
<?php require '../app/views/layouts/sidebar.php'; ?>

<div id="main-content" class="main-content">
    <div class="content-wrapper container-fluid">
        <h4>🗑️ Trash Employees</h4>

        <button class="btn btn-success mb-2" id="bulkRestoreBtn">
            Restore Selected
        </button>

        <button id="bulkDeleteBtn" class="btn btn-danger btn-sm deleteEmployeeBtn">
            🗑 Delete Selected
        </button>

        <table id="employeeTable" class="table bitrix-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllTrash"></th>
                    <th>Employee Code</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Deleted At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $emp): ?>
                    <tr id="row-<?= $emp['id'] ?>">
                        <td>
                            <input type="checkbox" class="trashChk" value="<?= $emp['id']; ?>">
                        </td>
                        <td><?= $emp['employee_code'] ?></td>
                        <td><?= $emp['name'] ?></td>
                        <td><?= $emp['email'] ?></td>
                        <td><?= $emp['deleted_at'] ?></td>
                        <td>
                            <button class="btn btn-sm btn-success restoreBtn"
                                data-id="<?= $emp['id'] ?>">
                                Restore
                            </button>
                            <button
                                class="icon-btn delete delete-btn"
                                data-id="<?= $emp['id']; ?>">
                                🗑
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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
</div>

<?php require '../app/views/layouts/footer.php'; ?>