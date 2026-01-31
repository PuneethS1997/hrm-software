<?php
// class RoleMiddleware {
//   public static function allow($roles = []) {
//     if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], $roles)) {
//       header('HTTP/1.1 403 Forbidden');
//       echo 'Access Denied';
//       exit;
//     }
//   }
// }

class RoleMiddleware
{
    public static function allow($roles)
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], $roles)) {
            header('Location: /crm-hrms/public/dashboard');
            exit;
        }
    }
}
