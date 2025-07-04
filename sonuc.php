<?php
// sonuc.php - CEVAP ANALİZİ EKLENMİŞ FİNAL SÜRÜM
session_start();

// Güvenlik kontrolleri
if (!isset($_SESSION['skor']) || !isset($_SESSION['soru_ids']) || !isset($_SESSION['kullanici_cevaplari'])) {
    header("Location: index.php");
    exit();
}

require 'includes/db_baglantisi.php';

// Session'dan quiz verilerini alalım
$skor = $_SESSION['skor'];
$soru_idler = $_SESSION['soru_ids'];
$kullanici_cevaplari = $_SESSION['kullanici_cevaplari'];
$toplam_soru = count($soru_idler);

// Başarı yüzdesini hesapla
$basari_yuzdesi = ($toplam_soru > 0) ? round(($skor / $toplam_soru) * 100) : 0;

// --- VERİTABANINDAN TÜM SORU VE SEÇENEK BİLGİLERİNİ ÇEKME ---

// Veritabanında toplu sorgu yapmak için ID listesini string'e çevir
$soru_idler_string = implode(',', array_map('intval', $soru_idler));
$quiz_data = [];

if (!empty($soru_idler_string)) {
    // 1. Tüm soruları tek sorguda çek
    $sorular_result = $conn->query("SELECT id, soru_metni, dogru_secenek_id FROM sorular WHERE id IN ($soru_idler_string)");
    while ($soru = $sorular_result->fetch_assoc()) {
        $quiz_data[$soru['id']] = [
            'soru_metni' => $soru['soru_metni'],
            'dogru_secenek_id' => $soru['dogru_secenek_id'],
            'kullanici_cevabi_id' => $kullanici_cevaplari[$soru['id']] ?? 0,
            'secenekler' => []
        ];
    }

    // 2. Tüm seçenekleri tek sorguda çek
    $secenekler_result = $conn->query("SELECT id, soru_id, secenek_metni FROM secenekler WHERE soru_id IN ($soru_idler_string)");
    while ($secenek = $secenekler_result->fetch_assoc()) {
        // Her seçeneği ait olduğu sorunun altına ekle
        $quiz_data[$secenek['soru_id']]['secenekler'][] = $secenek;
    }
}

// Skoru veritabanına kaydetme ve durumu güncelleme
if (isset($_SESSION['kullanici_id'])) {
    $kullanici_id = $_SESSION['kullanici_id'];
    $stmt_skor = $conn->prepare("INSERT INTO skorlar (kullanici_id, skor, toplam_soru) VALUES (?, ?, ?)");
    $stmt_skor->bind_param("iii", $kullanici_id, $skor, $toplam_soru);
    $stmt_skor->execute();
    $stmt_skor->close();

    $stmt_izin = $conn->prepare("UPDATE kullanicilar SET quiz_cozebilir = 0, quiz_durumu = 0 WHERE id = ?");
    $stmt_izin->bind_param("i", $kullanici_id);
    $stmt_izin->execute();
    $stmt_izin->close();
}
$conn->close();

// Sayfa içeriği gösterildikten sonra session'u yok et
session_destroy();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Quiz Sonucu ve Analizi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style> body { background-color: #f4f4f9; } </style>
</head>
<body>
    <div class="container my-5">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm text-center">
                <div class="card-header">
                    <h2>Quiz Sonucu</h2>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Tebrikler, Quizi Tamamladınız!</h5>
                    <p class="display-4 fw-bold <?php echo ($basari_yuzdesi >= 50) ? 'text-success' : 'text-danger'; ?>">
                        %<?php echo $basari_yuzdesi; ?>
                    </p>
                    <p class="lead">
                        Toplam <strong><?php echo $toplam_soru; ?></strong> sorudan <strong><?php echo $skor; ?></strong> tanesini doğru bildiniz.
                    </p>
                    <a href="index.php" class="btn btn-primary btn-lg mt-3">Tekrar Oyna</a>
                </div>
            </div>

            <h3 class="mt-5 mb-4 text-center">Cevapların İncelenmesi</h3>

            <?php foreach ($soru_idler as $soru_id): if (!isset($quiz_data[$soru_id])) continue; $soru_detay = $quiz_data[$soru_id]; ?>
                <div class="card mb-3">
                    <div class="card-header fw-bold">
                        <?php echo htmlspecialchars($soru_detay['soru_metni']); ?>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($soru_detay['secenekler'] as $secenek): 
                            $is_correct = ($secenek['id'] == $soru_detay['dogru_secenek_id']);
                            $is_user_answer = ($secenek['id'] == $soru_detay['kullanici_cevabi_id']);
                            $li_class = '';
                            $icon = '';

                            if ($is_correct) {
                                $li_class = 'list-group-item-success';
                                $icon = '<i class="bi bi-check-circle-fill text-success float-end"></i>';
                            } elseif ($is_user_answer && !$is_correct) {
                                $li_class = 'list-group-item-danger';
                                $icon = '<i class="bi bi-x-circle-fill text-danger float-end"></i>';
                            }
                        ?>
                            <li class="list-group-item <?php echo $li_class; ?>">
                                <?php echo htmlspecialchars($secenek['secenek_metni']); ?>
                                <?php echo $icon; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
