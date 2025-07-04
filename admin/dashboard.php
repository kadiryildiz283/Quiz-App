<?php
// admin/dashboard.php
session_start();
require '../includes/db_baglantisi.php';
if (!isset($_SESSION['admin_id'])) { header("Location: index.php"); exit(); }

$result = $conn->query("SELECT id, soru_metni FROM sorular ORDER BY id DESC");

$sayfa_basligi = 'Soru Yönetimi'; // Header'da kullanılacak başlık
require 'templates/header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Soru Listesi</h2>
    <a href="soru_ekle.php" class="btn btn-success">Yeni Soru Ekle</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Soru Metni</th>
                    <th scope="col" class="text-end">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($soru = $result->fetch_assoc()): ?>
                        <tr>
                            <th scope="row"><?php echo $soru['id']; ?></th>
                            <td><?php echo htmlspecialchars(substr($soru['soru_metni'], 0, 90)); ?>...</td>
                            <td class="text-end">
                                <a href="soru_duzenle.php?id=<?php echo $soru['id']; ?>" class="btn btn-primary btn-sm">Düzenle</a>
                                <a href="soru_sil.php?id=<?php echo $soru['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bu soruyu silmek istediğinizden emin misiniz?');">Sil</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center p-4">Henüz hiç soru eklenmemiş.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
$conn->close();
require 'templates/footer.php'; 
?>
