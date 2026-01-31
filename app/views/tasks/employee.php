<div class="container mt-4">
  <h5 class="mb-3">My Tasks</h5>

  <div class="row g-3">
    <?php foreach ($tasks as $task): ?>
    <div class="col-md-4">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <span class="badge bg-<?=
            $task['priority']=='high'?'danger':
            ($task['priority']=='medium'?'warning':'secondary')
          ?>">
            <?= ucfirst($task['priority']) ?>
          </span>

          <h6 class="mt-2"><?= $task['title'] ?></h6>
          <p class="text-muted small"><?= $task['description'] ?></p>

          <select class="form-select form-select-sm"
            onchange="updateStatus(<?= $task['id'] ?>, this.value)">
            <option value="todo" <?= $task['status']=='todo'?'selected':'' ?>>To Do</option>
            <option value="in_progress" <?= $task['status']=='in_progress'?'selected':'' ?>>In Progress</option>
            <option value="done" <?= $task['status']=='done'?'selected':'' ?>>Done</option>
          </select>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
