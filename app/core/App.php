<?php
class App {
    public function __construct() {
        $url = $_GET['url'] ?? 'auth/login';
        $url = explode('/', filter_var($url, FILTER_SANITIZE_URL));
        $controller = ucfirst($url[0]).'Controller';
        require_once "../app/controllers/$controller.php";
        $obj = new $controller;
        $method = $url[1] ?? 'index';
        $obj->$method();
    }
}
