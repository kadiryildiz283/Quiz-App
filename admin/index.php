<?php
// admin/index.php - MODERN TASARIM
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}
require '../includes/db_baglantisi.php';
$hata_mesaji = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ... (Bu kısımdaki PHP mantığı aynı kalıyor, kopyalayabilirsiniz veya aşağıdakini kullanabilirsiniz)
    $kullanici_adi = $_POST['kullanici_adi'];
    $sifre = $_POST['sifre'];
    if (empty($kullanici_adi) || empty($sifre)) {
        $hata_mesaji = "Kullanıcı adı ve şifre alanları boş bırakılamaz.";
    } else {
        $stmt = $conn->prepare("SELECT id, sifre FROM adminler WHERE kullanici_adi = ?");
        $stmt->bind_param("s", $kullanici_adi);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($sifre, $admin['sifre'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['kullanici_adi'] = $kullanici_adi;
                header("Location: dashboard.php");
                exit();
            }
        }
        $hata_mesaji = "Kullanıcı adı veya şifre hatalı!";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli Giriş</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .login-form {
            max-width: 400px;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

    <div class="login-form w-100">
        <div class="card shadow-sm">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <i class="bi bi-shield-lock-fill text-primary" style="font-size: 3rem;"></i>
                    <h3 class="mt-2">Admin Paneli Giriş</h3>
                </div>
                
                <?php if (!empty($hata_mesaji)): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($hata_mesaji); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <div class="form-floating">
                            <input type="text" class="form-control" id="kullanici_adi" name="kullanici_adi" placeholder="Kullanıcı Adı" required>
                            <label for="kullanici_adi">Kullanıcı Adı</label>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <div class="form-floating">
                            <input type="password" class="form-control" id="sifre" name="sifre" placeholder="Şifre" required>
                            <label for="sifre">Şifre</label>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Giriş Yap</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
