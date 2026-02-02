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
  
  public function updateStatus($id, $status, $remark, $adminId)
  {
      $this->db->beginTransaction();
  
      try {
          // 1. Update leave status
          $stmt = $this->db->prepare("
              UPDATE leaves SET
                status = ?,
                admin_remark = ?,
                approved_by = ?,
                approved_at = NOW()
              WHERE id = ?
          ");
          $stmt->execute([$status, $remark, $adminId, $id]);
  
          // 2. Deduct balance ONLY if approved
          if ($status === 'approved') {
              $this->deductLeaveBalance($id);
          }
  
          $this->db->commit();
          return true;
  
      } catch (Exception $e) {
          $this->db->rollBack();
          throw $e;
      }
  }
  private function deductLeaveBalance($leaveId)
  {
      // Get leave info
      $stmt = $this->db->prepare("
          SELECT user_id, leave_type_id, total_days
          FROM leaves
          WHERE id = ?
      ");
      $stmt->execute([$leaveId]);
      $leave = $stmt->fetch(PDO::FETCH_ASSOC);
  
      if (!$leave) {
          throw new Exception('Leave not found');
      }
  
      // Deduct balance
      $stmt = $this->db->prepare("
          UPDATE leave_balances
          SET balance = balance - ?
          WHERE user_id = ?
            AND leave_type_id = ?
            AND balance >= ?
      ");
      $stmt->execute([
          $leave['total_days'],
          $leave['user_id'],
          $leave['leave_type_id'],
          $leave['total_days']
      ]);
  
      // Safety check (no negative balance)
      if ($stmt->rowCount() === 0) {
          throw new Exception('Insufficient leave balance');
      }
  }
  public function hasSufficientBalance($leaveId) {
    $stmt = $this->db->prepare("
      SELECT l.total_days, b.balance
      FROM leaves l
      JOIN leave_balances b 
        ON b.user_id = l.user_id 
       AND b.leave_type_id = l.leave_type_id
      WHERE l.id = ?
    ");
    $stmt->execute([$leaveId]);
    $row = $stmt->fetch();
  
    return $row && $row['balance'] >= $row['total_days'];
  }
  
  public function rollbackBalance($leaveId) {
    $stmt = $this->db->prepare("
      UPDATE leave_balances b
      JOIN leaves l ON l.leave_type_id = b.leave_type_id
      SET b.balance = b.balance + l.total_days
      WHERE l.id = ? AND b.user_id = l.user_id
    ");
    $stmt->execute([$leaveId]);
  }
  
  public function getLeaveSummary($userId) {
    $stmt = $this->db->prepare("
      SELECT
        SUM(CASE WHEN status='approved' THEN total_days ELSE 0 END) as used,
        SUM(balance) as balance
      FROM leaves l
      JOIN leave_balances b ON b.user_id = l.user_id
      WHERE l.user_id = ?
    ");
    $stmt->execute([$userId]);
    return $stmt->fetch();
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
