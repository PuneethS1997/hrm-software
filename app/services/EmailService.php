<?php
require_once '../app/config/mail.php';

class EmailService {

  public static function send($to, $subject, $message) {
    $headers  = "From: ".MAIL_FROM." <".MAIL_USER.">\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";

    return mail($to, $subject, $message, $headers);
  }
}
