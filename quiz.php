<?php
// quiz.php - TÜM ÖZELLİKLER DAHİL EDİLMİŞ TAM SÜRÜM
session_start();

// --- 1. ADIM: GÜVENLİK KONTROLLERİ ---
if (!isset($_SESSION['kullanici_id'])) { header("Location: giris.php"); exit(); }
require 'includes/db_baglantisi.php';
$kullanici_id = $_SESSION['kullanici_id'];
$stmt_izin_check = $conn->prepare("SELECT quiz_cozebilir FROM kullanicilar WHERE id = ?");
$stmt_izin_check->bind_param("i", $kullanici_id);
$stmt_izin_check->execute();
$result_izin_check = $stmt_izin_check->get_result()->fetch_assoc();
if ($result_izin_check['quiz_cozebilir'] == 0) { header("Location: index.php"); exit(); }

// --- 2. ADIM: AYARLARI ÇEKME ---
$ayar_soru_suresi = $conn->query("SELECT ayar_degeri FROM ayarlar WHERE ayar_adi = 'soru_suresi'")->fetch_assoc()['ayar_degeri'] ?? 20;

// --- 3. ADIM: QUIZ MANTIĞI (SADECE İLK YÜKLEME) ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $result_sorular = $conn->query("SELECT id FROM sorular ORDER BY RAND() LIMIT 10");
    $soru_idler_dizisi = $result_sorular->fetch_all(MYSQLI_ASSOC);
    if (count($soru_idler_dizisi) === 0) { die("Hata: Veritabanında soru bulunmuyor."); }
    $_SESSION['soru_ids'] = array_column($soru_idler_dizisi, 'id');
    $_SESSION['mevcut_soru_index'] = 0;
    $_SESSION['skor'] = 0;
    $_SESSION['kullanici_cevaplari'] = []; 
} else {
    header("Location: quiz.php"); exit();
}

// --- 4. ADIM: İLK SORU BİLGİLERİNİ ÇEKME ---
$guncel_soru_id = $_SESSION['soru_ids'][0];
$soru = $conn->query("SELECT soru_metni FROM sorular WHERE id = $guncel_soru_id")->fetch_assoc();
$secenekler = $conn->query("SELECT id, secenek_metni FROM secenekler WHERE soru_id = $guncel_soru_id")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Quiz</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style> body { background-color: #e9ecef; } .list-group-item { transition: background-color 0.2s ease-in-out; } .list-group-item:hover { background-color: #f0f0f0; } .list-group-item input:checked + span { font-weight: bold; } </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span id="soru_sayac" class="fw-bold">Soru 1 / <?php echo count($_SESSION['soru_ids']); ?></span>
                            <div id="timer-container" class="fw-bold">Kalan Süre: <span id="timer" class="badge bg-primary rounded-pill fs-6"><?php echo htmlspecialchars($ayar_soru_suresi); ?></span></div>
                        </div>
                        <div class="progress mt-2" style="height: 10px;">
                            <div id="progress_bar_inner" class="progress-bar" role="progressbar" style="width: <?php echo (1 / count($_SESSION['soru_ids'])) * 100; ?>%;"></div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <h3 id="soru_metni" class="card-title text-center mb-4"><?php echo htmlspecialchars($soru['soru_metni']); ?></h3>
                        <form id="quiz_form">
                            <div id="secenekler_listesi" class="list-group">
                                <?php foreach($secenekler as $secenek): ?>
                                    <label class="list-group-item list-group-item-action">
                                        <input class="form-check-input me-2" type="radio" name="cevap" value="<?php echo $secenek['id']; ?>" required>
                                        <span><?php echo htmlspecialchars($secenek['secenek_metni']); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">Cevapla ve İlerle</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        let timerInterval;
        let timeLeft = <?php echo json_encode((int)$ayar_soru_suresi); ?>;

        function startTimer() { /* ... Bu fonksiyon aynı kalıyor ... */ }

        $('#secenekler_listesi').on('click', '.list-group-item', function() {
            $(this).find('input[type="radio"]').prop('checked', true);
        });

        $('#quiz_form').on('submit', function(event) {
            event.preventDefault(); 
            clearInterval(timerInterval); 

            let secilenCevap = $('input[name="cevap"]:checked').val();

            $.ajax({
                url: 'api_cevap_isle.php',
                type: 'POST',
                dataType: 'json',
                data: { cevap: secilenCevap },
                success: function(response) {
                    if (response.hata) { alert(response.hata); return; }
                    if (response.quiz_bitti) {
                        window.location.href = 'sonuc.php';
                    } else {
                        $('#soru_sayac').text('Soru ' + response.soru_index + ' / ' + response.toplam_soru);
                        $('#soru_metni').text(response.soru_metni);
                        let progressPercent = (response.soru_index / response.toplam_soru) * 100;
                        $('#progress_bar_inner').css('width', progressPercent + '%');
                        
                        // Seçenekleri yeniden oluştur
                        let seceneklerHtml = '';
                        $.each(response.secenekler, function(index, secenek) {
                            // ==========================================================
                            // ***** İŞTE DÜZELTİLEN KRİTİK SATIR BURASI *****
                            // ==========================================================
                            seceneklerHtml += '<label class="list-group-item list-group-item-action"><input class="form-check-input me-2" type="radio" name="cevap" value="' + secenek.id + '" required><span>' + secenek.secenek_metni + '</span></label>';
                        });
                        $('#secenekler_listesi').html(seceneklerHtml);
                        
                        startTimer();
                    }
                },
                error: function() {
                    alert('Sunucu ile iletişim kurulamadı. Lütfen tekrar deneyin.');
                }
            });
        });

        // startTimer fonksiyonunun tanımı (kısaltma amaçlı gizlendi, kodda mevcut)
        function startTimer() { let sure = timeLeft; const timerElement = $('#timer'); timerElement.text(sure).removeClass('bg-danger').addClass('bg-primary'); clearInterval(timerInterval); timerInterval = setInterval(function() { sure--; timerElement.text(sure); if (sure <= 5) { timerElement.removeClass('bg-primary').addClass('bg-danger'); } if (sure < 0) { clearInterval(timerInterval); $('#quiz_form').submit(); } }, 1000); }
        startTimer();
    });
    </script>
</body>
</html>
