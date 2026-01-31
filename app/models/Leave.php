<?php
require_once __DIR__ . '/../core/Database.php';


class Leave extends Database {

  public function apply($data, $userId) {
    $stmt = $this->db->prepare("
      INSERT INTO leaves
      (user_id, leave_type_id, start_date, end_date, total_days, reason)
      VALUES (?, ?, ?, ?, ?, ?)
    ");

    return $stmt->execute([
      $userId,
      $data['leave_type_id'],
      $data['start_date'],
      $data['end_date'],
      $data['total_days'],
      $data['reason']
    ]);
  }

  public function getLeaveTypes() {
    $stmt = $this->db->query("SELECT * FROM leave_types");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  

  public function myLeaves($userId) {
    $stmt = $this->db->prepare("
      SELECT l.*, lt.name AS leave_type
      FROM leaves l
      JOIN leave_types lt ON lt.id = l.leave_type_id
      WHERE l.user_id = ?
      ORDER BY l.applied_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function pending() {
    $stmt = $this->db->query("
      SELECT l.*, u.name, lt.name AS leave_type
      FROM leaves l
      JOIN users u ON u.id = l.user_id
      JOIN leave_types lt ON lt.id = l.leave_type_id
      WHERE l.status = 'pending'
      ORDER BY l.applied_at ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // public function updateStatus($id, $status, $adminId, $remark) {
  //   $stmt = $this->db->prepare("
  //     UPDATE leaves
  //     SET status=?, admin_remark=?, approved_by=?, approved_at=NOW()
  //     WHERE id=?
  //   ");
  //   return $stmt->execute([$status, $remark, $adminId, $id]);
  // }

  public function getBalance($userId, $leaveTypeId) {
    // max allowed days
    $stmt = $this->db->prepare(
      "SELECT max_days FROM leave_types WHERE id=?"
    );
    $stmt->execute([$leaveTypeId]);
    $max = $stmt->fetchColumn();
  
    // already approved leaves
    $stmt2 = $this->db->prepare(
      "SELECT COALESCE(SUM(total_days),0)
       FROM leaves
       WHERE user_id=? AND leave_type_id=? AND status='approved'"
    );
    $stmt2->execute([$userId, $leaveTypeId]);
    $used = $stmt2->fetchColumn();
  
    return [
      'max' => (int)$max,
      'used' => (int)$used,
      'balance' => max(0, $max - $used)
    ];
  }
  
  public function pendingLeaves() {
    return $this->db->query("
      SELECT l.*, u.name, lt.name AS leave_type
      FROM leaves l
      JOIN users u ON u.id=l.user_id
      JOIN leave_types lt ON lt.id=l.leave_type_id
      WHERE l.status='pending'
      ORDER BY l.applied_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
  }
  
  public function updateStatus($id, $status, $remark, $adminId) {
    $stmt = $this->db->prepare("
      UPDATE leaves SET
        status=?,
        admin_remark=?,
        approved_by=?,
        approved_at=NOW()
      WHERE id=?
    ");
    return $stmt->execute([$status, $remark, $adminId, $id]);
  }

  public function calendarData($userId) {
    $stmt = $this->db->prepare("
      SELECT start_date AS start,
             DATE_ADD(end_date, INTERVAL 1 DAY) AS end,
             status AS title
      FROM leaves
      WHERE user_id=? AND status='approved'
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  
  
}
