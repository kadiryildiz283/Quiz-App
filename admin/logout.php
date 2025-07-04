<?php
// admin/logout.php

// Her session işleminden önce oturumu başlatmamız gerekir.
session_start();

// 1. Tüm session değişkenlerini temizle.
$_SESSION = array();

// 2. Session'ı sonlandır.
session_destroy();

// 3. Kullanıcıyı giriş sayfasına yönlendir.
header("Location: index.php");
exit(); // Yönlendirmeden sonra kodun çalışmasını durdur.
?>
