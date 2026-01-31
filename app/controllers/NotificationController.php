<?php
require_once '../app/middleware/AuthMiddleware.php';
require_once '../app/models/Notification.php';

class NotificationController {

  public function fetch() {
    AuthMiddleware::handle();

    $notifications = (new Notification())
      ->unread($_SESSION['user']['id']);

    echo json_encode($notifications);
  }

  public function read($id) {
    AuthMiddleware::handle();
    (new Notification())->markRead($id);
  }
}
