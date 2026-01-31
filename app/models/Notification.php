<?php

require_once __DIR__ . '/../core/Database.php';

class Notification extends Database {

  public function create($user_id, $title, $message, $link = '#') {
    $stmt = $this->prepare("
      INSERT INTO notifications (user_id, title, message, link)
      VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $title, $message, $link]);
  }

  public function unread($user_id) {
    return $this->query("
      SELECT * FROM notifications 
      WHERE user_id=$user_id AND is_read=0 
      ORDER BY id DESC LIMIT 5
    ")->fetchAll();
  }

  public function markRead($id) {
    return $this->query("
      UPDATE notifications SET is_read=1 WHERE id=$id
    ");
  }
}
