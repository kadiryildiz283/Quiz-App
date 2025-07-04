<?php
// cikis.php

session_start();

// Tüm session değişkenlerini temizle.
$_SESSION = array();

// Session'ı sonlandır.
session_destroy();

// Kullanıcıyı ana sayfaya yönlendir.
header("Location: index.php");
exit();
?>
