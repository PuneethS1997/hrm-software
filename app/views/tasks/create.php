<div class="container mt-4">
  <div class="card shadow-sm border-0">
    <div class="card-header bg-white fw-semibold">
      Create Task
    </div>

    <div class="card-body">
      <form method="post" class="row g-3">

        <div class="col-md-6">
          <label class="form-label">Title</label>
          <input name="title" class="form-control" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Assign To</label>
          <select name="assigned_to" class="form-select" required>
            <!-- populate employees -->
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Priority</label>
          <select name="priority" class="form-select">
            <option value="low">Low</option>
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Due Date</label>
          <input type="date" name="due_date" class="form-control">
        </div>

        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="3"></textarea>
        </div>

        <div class="col-12 text-end">
          <button class="btn btn-primary px-4">
            Create Task
          </button>
        </div>

      </form>
    </div>
  </div>
</div>
