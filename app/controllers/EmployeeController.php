<?php
// die('UPDATE CONTROLLER HIT');

require_once '../app/middleware/AuthMiddleware.php';
require_once '../app/middleware/RoleMiddleware.php';
require_once '../app/models/User.php';

class EmployeeController
{

    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function list()
    {
        AuthMiddleware::handle();
        RoleMiddleware::allow(['super_admin', 'admin']);

        $employees = $this->user->allEmployees();
        require '../app/views/employees/list.php';
    }

   


    public function create()
    {
        AuthMiddleware::handle();
        RoleMiddleware::allow(['super_admin', 'admin']);
        require '../app/views/employees/create.php';
    }

    public function store()
    {
        AuthMiddleware::handle();
        RoleMiddleware::allow(['super_admin', 'admin']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->user->createEmployee($_POST);
            header('Location: /crm-hrms/public/employees/list');
            exit;
        }
    }

    public function toggleStatus($id)
    {
        AuthMiddleware::handle();
        RoleMiddleware::allow(['super_admin', 'admin']);

        (new User())->toggleStatus($id);

        header('Location: /crm-hrms/public/employees/list');
        exit;
    }




   public function bulkUpload()
{
    header('Content-Type: application/json');

    try {
        if (!isset($_FILES['csv'])) {
            echo json_encode(['status'=>'error','msg'=>'No file']);
            exit;
        }

        $result = $this->user->bulkInsert($_FILES['csv']['tmp_name']);

        echo json_encode([
            'status' => 'success',
            'inserted' => $result['inserted'],
            'skipped' => $result['skipped']
        ]);
        exit;

    } catch (Exception $e) {
        echo json_encode(['status'=>'error','msg'=>$e->getMessage()]);
        exit;
    }
}



    public function fetch($id)
    {
        header('Content-Type: application/json');

        require_once '../app/models/User.php';

        $user = new User();
        $employee = $user->find($id);

        if (!$employee) {
            http_response_code(404);
            echo json_encode(['error' => 'Employee not found']);
            exit;
        }

        echo json_encode($employee);
        exit;
    }



    public function update()
    {
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['status' => 'error', 'msg' => 'Invalid request']);
                exit;
            }

            if (empty($_POST['id'])) {
                echo json_encode(['status' => 'error', 'msg' => 'ID missing']);
                exit;
            }

            require_once '../app/models/User.php';
            $user = new User();

            $id = $_POST['id'];

            // Remove id from the data array to prevent updating the primary key
            $data = $_POST;
            unset($data['id']);

            $updated = $user->update($id, $data);

            if ($updated) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Update failed']);
            }
            exit;
        } catch (Throwable $e) {
            echo json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function delete()
    {
        header('Content-Type: application/json');

        AuthMiddleware::handle();
        RoleMiddleware::allow(['super_admin', 'admin']);

        if (empty($_POST['id'])) {
            echo json_encode(['status' => 'error', 'msg' => 'ID missing']);
            return;
        }

        require_once '../app/models/User.php';

        (new User())->delete($_POST['id']);

        echo json_encode(['status' => 'success']);
    }

    public function bulkDelete()
{
    header('Content-Type: application/json');

    AuthMiddleware::handle();
    RoleMiddleware::allow(['super_admin', 'admin']);

    if (empty($_POST['ids'])) {
        echo json_encode(['status' => 'error', 'msg' => 'No IDs']);
        return;
    }

    require_once '../app/models/User.php';

    (new User())->bulkDelete($_POST['ids']);

    echo json_encode(['status' => 'success']);
}


    /* =========================
       SOFT DELETE (AJAX)
    ========================= */
   public function softdelete()
{
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['id'])) {
        echo json_encode(['status' => 'error', 'msg' => 'ID missing']);
        exit;
    }

    $user = new User();
    $user->softDelete($data['id']);

    echo json_encode(['status' => 'success']);
    exit;
}



    /* =========================
       BULK SOFT DELETE
    ========================= */
   public function bulkSoftDelete()
{
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['ids'])) {
        echo json_encode(['status' => 'error', 'msg' => 'No IDs']);
        exit;
    }

    (new User())->bulkSoftDelete($data['ids']);

    echo json_encode(['status' => 'success']);
    exit;
}

// undo

public function undo()
{
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['id'])) {
        echo json_encode(['status' => 'error', 'msg' => 'ID missing']);
        exit;
    }

    (new User())->undo($data['id']);

    echo json_encode(['status' => 'success']);
    exit;
}

// bulk undo
public function bulkUndo()
{
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['ids'])) {
        echo json_encode(['status' => 'error']);
        exit;
    }

    (new User())->bulkUndo($data['ids']);

    echo json_encode(['status' => 'success']);
    exit;
}




   /* =========================
   TRASH PAGE
========================= */
public function trash()
{
    AuthMiddleware::handle();
    RoleMiddleware::allow(['super_admin', 'admin']);

    $employees = (new User())->trashedEmployees();
    require '../app/views/employees/trash.php';
}

/* =========================
   RESTORE SINGLE
========================= */
public function restore()
{
    header('Content-Type: application/json');

    try {
        $id = $_POST['id'] ?? null;

        if (!$id) {
            echo json_encode(['status'=>'error','msg'=>'ID missing']);
            exit;
        }

        $this->user->restore($id);

        echo json_encode(['status'=>'success']);
        exit;

    } catch (Throwable $e) {
        echo json_encode(['status'=>'error','msg'=>$e->getMessage()]);
        exit;
    }
}


/* =========================
   BULK RESTORE
========================= */
public function bulkRestore()
{
    header('Content-Type: application/json');

    try {
        $ids = $_POST['ids'] ?? [];

        if (empty($ids)) {
            echo json_encode(['status'=>'error','msg'=>'No IDs']);
            exit;
        }

        $this->user->bulkRestore($ids);

        echo json_encode(['status'=>'success']);
        exit;

    } catch (Throwable $e) {
        echo json_encode(['status'=>'error','msg'=>$e->getMessage()]);
        exit;
    }
}




}
