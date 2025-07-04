<?php
// admin/skorlar.php - GÜNCELLENMİŞ KOD

session_start();
require '../includes/db_baglantisi.php';

// Güvenlik Kontrolü
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Kullanıcı adlarını da alabilmek için JOIN ile tabloları birleştiriyoruz.
$sql = "SELECT s.id, k.kullanici_adi, s.skor, s.toplam_soru, s.tarih 
        FROM skorlar s
        JOIN kullanicilar k ON s.kullanici_id = k.id
        ORDER BY s.skor DESC, s.tarih DESC";

$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skor Tablosu - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .container { max-width: 800px; margin: 2em auto; background-color: #fff; padding: 2em; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .button-group { display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>

    <div class="container">
        <div class="button-group">
            <h2>Oyuncu Skor Tablosu</h2>
            <a href="dashboard.php" style="text-decoration: none;">&larr; Panele Geri Dön</a>
        </div>
        <hr>

        <table class="table">
            <thead>
                <tr>
                    <th>Sıra</th>
                    <th>Kullanıcı Adı</th>
                    <th>Skor (Başarı Yüzdesi)</th>
                    <th>Tarih</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php $sira = 1; ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <?php
                            // ===============================================
                            // YENİ EKLENEN KISIM BAŞLANGICI
                            // ===============================================
                            
                            // Sıfıra bölünme hatasını önlemek için kontrol yapıyoruz.
                            $yuzde = 0;
                            if ($row['toplam_soru'] > 0) {
                                // Yüzdeyi hesapla ve yuvarla.
                                $yuzde = round(($row['skor'] / $row['toplam_soru']) * 100);
                            }
                            
                            // ===============================================
                            // YENİ EKLENEN KISIM SONU
                            // ===============================================
                        ?>
                        <tr>
                            <td><?php echo $sira++; ?></td>
                            <td><?php echo htmlspecialchars($row['kullanici_adi']); ?></td>
                            <td>
                                <strong><?php echo $row['skor']; ?> / <?php echo $row['toplam_soru']; ?></strong> 
                                <span style="color: #6c757d;">(%<?php echo $yuzde; ?>)</span>
                            </td>
                            <td><?php echo date('d.m.Y H:i', strtotime($row['tarih'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center;">Henüz kaydedilmiş bir skor bulunmuyor.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
    </div>

</body>
</html>
<?php
$conn->close();
?>
