 <?php

// class Database extends PDO {

//   public function __construct() {
//     parent::__construct(
//       "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
//       DB_USER,
//       DB_PASS,
//       [
//         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
//       ]
//     );
//   }
// } 


// class Database {

//     protected $db;

//     public function __construct() {
//         try {
//             $this->db = new PDO(
//                 "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
//                 DB_USER,
//                 DB_PASS,
//                 [
//                     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//                     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
//                 ]
//             );
//         } catch (PDOException $e) {
//             die("DB Connection Failed: " . $e->getMessage());
//         }
//     }

//         // ✅ ADD THIS METHOD
//     public function query($sql, $params = []) {
//         $stmt = $this->db->prepare($sql);
//         $stmt->execute($params);
//         return $stmt;
//     }

// }

class Database {
  public $db;

  public function __construct() {
    $this->db = new PDO(
      "mysql:host=".DB_HOST.";dbname=".DB_NAME,
      DB_USER,
      DB_PASS
    );
    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
}

  
