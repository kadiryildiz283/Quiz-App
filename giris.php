<?php
// giris.php - MODERN TASARIM
session_start();
require 'includes/db_baglantisi.php';
if (isset($_SESSION['kullanici_id'])) {
    header("Location: index.php");
    exit();
}
$hata_mesaji = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ... (Bu kısımdaki PHP mantığı aynı kalıyor, kopyalayabilirsiniz veya aşağıdakini kullanabilirsiniz)
    $kullanici_adi = trim($_POST['kullanici_adi']);
    $sifre = $_POST['sifre'];
    if (empty($kullanici_adi) || empty($sifre)) {
        $hata_mesaji = "Kullanıcı adı ve şifre alanları boş bırakılamaz.";
    } else {
        $stmt = $conn->prepare("SELECT id, kullanici_adi, sifre FROM kullanicilar WHERE kullanici_adi = ?");
        $stmt->bind_param("s", $kullanici_adi);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $kullanici = $result->fetch_assoc();
            if (password_verify($sifre, $kullanici['sifre'])) {
                $_SESSION['kullanici_id'] = $kullanici['id'];
                $_SESSION['kullanici_adi'] = $kullanici['kullanici_adi'];
                header("Location: index.php");
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
    <title>Giriş Yap - Quiz Uygulaması</title>
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
                    <i class="bi bi-joystick text-success" style="font-size: 3rem;"></i>
                    <h3 class="mt-2">Oyuncu Girişi</h3>
                </div>
                
                <?php if (isset($_SESSION['mesaj'])): ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['mesaj']; ?>
                    </div>
                    <?php unset($_SESSION['mesaj']); ?>
                <?php endif; ?>

                <?php if (!empty($hata_mesaji)): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($hata_mesaji); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="giris.php">
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
                        <button type="submit" class="btn btn-success btn-lg">Giriş Yap</button>
                    </div>
                </form>
                <div class="text-center mt-3">
                    <a href="kayit.php" class="text-decoration-none">Hesabın yok mu? Hemen Kayıt Ol</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
