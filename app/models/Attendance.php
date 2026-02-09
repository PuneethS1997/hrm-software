<?php
class Attendance {
  private $db;

  public function __construct() {
    $this->db = new PDO(
      "mysql:host=".DB_HOST.";dbname=".DB_NAME,
      DB_USER,
      DB_PASS
    );
  }

   public function today($userId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM attendance 
             WHERE user_id = :uid 
             AND attendance_date = CURDATE()"
        );
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkIn($userId)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO attendance (user_id, attendance_date, login_time)
             VALUES (:uid, CURDATE(), NOW())
             ON DUPLICATE KEY UPDATE login_time = login_time"
        );
        return $stmt->execute(['uid' => $userId]);
    }

    public function checkOut($userId)
    {
        $stmt = $this->db->prepare(
            "UPDATE attendance
             SET logout_time = NOW(),
                 total_hours = ROUND(
                    TIMESTAMPDIFF(MINUTE, login_time, NOW()) / 60, 2
                 )
             WHERE user_id = :uid
             AND attendance_date = CURDATE()"
        );
        return $stmt->execute(['uid' => $userId]);
    }

    public function getAdminAttendance($date, $userId = null)
{
    $sql = "
    SELECT 
        u.id AS user_id,
        u.name,
        a.attendance_date,
        a.login_time,
        a.logout_time,
        a.total_hours,
        IFNULL(a.status, 'logged_out') AS status
    FROM users u
    LEFT JOIN attendance a 
        ON u.id = a.user_id 
        AND a.attendance_date = :date
    WHERE u.deleted_at IS NULL
    ";

    if ($userId) {
        $sql .= " AND u.id = :uid";
    }

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':date', $date);

    if ($userId) {
        $stmt->bindValue(':uid', $userId);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getToday($userId) {
    $stmt = $this->db->prepare(
      "SELECT * FROM attendance
       WHERE user_id=? AND attendance_date=CURRENT_DATE"
    );
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  

  public function getLiveWorkSeconds($attendance) {
    if (!$attendance || !$attendance['login_time']) return 0;
  
    $endTime = $attendance['logout_time'] ?? date('Y-m-d H:i:s');
  
    $workedSeconds = strtotime($endTime) - strtotime($attendance['login_time']);
  
    $breakSeconds = ($attendance['break_minutes'] ?? 0) * 60;
  
    return max(0, $workedSeconds - $breakSeconds);
  }

  // LOGIN
  public function clockIn($userId) {
    return $this->db->prepare("
      INSERT INTO attendance (user_id, login_time, status, date)
      VALUES (?, NOW(), 'active', CURDATE())
    ")->execute([$userId]);
  }

  // START BREAK
  public function startBreak($attendanceId) {
    $this->db->prepare("
      INSERT INTO attendance_breaks (attendance_id, break_start)
      VALUES (?, NOW())
    ")->execute([$attendanceId]);

    return $this->db->prepare("
      UPDATE attendance SET status='break' WHERE id=?
    ")->execute([$attendanceId]);
  }

  // END BREAK
  public function endBreak($attendanceId) {
    $this->db->prepare("
      UPDATE attendance_breaks
      SET break_end = NOW()
      WHERE attendance_id=? AND break_end IS NULL
      ORDER BY id DESC LIMIT 1
    ")->execute([$attendanceId]);

    return $this->db->prepare("
      UPDATE attendance SET status='active' WHERE id=?
    ")->execute([$attendanceId]);
  }

  // LOGOUT
  public function clockOut($attendanceId) {
    $stmt = $this->db->prepare("
      SELECT SUM(TIMESTAMPDIFF(SECOND, break_start, break_end)) AS break_seconds
      FROM attendance_breaks
      WHERE attendance_id=? AND break_end IS NOT NULL
    ");
    $stmt->execute([$attendanceId]);
    $breakSeconds = (int) ($stmt->fetch()['break_seconds'] ?? 0);

    return $this->db->prepare("
      UPDATE attendance
      SET logout_time = NOW(),
          total_minutes =
            TIMESTAMPDIFF(SECOND, login_time, NOW()) - ?,
          status='logged_out'
      WHERE id=?
    ")->execute([$breakSeconds, $attendanceId]);
  }

  // STATUS (for live timer)
  public function getStatus($userId) {
    $stmt = $this->db->prepare("
      SELECT a.*,
      (
        SELECT SUM(TIMESTAMPDIFF(SECOND, break_start, break_end))
        FROM attendance_breaks
        WHERE attendance_id=a.id AND break_end IS NOT NULL
      ) AS break_seconds
      FROM attendance a
      WHERE a.user_id=? AND a.logout_time IS NULL
      ORDER BY a.id DESC LIMIT 1
    ");
    $stmt->execute([$userId]);
    return $stmt->fetch();
  }

  public function getLiveAttendance() {
    $sql = "
      SELECT 
        u.id,
        u.name,
        a.login_time,
        a.logout_time,
        a.break_start,
        a.break_end,
        a.total_minutes,
        CASE
          WHEN a.logout_time IS NOT NULL THEN 'logged_out'
          WHEN a.break_start IS NOT NULL AND a.break_end IS NULL THEN 'break'
          ELSE 'active'
        END AS status
      FROM attendance a
      JOIN users u ON u.id = a.user_id
      WHERE DATE(a.login_time) = CURDATE()
      ORDER BY a.login_time ASC
    ";
  
    return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  }
  

}
