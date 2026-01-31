<?php
class AuthHelper {
  public static function check() {
    return isset($_SESSION['user']);
  }

  public static function user() {
    return $_SESSION['user'] ?? null;
  }

  public static function role() {
    return $_SESSION['user']['role'] ?? null;
  }
}
