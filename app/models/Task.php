<?php
class Task {
  private $db;

  public function __construct() {
    $this->db = new PDO(
      "mysql:host=".DB_HOST.";dbname=".DB_NAME,
      DB_USER,
      DB_PASS
    );
  }

  public function create($data, $adminId) {
    $stmt = $this->db->prepare(
      "INSERT INTO tasks
      (title, description, assigned_to, assigned_by, priority, due_date)
      VALUES (?, ?, ?, ?, ?, ?)"
    );

    return $stmt->execute([
      $data['title'],
      $data['description'],
      $data['assigned_to'],
      $adminId,
      $data['priority'],
      $data['due_date']
    ]);
  }

  public function employeeTasks($userId) {
    $stmt = $this->db->prepare(
      "SELECT * FROM tasks WHERE assigned_to=? ORDER BY due_date ASC"
    );
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function allTasks() {
    return $this->db->query(
      "SELECT t.*, u.name AS employee
       FROM tasks t
       JOIN users u ON u.id = t.assigned_to
       ORDER BY created_at DESC"
    )->fetchAll(PDO::FETCH_ASSOC);
  }

  public function updateStatus($id, $status) {
    $stmt = $this->db->prepare(
      "UPDATE tasks SET status=? WHERE id=?"
    );
    return $stmt->execute([$status, $id]);
  }
}
