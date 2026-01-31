<?php
require_once '../app/middleware/AuthMiddleware.php';
require_once '../app/middleware/RoleMiddleware.php';
require_once '../app/models/Task.php';

class TaskController {

  public function index() {
    AuthMiddleware::handle();
    $task = new Task();

    if ($_SESSION['user']['role'] === 'employee') {
      $tasks = $task->employeeTasks($_SESSION['user']['id']);
      require '../app/views/tasks/employee.php';
    } else {
      $tasks = $task->allTasks();
      require '../app/views/tasks/admin.php';
    }
  }

  public function create() {
    AuthMiddleware::handle();
    RoleMiddleware::allow(['super_admin','admin']);

    if ($_POST) {
      (new Task())->create($_POST, $_SESSION['user']['id']);
      header('Location: /tasks');
    }

    require '../app/views/tasks/create.php';
  }

  public function updateStatus() {
    AuthMiddleware::handle();
    (new Task())->updateStatus($_POST['id'], $_POST['status']);
  }
}
