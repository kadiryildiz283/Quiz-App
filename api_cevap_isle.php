<?php
// api_cevap_isle.php

session_start();
header('Content-Type: application/json'); // Bu dosyanın JSON döndüreceğini belirtiyoruz.

// Gerekli kontroller
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['kullanici_id'])) {
    // Geçersiz istek veya giriş yapılmamışsa hata döndür
    echo json_encode(['hata' => 'Geçersiz istek.']);
    exit();
}

require 'includes/db_baglantisi.php';

// --- CEVABI İŞLE ---
$kullanici_cevabi_id = isset($_POST['cevap']) ? (int)$_POST['cevap'] : 0;
$mevcut_soru_index = $_SESSION['mevcut_soru_index'];
$mevcut_soru_id = $_SESSION['soru_ids'][$mevcut_soru_index];
$_SESSION['kullanici_cevaplari'][$mevcut_soru_id] = $kullanici_cevabi_id; 

$stmt_dogru_cevap = $conn->prepare("SELECT dogru_secenek_id FROM sorular WHERE id = ?");
$stmt_dogru_cevap->bind_param("i", $mevcut_soru_id);
$stmt_dogru_cevap->execute();
$result = $stmt_dogru_cevap->get_result()->fetch_assoc();
$dogru_cevap_id = $result['dogru_secenek_id'];
$stmt_dogru_cevap->close();

if ($kullanici_cevabi_id === $dogru_cevap_id) {
    $_SESSION['skor']++;
}
$_SESSION['mevcut_soru_index']++;


// --- YANITI HAZIRLA ---
$response = [];

if ($_SESSION['mevcut_soru_index'] >= count($_SESSION['soru_ids'])) {
    // Quiz bitti
    $response['quiz_bitti'] = true;
} else {
    // Quiz devam ediyor, bir sonraki sorunun verilerini hazırla
    $yeni_soru_index = $_SESSION['mevcut_soru_index'];
    $yeni_soru_id = $_SESSION['soru_ids'][$yeni_soru_index];

    // Yeni sorunun metnini çek
    $stmt_soru = $conn->prepare("SELECT soru_metni FROM sorular WHERE id = ?");
    $stmt_soru->bind_param("i", $yeni_soru_id);
    $stmt_soru->execute();
    $soru = $stmt_soru->get_result()->fetch_assoc();
    $stmt_soru->close();

    // Yeni soruya ait seçenekleri çek
    $stmt_secenekler = $conn->prepare("SELECT id, secenek_metni FROM secenekler WHERE soru_id = ?");
    $stmt_secenekler->bind_param("i", $yeni_soru_id);
    $stmt_secenekler->execute();
    $secenekler = $stmt_secenekler->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_secenekler->close();
    
    $response = [
        'quiz_bitti' => false,
        'soru_index' => $yeni_soru_index + 1,
        'toplam_soru' => count($_SESSION['soru_ids']),
        'soru_metni' => $soru['soru_metni'],
        'secenekler' => $secenekler
    ];
}

$conn->close();
// Hazırlanan yanıtı JSON formatında JavaScript'e gönder
echo json_encode($response);
exit();
