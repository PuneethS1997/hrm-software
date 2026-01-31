<?php
session_start();
// echo '<pre>';
// var_dump($_SERVER['REQUEST_URI']);
// exit;
echo '<pre>';
var_dump($route);
exit;


// echo '<pre>';
// var_dump($_SERVER['REQUEST_URI']);
// $route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// $route = str_replace('/crm-hrms/public', '', $route);
// var_dump($route);
// exit;


require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/controllers/ChatController.php';
require_once __DIR__ . '/../app/controllers/NotificationController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/EmployeeController.php';
require_once __DIR__ . '/../app/controllers/AttendanceController.php';



// $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// // remove project folder path
// $basePath = '/crm-hrms/public';
// $route = str_replace($basePath, '', $uri);
$route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$route = str_replace('/crm-hrms/public', '', $route);

// ============================
// EMPLOYEE FETCH (AJAX-FIRST) 
// ============================
if (preg_match('~^/employees/fetch/([0-9]+)$~', $route, $matches)) {
    require_once '../app/controllers/EmployeeController.php';
    (new EmployeeController())->fetch($matches[1]);
    exit;
}
if ($route === '/employees/update') {
    require_once '../app/controllers/EmployeeController.php';
    (new EmployeeController())->update();
    exit;
}

switch ($route) {

    case '':
        // case '/':
        //     echo "CRM-HRMS is running ✅";
        //     break;
    case '/':
        if (isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/dashboard');
        } else {
            require '../app/controllers/AuthController.php';
            (new AuthController())->login();
        }
        break;



    case '/dashboard':
        (new DashboardController())->index();
        break;

    case '/logout':
        require '../app/controllers/AuthController.php';
        (new AuthController())->logout();
        break;

    case '/login':
        require '../app/controllers/AuthController.php';
        (new AuthController())->login();
        break;




    // case '/chat':
    //     (new ChatController())->index();
    //     break;

    case '/chat/send':
        (new ChatController())->send();
        break;

    case '/chat/fetch':
        (new ChatController())->fetch();
        break;

    case '/notifications/fetch':
        (new NotificationController())->fetch();
        break;

    // case '/notifications/read':
    //     (new NotificationController())->read();
    //     break;




    case '/employees/list':
        (new EmployeeController())->list();
        break;

    case '/employees/create':
        (new EmployeeController())->create();
        break;

    case '/employees/store':
        (new EmployeeController())->store();
        break;

    case (preg_match('#^/employees/toggle/(\d+)$#', $route, $m) ? true : false):
        (new EmployeeController())->toggleStatus($m[1]);
        break;



    case '/employees/bulkUpload':
        (new EmployeeController())->bulkUpload();
        break;



    case '/employees/delete':
        (new EmployeeController())->delete();
        break;

    case '/employees/bulk-delete':
        (new EmployeeController())->bulkDelete();
        break;
    case '/employees/softdelete':
        (new EmployeeController())->softdelete();
        break;

    case '/employees/bulkSoftDelete':
        (new EmployeeController())->bulkSoftDelete();
        break;

    case '/employees/bulkUndo':
        (new EmployeeController())->bulkUndo();
        break;


    case '/employees/trash':
        (new EmployeeController())->trash();
        break;

    case '/employees/restore':
        (new EmployeeController())->restore();
        break;

    case '/employees/bulkRestore':
        (new EmployeeController())->bulkRestore();
        break;

    case '/attendance':
        (new AttendanceController())->today();
        break;

    case '/attendance/check-in':
        (new AttendanceController())->checkIn();
        break;

    case '/attendance/check-out':
        (new AttendanceController())->checkOut();
        break;

    case '/attendance/index':
        (new AttendanceController())->index();
        break;





    default:
        http_response_code(404);
        echo "404 Route Not Found";
}
