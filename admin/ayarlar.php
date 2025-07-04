<?php
// admin/ayarlar.php
session_start();
require '../includes/db_baglantisi.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$mesaj = '';

// Form gönderildiyse ayarı güncelle
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $yeni_sure = filter_input(INPUT_POST, 'soru_suresi', FILTER_VALIDATE_INT);
    
    if ($yeni_sure && $yeni_sure > 0) {
        $stmt = $conn->prepare("UPDATE ayarlar SET ayar_degeri = ? WHERE ayar_adi = 'soru_suresi'");
        $stmt->bind_param("s", $yeni_sure);
        if ($stmt->execute()) {
            $mesaj = "Ayar başarıyla güncellendi!";
        } else {
            $mesaj = "Hata: Ayar güncellenemedi.";
        }
        $stmt->close();
    } else {
        $mesaj = "Lütfen geçerli bir süre girin (sadece pozitif sayılar).";
    }
}

// Mevcut ayarı veritabanından çek
$result = $conn->query("SELECT ayar_degeri FROM ayarlar WHERE ayar_adi = 'soru_suresi'");
$mevcut_sure = $result->fetch_assoc()['ayar_degeri'] ?? 20;
$conn->close();

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Genel Ayarlar - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style> /* Diğer admin sayfalarındaki stiller */
        .container { max-width: 600px; margin: 2em auto; background-color: #fff; padding: 2em; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .button-group { display: flex; justify-content: space-between; align-items: center; }
        .form-group { margin: 2em 0; }
        .form-group label { font-weight: bold; font-size: 1.1em; }
        .form-group input { width: 100%; padding: 10px; margin-top: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .mesaj { padding: 10px; border-radius: 5px; margin-top: 15px; text-align: center; }
        .basari { background-color: #d4edda; color: #155724; }
        .hata { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
<div class="container">
    <div class="button-group">
        <h2>Genel Quiz Ayarları</h2>
        <a href="dashboard.php">&larr; Panele Geri Dön</a>
    </div><hr>
    
    <?php if ($mesaj): ?>
        <p class="mesaj <?php echo (strpos($mesaj, 'başarıyla') !== false) ? 'basari' : 'hata'; ?>">
            <?php echo $mesaj; ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="ayarlar.php">
        <div class="form-group">
            <label for="soru_suresi">Soru Başına Süre (saniye olarak):</label>
            <input type="number" id="soru_suresi" name="soru_suresi" value="<?php echo htmlspecialchars($mevcut_sure); ?>" min="5" required>
        </div>
        <button type="submit">Ayarları Kaydet</button>
    </form>
</div>
</body>
</html>
