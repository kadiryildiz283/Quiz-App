<?php
// admin/soru_duzenle.php

session_start();
require '../includes/db_baglantisi.php';

// Güvenlik Kontrolü
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$hata_mesaji = '';
$soru_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Eğer form gönderildiyse (POST metodu), güncelleme işlemini yap
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Formdan gelen verileri al
    $soru_id = filter_input(INPUT_POST, 'soru_id', FILTER_VALIDATE_INT);
    $soru_metni = trim($_POST['soru_metni']);
    $secenek_metinleri = $_POST['secenek_metinleri']; // Seçenek metinleri dizisi
    $secenek_idler = $_POST['secenek_idler']; // Seçenek ID'leri dizisi
    $dogru_secenek_id = filter_input(INPUT_POST, 'dogru_secenek_id', FILTER_VALIDATE_INT);

    // Basit doğrulama
    if (!$soru_id || empty($soru_metni) || count($secenek_metinleri) < 2 || min(array_map('strlen', $secenek_metinleri)) == 0 || !$dogru_secenek_id) {
        $hata_mesaji = "Lütfen tüm alanları eksiksiz doldurun.";
    } else {
        // Veritabanı işlemlerini güvenli bir şekilde yapmak için TRANSACTION kullanıyoruz.
        $conn->begin_transaction();
        try {
            // 1. ADIM: Soru metnini ve doğru cevap ID'sini güncelle
            $stmt1 = $conn->prepare("UPDATE sorular SET soru_metni = ?, dogru_secenek_id = ? WHERE id = ?");
            $stmt1->bind_param("sii", $soru_metni, $dogru_secenek_id, $soru_id);
            $stmt1->execute();
            $stmt1->close();

            // 2. ADIM: Seçenek metinlerini güncelle
            $stmt2 = $conn->prepare("UPDATE secenekler SET secenek_metni = ? WHERE id = ?");
            foreach ($secenek_metinleri as $index => $metin) {
                $id = $secenek_idler[$index];
                $stmt2->bind_param("si", $metin, $id);
                $stmt2->execute();
            }
            $stmt2->close();
            
            // Tüm işlemler başarılıysa, onayla
            $conn->commit();

            $_SESSION['mesaj'] = "Soru (ID: $soru_id) başarıyla güncellendi.";
            header("Location: dashboard.php");
            exit();

        } catch (Exception $e) {
            // Herhangi bir hata olursa, tüm işlemleri geri al
            $conn->rollback();
            $hata_mesaji = "Bir hata oluştu, soru güncellenemedi: " . $e->getMessage();
        }
    }
}

// Eğer sayfa ilk defa yükleniyorsa (GET metodu), verileri çek ve formu göster
if (!$soru_id) {
    header("Location: dashboard.php");
    exit();
}

// Soruyu ve seçeneklerini veritabanından çek
$stmt_soru = $conn->prepare("SELECT * FROM sorular WHERE id = ?");
$stmt_soru->bind_param("i", $soru_id);
$stmt_soru->execute();
$result_soru = $stmt_soru->get_result();
if ($result_soru->num_rows === 0) {
    // Soru bulunamadıysa dashboard'a yönlendir
    header("Location: dashboard.php");
    exit();
}
$soru = $result_soru->fetch_assoc();
$stmt_soru->close();

$stmt_secenekler = $conn->prepare("SELECT * FROM secenekler WHERE soru_id = ?");
$stmt_secenekler->bind_param("i", $soru_id);
$stmt_secenekler->execute();
$result_secenekler = $stmt_secenekler->get_result();
$secenekler = $result_secenekler->fetch_all(MYSQLI_ASSOC);
$stmt_secenekler->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soru Düzenle</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .container { max-width: 800px; margin: 2em auto; background-color: #fff; padding: 2em; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 1.5em; }
        .form-group label { display: block; margin-bottom: .5em; font-weight: bold; }
        .form-group textarea, .form-group input[type="text"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .form-group textarea { min-height: 100px; }
        .options-group .option { display: flex; align-items: center; margin-bottom: .5em; }
        .options-group .option input[type="radio"] { margin-right: 10px; }
        .button-group { display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="button-group">
            <h2>Soru Düzenle (ID: <?php echo $soru_id; ?>)</h2>
            <a href="dashboard.php">&larr; Geri Dön</a>
        </div>
        <hr>

        <?php if (!empty($hata_mesaji)): ?>
            <p class="hata"><?php echo htmlspecialchars($hata_mesaji); ?></p>
        <?php endif; ?>

        <form method="POST" action="soru_duzenle.php">
            <input type="hidden" name="soru_id" value="<?php echo $soru['id']; ?>">

            <div class="form-group">
                <label for="soru_metni">Soru Metni:</label>
                <textarea id="soru_metni" name="soru_metni" required><?php echo htmlspecialchars($soru['soru_metni']); ?></textarea>
            </div>

            <div class="form-group options-group">
                <label>Seçenekler (Lütfen doğru olanı işaretleyin):</label>
                <?php foreach ($secenekler as $secenek): ?>
                <div class="option">
                    <input type="hidden" name="secenek_idler[]" value="<?php echo $secenek['id']; ?>">
                    <input type="radio" name="dogru_secenek_id" value="<?php echo $secenek['id']; ?>" <?php if ($secenek['id'] == $soru['dogru_secenek_id']) echo 'checked'; ?> required>
                    <input type="text" name="secenek_metinleri[]" value="<?php echo htmlspecialchars($secenek['secenek_metni']); ?>" required>
                </div>
                <?php endforeach; ?>
            </div>
            
            <button type="submit">Değişiklikleri Kaydet</button>
        </form>

    </div>
</body>
</html>
