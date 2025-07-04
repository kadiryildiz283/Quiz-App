<?php
// admin/soru_sil.php

session_start();
require '../includes/db_baglantisi.php';

// Güvenlik Kontrolü: Sadece giriş yapmış adminler bu sayfaya erişebilir.
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// URL'den gelen 'id' parametresini kontrol et ve al.
// filter_input ile almak, doğrudan $_GET kullanmaktan daha güvenlidir.
$soru_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$soru_id) {
    // Eğer 'id' parametresi yoksa veya geçerli bir tamsayı değilse,
    // kullanıcıyı dashboard'a yönlendir.
    header("Location: dashboard.php");
    exit();
}

// SQL Injection'a karşı korunmak için PREPARED STATEMENT kullanıyoruz.
$stmt = $conn->prepare("DELETE FROM sorular WHERE id = ?");

// Eğer prepare işlemi başarısız olursa hatayı göster
if (false === $stmt) {
    die('SQL hazırlama hatası: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $soru_id); // 'i' -> integer

// Sorguyu çalıştır
if ($stmt->execute()) {
    // Silme işlemi başarılı oldu.
    // Başarı mesajını session'a kaydet.
    $_SESSION['mesaj'] = "Soru (ID: " . $soru_id . ") başarıyla silindi.";
} else {
    // Silme işlemi başarısız oldu.
    // Hata mesajını session'a kaydet.
    $_SESSION['mesaj'] = "Hata: Soru silinemedi. " . htmlspecialchars($stmt->error);
}

// İşlem tamamlandıktan sonra statement ve bağlantıyı kapat.
$stmt->close();
$conn->close();

// Kullanıcıyı tekrar dashboard'a yönlendir.
header("Location: dashboard.php");
exit();

?><?php
// admin/soru_sil.php

session_start();
require '../includes/db_baglantisi.php';

// Güvenlik Kontrolü: Sadece giriş yapmış adminler bu sayfaya erişebilir.
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// URL'den gelen 'id' parametresini kontrol et ve al.
// filter_input ile almak, doğrudan $_GET kullanmaktan daha güvenlidir.
$soru_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$soru_id) {
    // Eğer 'id' parametresi yoksa veya geçerli bir tamsayı değilse,
    // kullanıcıyı dashboard'a yönlendir.
    header("Location: dashboard.php");
    exit();
}

// SQL Injection'a karşı korunmak için PREPARED STATEMENT kullanıyoruz.
$stmt = $conn->prepare("DELETE FROM sorular WHERE id = ?");

// Eğer prepare işlemi başarısız olursa hatayı göster
if (false === $stmt) {
    die('SQL hazırlama hatası: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $soru_id); // 'i' -> integer

// Sorguyu çalıştır
if ($stmt->execute()) {
    // Silme işlemi başarılı oldu.
    // Başarı mesajını session'a kaydet.
    $_SESSION['mesaj'] = "Soru (ID: " . $soru_id . ") başarıyla silindi.";
} else {
    // Silme işlemi başarısız oldu.
    // Hata mesajını session'a kaydet.
    $_SESSION['mesaj'] = "Hata: Soru silinemedi. " . htmlspecialchars($stmt->error);
}

// İşlem tamamlandıktan sonra statement ve bağlantıyı kapat.
$stmt->close();
$conn->close();

// Kullanıcıyı tekrar dashboard'a yönlendir.
header("Location: dashboard.php");
exit();

?><?php
// admin/soru_sil.php

session_start();
require '../includes/db_baglantisi.php';

// Güvenlik Kontrolü: Sadece giriş yapmış adminler bu sayfaya erişebilir.
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// URL'den gelen 'id' parametresini kontrol et ve al.
// filter_input ile almak, doğrudan $_GET kullanmaktan daha güvenlidir.
$soru_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$soru_id) {
    // Eğer 'id' parametresi yoksa veya geçerli bir tamsayı değilse,
    // kullanıcıyı dashboard'a yönlendir.
    header("Location: dashboard.php");
    exit();
}

// SQL Injection'a karşı korunmak için PREPARED STATEMENT kullanıyoruz.
$stmt = $conn->prepare("DELETE FROM sorular WHERE id = ?");

// Eğer prepare işlemi başarısız olursa hatayı göster
if (false === $stmt) {
    die('SQL hazırlama hatası: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $soru_id); // 'i' -> integer

// Sorguyu çalıştır
if ($stmt->execute()) {
    // Silme işlemi başarılı oldu.
    // Başarı mesajını session'a kaydet.
    $_SESSION['mesaj'] = "Soru (ID: " . $soru_id . ") başarıyla silindi.";
} else {
    // Silme işlemi başarısız oldu.
    // Hata mesajını session'a kaydet.
    $_SESSION['mesaj'] = "Hata: Soru silinemedi. " . htmlspecialchars($stmt->error);
}

// İşlem tamamlandıktan sonra statement ve bağlantıyı kapat.
$stmt->close();
$conn->close();

// Kullanıcıyı tekrar dashboard'a yönlendir.
header("Location: dashboard.php");
exit();

?><?php
// admin/soru_sil.php

session_start();
require '../includes/db_baglantisi.php';

// Güvenlik Kontrolü: Sadece giriş yapmış adminler bu sayfaya erişebilir.
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// URL'den gelen 'id' parametresini kontrol et ve al.
// filter_input ile almak, doğrudan $_GET kullanmaktan daha güvenlidir.
$soru_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$soru_id) {
    // Eğer 'id' parametresi yoksa veya geçerli bir tamsayı değilse,
    // kullanıcıyı dashboard'a yönlendir.
    header("Location: dashboard.php");
    exit();
}

// SQL Injection'a karşı korunmak için PREPARED STATEMENT kullanıyoruz.
$stmt = $conn->prepare("DELETE FROM sorular WHERE id = ?");

// Eğer prepare işlemi başarısız olursa hatayı göster
if (false === $stmt) {
    die('SQL hazırlama hatası: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $soru_id); // 'i' -> integer

// Sorguyu çalıştır
if ($stmt->execute()) {
    // Silme işlemi başarılı oldu.
    // Başarı mesajını session'a kaydet.
    $_SESSION['mesaj'] = "Soru (ID: " . $soru_id . ") başarıyla silindi.";
} else {
    // Silme işlemi başarısız oldu.
    // Hata mesajını session'a kaydet.
    $_SESSION['mesaj'] = "Hata: Soru silinemedi. " . htmlspecialchars($stmt->error);
}

// İşlem tamamlandıktan sonra statement ve bağlantıyı kapat.
$stmt->close();
$conn->close();

// Kullanıcıyı tekrar dashboard'a yönlendir.
header("Location: dashboard.php");
exit();

?>
