<?php
/**
 * kullanici_yonetimi.php - KULLANICI YÖNETİM PANELİ (FİNAL SÜRÜM)
 * Bu sayfa, tüm oyuncuları listeler, onların quiz durumlarını gösterir
 * ve adminin gerekli durumlarda kullanıcı izinlerini sıfırlamasına olanak tanır.
 */
session_start();

// Admin girişi yapılmamışsa, giriş sayfasına yönlendir
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

require '../includes/db_baglantisi.php';

// Veritabanından tüm kullanıcıları ve durumlarını çek
$result = $conn->query("SELECT id, kullanici_adi, email, quiz_cozebilir, quiz_durumu FROM kullanicilar ORDER BY kullanici_adi ASC");

// Bu değişken, header.php dosyasındaki <title> etiketini dinamik olarak ayarlar
$sayfa_basligi = 'Kullanıcı Yönetimi';

// Sayfanın başlangıç HTML'ini, CSS linklerini ve navigasyon menüsünü dahil et
require 'templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Kullanıcı Yönetimi</h2>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Kullanıcı Adı</th>
                        <th scope="col">E-posta Adresi</th>
                        <th scope="col" class="text-center">Quiz Durumu</th>
                        <th scope="col" class="text-center">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($user = $result->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($user['kullanici_adi']); ?></strong></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td class="text-center">
                                    <?php
                                        // Kullanıcının durumuna göre renkli etiketler (badge) göster
                                        if ($user['quiz_durumu'] == 1) {
                                            echo '<span class="badge bg-warning text-dark">Yarım Bıraktı</span>';
                                        } elseif ($user['quiz_cozebilir'] == 1) {
                                            echo '<span class="badge bg-success">Çözebilir</span>';
                                        } else {
                                            echo '<span class="badge bg-danger">Bitirdi</span>';
                                        }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                        // Sadece durumu "Çözebilir" olmayan kullanıcılara sıfırlama butonu göster
                                        if ($user['quiz_cozebilir'] == 0 || $user['quiz_durumu'] == 1): 
                                    ?>
                                        <a href="kullanici_durum_degistir.php?id=<?php echo $user['id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Bu kullanıcının quiz iznini sıfırlamak istediğinizden emin misiniz? Tekrar quiz çözebilecek.')">
                                            İzni Sıfırla
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center p-4">Sisteme kayıtlı hiç kullanıcı bulunmuyor.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Veritabanı bağlantısını kapat
$conn->close();
// Sayfanın bitiş HTML'ini ve script'lerini dahil et
require 'templates/footer.php';
?>
