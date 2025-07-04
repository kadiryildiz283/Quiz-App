<?php
// admin/soru_ekle.php

session_start();
require '../includes/db_baglantisi.php';

// Güvenlik Kontrolü
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$hata_mesaji = '';
$basari_mesaji = '';

// Form gönderildi mi kontrolü
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Formdan gelen verileri al
    $soru_metni = trim($_POST['soru_metni']);
    $secenekler = $_POST['secenekler']; // Bu bir dizi olacak
    $dogru_cevap_index = isset($_POST['dogru_cevap_index']) ? $_POST['dogru_cevap_index'] : -1;

    // Basit doğrulama
    if (empty($soru_metni) || count($secenekler) < 2 || min(array_map('strlen', $secenekler)) == 0 || $dogru_cevap_index == -1) {
        $hata_mesaji = "Lütfen soru metnini, en az iki seçeneği doldurun ve doğru cevabı işaretleyin.";
    } else {
        // Veritabanı işlemlerini güvenli bir şekilde yapmak için TRANSACTION kullanıyoruz.
        // Bu sayede işlemlerden biri başarısız olursa, tümü geri alınır.
        $conn->begin_transaction();

        try {
            // 1. ADIM: Soruyu 'sorular' tablosuna ekle
            $stmt1 = $conn->prepare("INSERT INTO sorular (soru_metni) VALUES (?)");
            $stmt1->bind_param("s", $soru_metni);
            $stmt1->execute();
            $yeni_soru_id = $conn->insert_id; // Eklenen sorunun ID'sini al
            $stmt1->close();

            // 2. ADIM: Seçenekleri 'secenekler' tablosuna ekle
            $dogru_secenek_id_veritabani = 0;
            $stmt2 = $conn->prepare("INSERT INTO secenekler (soru_id, secenek_metni) VALUES (?, ?)");
            
            foreach ($secenekler as $index => $secenek_metni) {
                $stmt2->bind_param("is", $yeni_soru_id, $secenek_metni);
                $stmt2->execute();
                
                // Eğer bu seçenek doğru cevap olarak işaretlendiyse, ID'sini sakla
                if ($index == $dogru_cevap_index) {
                    $dogru_secenek_id_veritabani = $conn->insert_id;
                }
            }
            $stmt2->close();

            // 3. ADIM: 'sorular' tablosunu doğru cevap ID'si ile güncelle
            $stmt3 = $conn->prepare("UPDATE sorular SET dogru_secenek_id = ? WHERE id = ?");
            $stmt3->bind_param("ii", $dogru_secenek_id_veritabani, $yeni_soru_id);
            $stmt3->execute();
            $stmt3->close();

            // Tüm işlemler başarılıysa, onayla
            $conn->commit();

            // Başarı mesajını session'a kaydet ve dashboard'a yönlendir
            $_SESSION['mesaj'] = "Soru başarıyla eklendi!";
            header("Location: dashboard.php");
            exit();

        } catch (Exception $e) {
            // Herhangi bir hata olursa, tüm işlemleri geri al
            $conn->rollback();
            $hata_mesaji = "Bir hata oluştu, soru eklenemedi: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Soru Ekle</title>
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
        .basari { color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: .75rem 1.25rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: .25rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="button-group">
            <h2>Yeni Soru Ekle</h2>
            <a href="dashboard.php">&larr; Geri Dön</a>
        </div>
        <hr>

        <?php if (!empty($hata_mesaji)): ?>
            <p class="hata"><?php echo htmlspecialchars($hata_mesaji); ?></p>
        <?php endif; ?>

        <form method="POST" action="soru_ekle.php">
            <div class="form-group">
                <label for="soru_metni">Soru Metni:</label>
                <textarea id="soru_metni" name="soru_metni" required></textarea>
            </div>

            <div class="form-group options-group">
                <label>Seçenekler (Lütfen doğru olanı işaretleyin):</label>
                <?php for ($i = 0; $i < 4; $i++): ?>
                <div class="option">
                    <input type="radio" name="dogru_cevap_index" value="<?php echo $i; ?>" required>
                    <input type="text" name="secenekler[]" placeholder="Seçenek <?php echo $i + 1; ?>" required>
                </div>
                <?php endfor; ?>
            </div>
            
            <button type="submit">Soruyu Kaydet</button>
        </form>

    </div>
</body>
</html>
