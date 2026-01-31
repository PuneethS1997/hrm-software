<?php
require_once __DIR__ . '/../core/Database.php';

class AuthMiddleware {

  public static function handle() {
    if (!isset($_SESSION['user'])) {
      header('Location: /login');
      exit;
    }

    // OPTIONAL: revalidate user from DB
    $db = new Database();

    // ✅ USE $db->db->query()
    $stmt = $db->db->query("
      SELECT id FROM users WHERE id = ".$_SESSION['user']['id']."
    ");
    $user = $stmt->fetch();

    if (!$user) {
      session_destroy();
      header('Location: /login');
      exit;
    }
  }
}
