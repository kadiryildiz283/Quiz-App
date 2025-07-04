<?php
// kayit.php

session_start();
require 'includes/db_baglantisi.php';

// Eğer kullanıcı zaten giriş yapmışsa, onu ana sayfaya yönlendir.
if (isset($_SESSION['kullanici_id'])) {
    header("Location: index.php");
    exit();
}

$hata_mesaji = '';
$basari_mesaji = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kullanici_adi = trim($_POST['kullanici_adi']);
    $email = trim($_POST['email']);
    $sifre = $_POST['sifre'];
    $sifre_tekrar = $_POST['sifre_tekrar'];

    // Alanların boş olup olmadığını kontrol et
    if (empty($kullanici_adi) || empty($email) || empty($sifre)) {
        $hata_mesaji = "Lütfen tüm alanları doldurun.";
    } 
    // Email formatını kontrol et
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $hata_mesaji = "Lütfen geçerli bir e-posta adresi girin.";
    }
    // Şifrelerin eşleşip eşleşmediğini kontrol et
    elseif ($sifre !== $sifre_tekrar) {
        $hata_mesaji = "Girdiğiniz şifreler eşleşmiyor.";
    } 
    // Şifre uzunluğunu kontrol et
    elseif (strlen($sifre) < 6) {
        $hata_mesaji = "Şifreniz en az 6 karakter uzunluğunda olmalıdır.";
    } 
    else {
        // Kullanıcı adı veya email veritabanında mevcut mu diye kontrol et
        $stmt = $conn->prepare("SELECT id FROM kullanicilar WHERE kullanici_adi = ? OR email = ?");
        $stmt->bind_param("ss", $kullanici_adi, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $hata_mesaji = "Bu kullanıcı adı veya e-posta adresi zaten kullanılıyor.";
        } else {
            // Her şey yolundaysa, kullanıcıyı kaydet
            // Şifreyi hash'le
            $sifrelenmis_parola = password_hash($sifre, PASSWORD_DEFAULT);

            $insert_stmt = $conn->prepare("INSERT INTO kullanicilar (kullanici_adi, email, sifre) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("sss", $kullanici_adi, $email, $sifrelenmis_parola);
            
            if ($insert_stmt->execute()) {
                $_SESSION['mesaj'] = "Kayıt başarılı! Şimdi giriş yapabilirsiniz.";
                header("Location: giris.php");
                exit();
            } else {
                $hata_mesaji = "Kayıt sırasında bir hata oluştu. Lütfen tekrar deneyin.";
            }
            $insert_stmt->close();
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol - Quiz Uygulaması</title>
    <link rel="stylesheet" href="css/style.css">
    <style> .login-container { max-width: 450px; } </style>
</head>
<body>
    <div class="login-container">
        <form method="POST" action="kayit.php">
            <h2>Yeni Oyuncu Kaydı</h2>
            
            <?php if (!empty($hata_mesaji)): ?>
                <p class="hata"><?php echo htmlspecialchars($hata_mesaji); ?></p>
            <?php endif; ?>

            <div class="input-group">
                <label for="kullanici_adi">Kullanıcı Adı:</label>
                <input type="text" id="kullanici_adi" name="kullanici_adi" value="<?php echo isset($kullanici_adi) ? htmlspecialchars($kullanici_adi) : ''; ?>" required>
            </div>
            <div class="input-group">
                <label for="email">E-posta Adresi:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
            </div>
            <div class="input-group">
                <label for="sifre">Şifre:</label>
                <input type="password" id="sifre" name="sifre" required>
            </div>
            <div class="input-group">
                <label for="sifre_tekrar">Şifre Tekrar:</label>
                <input type="password" id="sifre_tekrar" name="sifre_tekrar" required>
            </div>
            <button type="submit">Kayıt Ol</button>
            <p style="text-align:center; margin-top:20px;">
                Zaten bir hesabın var mı? <a href="giris.php">Giriş Yap</a>
            </p>
        </form>
    </div>
</body>
</html>
