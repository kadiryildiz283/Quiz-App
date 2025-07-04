<?php
$sunucu = "localhost";        // Genellikle "localhost" veya "127.0.0.1"
$kullanici_adi = "root";      // Varsayılan MySQL kullanıcısı
$sifre = "123456";                  // Varsayılan MySQL şifresi (genellikle boş)
$veritabani_adi = "quiz_uygulamasi"; // 2. Adımda oluşturduğumuz veritabanının adı

// mysqli kullanarak nesne tabanlı bir bağlantı oluşturuyoruz.
$conn = new mysqli($sunucu, $kullanici_adi, $sifre, $veritabani_adi);

// Bağlantıyı kontrol et
// Eğer bağlantıda bir hata varsa, hatayı ekrana yazdır ve çalışmayı durdur.
if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

// Türkçe karakter sorunlarını önlemek için bağlantının karakter setini ayarlıyoruz.
// Bu adım, veritabanına veri yazarken veya okurken Türkçe karakterlerin (ş, ç, ğ, ı, ü, ö) bozuk görünmesini engeller.
$conn->set_charset("utf8mb4");

?>
