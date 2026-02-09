<?php
session_start();

/* =========================
   BOOTSTRAP
========================= */
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Database.php';

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/EmployeeController.php';
require_once __DIR__ . '/../app/controllers/AttendanceController.php';
require_once __DIR__ . '/../app/controllers/LeaveController.php';

require_once __DIR__ . '/../app/controllers/ChatController.php';
require_once __DIR__ . '/../app/controllers/NotificationController.php';

/* =========================
   ROUTE RESOLUTION (FIX)
========================= */
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

/**
 * Remove project base path
 */
$basePath = '/crm-hrms/public';
$route = str_replace($basePath, '', $uri);

/**
 * Normalize route
 */
$route = '/' . trim($route, '/');

/* =========================
   AJAX / REGEX ROUTES FIRST
========================= */

// Employee fetch
if (preg_match('#^/employees/fetch/([0-9]+)$#', $route, $m)) {
    (new EmployeeController())->fetch($m[1]);
    exit;
}

// Employee toggle
if (preg_match('#^/employees/toggle/([0-9]+)$#', $route, $m)) {
    (new EmployeeController())->toggleStatus($m[1]);
    exit;
}

/* =========================
   MAIN ROUTER
========================= */
switch ($route) {

    /* ========= ROOT ========= */
    case '/':
        if (isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        (new AuthController())->login();
        break;

    /* ========= AUTH ========= */
    case '/login':
        (new AuthController())->login();
        break;

    case '/logout':
        (new AuthController())->logout();
        break;

    /* ========= DASHBOARD ========= */
    case '/dashboard':
        (new DashboardController())->index();
        break;

    /* ========= CHAT ========= */
    case '/chat/send':
        (new ChatController())->send();
        break;

    case '/chat/fetch':
        (new ChatController())->fetch();
        break;

    /* ========= NOTIFICATIONS ========= */
    case '/notifications/fetch':
        (new NotificationController())->fetch();
        break;

    /* ========= EMPLOYEES ========= */
    case '/employees/list':
        (new EmployeeController())->list();
        break;

    case '/employees/create':
        (new EmployeeController())->create();
        break;

    case '/employees/store':
        (new EmployeeController())->store();
        break;

    case '/employees/update':
        (new EmployeeController())->update();
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

    case '/employees/bulkUpload':
        (new EmployeeController())->bulkUpload();
        break;

    /* ========= ATTENDANCE ========= */
    case '/attendance':
        (new AttendanceController())->today();
        break;

    case '/attendance/attendance_list':
        (new AttendanceController())->attendance_list();
        break;

    case '/attendance/check-in':
        (new AttendanceController())->checkIn();
        break;

    case '/attendance/check-out':
        (new AttendanceController())->checkOut();
        break;

    case '/attendance/status':
        (new AttendanceController())->status();
        break;

    case '/attendance/action':
        (new AttendanceController())->action();
        break;

    case '/attendance/startBreak':
        (new AttendanceController())->startBreak();
        break;

    case '/attendance/syncFromServer':
        (new AttendanceController())->syncFromServer();
        break;

        case '/attendance/liveAttendance':
            (new AttendanceController())->liveAttendance();
            break;

        

        case '/leaves/apply':
            (new LeaveController())->apply();
            break;
          
          case '/leaves/history':
            (new LeaveController())->history();
            break;

            
          
          case '/leaves/admin':
            (new LeaveController())->admin();
            break;
          
          case '/leave/action':
            (new LeaveController())->action();
            break;

         case '/leaves/balance':
            (new LeaveController())->balance();
            break;
        
         case '/leave/enterpriseCalendar':
                    (new LeaveController())->enterpriseCalendar();
                    break;

                    case '/leave/storeLeaveType':
                        (new LeaveController())->storeLeaveType();
                        break;
                    
                    case '/leave/listLeaveTypes':
                        (new LeaveController())->listLeaveTypes();
                        break;
                    

                        case '/holiday/store':
                            (new LeaveController())->storeHoliday();
                            break;
                        
                        case '/holiday/calendar':
                            (new LeaveController())->holidayCalendar();
                            break;

                            case '/chat/index':
                                (new ChatController())->index();
                                break;
                        

                
              

    /* ========= 404 ========= */
    default:
        http_response_code(404);
        echo "404 Route Not Found";
        break;
}
