<?php
// admin/kullanici_durum_degistir.php dosyasını güncelleyin
session_start();
require '../includes/db_baglantisi.php';
if (!isset($_SESSION['admin_id'])) { exit('Yetkisiz erişim!'); }

$kullanici_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($kullanici_id) {
    // Kullanıcının hem çözme iznini AÇ (1 yap) hem de durumunu "Boşta" (0 yap) olarak ayarla.
    $stmt = $conn->prepare("UPDATE kullanicilar SET quiz_cozebilir = 1, quiz_durumu = 0 WHERE id = ?");
    $stmt->bind_param("i", $kullanici_id);
    $stmt->execute();
    $stmt->close();
}
// Kullanıcı yönetimi sayfasına geri yönlendir
header("Location: kullanici_yonetimi.php");
exit();
?>
