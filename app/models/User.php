<?php
// die('MODEL UPDATE HIT');

class User
{
  private $db;

  public function __construct()
  {
    $this->db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
  }

  public function login($email, $password)
  {
    $stmt = $this->db->prepare(
      "SELECT u.*, r.name as role FROM users u 
       JOIN roles r ON u.role_id=r.id WHERE email=? AND status=1"
    );
    $stmt->execute([$email]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($u && password_verify($password, $u['password'])) {
      $_SESSION['user'] = [
        'id' => $u['id'],
        'name' => $u['name'],
        'email' => $u['email'],
        'role' => $u['role']
      ];
      return true;
    }
    return false;
  }

  public function onlineUsers()
  {
    return $this->query("
    SELECT id, name,
    IF(last_activity >= NOW() - INTERVAL 5 MINUTE, 1, 0) AS online
    FROM users
  ")->fetchAll();
  }

  public function allEmployees()
  {
    $stmt = $this->db->prepare("
            SELECT id, employee_code, name, email, phone, job_role, department, status
            FROM users  
            WHERE role_id = 3 AND deleted_at IS NULL
            ORDER BY id DESC
        ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function createEmployee($data)
  {
    $stmt = $this->db->prepare("
            INSERT INTO users
            (employee_code, name, email, phone, job_role, department, joining_date, password, role_id, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 3, 1)
        ");

    return $stmt->execute([
      $data['employee_code'],
      $data['name'],
      $data['email'],
      $data['phone'],
      $data['job_role'],
      $data['department'],
      $data['joining_date'],
      password_hash($data['password'], PASSWORD_DEFAULT)
    ]);
  }

  public function toggleStatus($id)
  {
    $stmt = $this->db->prepare("
        UPDATE users 
        SET status = IF(status = 1, 0, 1)
        WHERE id = ?
    ");
    return $stmt->execute([$id]);
  }

  public function find($id)
  {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }


  public function update($id, $data)
  {
    if (empty($data)) return false; // nothing to update

    $fields = [];
    $params = [];

    foreach ($data as $key => $value) {
      // Escape reserved words like 'name'
      if (in_array($key, ['name'])) {
        $fields[] = "`$key` = :$key";
      } else {
        $fields[] = "$key = :$key";
      }
      $params[":$key"] = $value;
    }

    $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
    $params[':id'] = $id;

    $stmt = $this->db->prepare($sql);
    return $stmt->execute($params); // returns true or false
  }

  

 public function bulkInsert($file)
{
    $handle = fopen($file, "r");
    fgetcsv($handle);

    $inserted = 0;
    $skipped = 0;

    while (($row = fgetcsv($handle, 1000, ",")) !== false) {

        $check = $this->db->prepare("SELECT id FROM users WHERE email=?");
        $check->execute([$row[2]]);

        if ($check->fetch()) {
            $skipped++;
            continue;
        }

        $stmt = $this->db->prepare("
            INSERT INTO users
            (employee_code,name,email,phone,job_role,department,joining_date,password,role_id,status)
            VALUES (?,?,?,?,?,?,?, ?,3,1)
        ");

        $stmt->execute([
            $row[0],$row[1],$row[2],$row[3],
            $row[4],$row[5],$row[6],
            password_hash($row[7], PASSWORD_DEFAULT)
        ]);

        $inserted++;
    }

    fclose($handle);

    return compact('inserted','skipped');
}



  public function delete($id)
  {
    $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
  }

  public function bulkDelete($ids)
  {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $this->db->prepare("DELETE FROM users WHERE id IN ($placeholders)");
    $stmt->execute($ids);
  }

 

    

    /* =========================
       SOFT DELETE (SINGLE)
    ========================= */
public function softDelete($id)
{
    $stmt = $this->db->prepare(
        "UPDATE users SET deleted_at = NOW() WHERE id = ?"
    );
    return $stmt->execute([$id]);
}


public function undo($id)
{
    $stmt = $this->db->prepare(
        "UPDATE users SET deleted_at = NULL WHERE id = ?"
    );
    return $stmt->execute([$id]);
}




    /* =========================
       BULK SOFT DELETE
    ========================= */
   public function bulkSoftDelete($ids)
{
    $in = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $this->db->prepare(
        "UPDATE users SET deleted_at = NOW() WHERE id IN ($in)"
    );
    $stmt->execute($ids);
}



public function bulkUndo($ids)
{
    $in = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $this->db->prepare(
        "UPDATE users SET deleted_at = NULL WHERE id IN ($in)"
    );
    $stmt->execute($ids);
}


    /* =========================
       TRASH
    ========================= */
  
    public function trashedEmployees() {
    $sql = "SELECT * FROM users WHERE deleted_at IS NOT NULL ORDER BY id DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // <-- use fetchAll instead of resultSet
}


    /* =========================
       RESTORE
    ========================= */
    public function restore($id)
   {
    $stmt = $this->db->prepare(
        "UPDATE users SET deleted_at = NULL WHERE id = ?"
    );
    return $stmt->execute([$id]);
}

    /* =========================
       BULK RESTORE
    ========================= */
    public function bulkRestore($ids)
  {
    $in = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $this->db->prepare(
        "UPDATE users SET deleted_at = NULL WHERE id IN ($in)"
    );
    return $stmt->execute($ids);
}
}
