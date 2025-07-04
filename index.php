<?php
/**
 * index.php - YEREL BOOTSTRAP KULLANAN FİNAL SÜRÜM
 */
session_start();

$kullanici_izni = 0;
$quiz_durumu = 0;

if (isset($_SESSION['kullanici_id'])) {
    require 'includes/db_baglantisi.php';
    $kullanici_id = $_SESSION['kullanici_id'];
    
    $stmt = $conn->prepare("SELECT quiz_cozebilir, quiz_durumu FROM kullanicilar WHERE id = ?");
    $stmt->bind_param("i", $kullanici_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $kullanici = $result->fetch_assoc();
        $kullanici_izni = $kullanici['quiz_cozebilir'];
        $quiz_durumu = $kullanici['quiz_durumu'];
    }
    
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Dinamik Quiz Uygulaması</title>
    
    <link rel="stylesheet" href="css/bootstrap.min.css">
    
    <style>
        body {
            background-color: #e9ecef;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card text-center shadow-sm">
                    
                    <div class="card-header py-3">
                        <?php if (isset($_SESSION['kullanici_id'])): ?>
                            <span>Hoş Geldin, <strong><?php echo htmlspecialchars($_SESSION['kullanici_adi']); ?>!</strong></span>
                            <a href="cikis.php" class="btn btn-outline-secondary btn-sm float-end">Çıkış Yap</a>
                        <?php else: ?>
                            <a href="giris.php" class="btn btn-outline-primary">Giriş Yap</a>
                            <a href="kayit.php" class="btn btn-primary">Kayıt Ol</a>
                        <?php endif; ?>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <h1 class="card-title h2 mb-3">PHP Quiz Uygulaması</h1>
                        
                        <?php if (isset($_SESSION['kullanici_id'])): ?>
                            
                            <?php if ($quiz_durumu == 1): ?>
                                <div class="alert alert-danger mt-3">
                                    <h4 class="alert-heading">Dikkat!</h4>
                                    <p>Başladığınız bir quizi yarım bıraktınız. Sisteme tekrar erişebilmek için yöneticinin hesabınızı sıfırlaması gerekmektedir.</p>
                                </div>

                            <?php elseif ($kullanici_izni == 1): ?>
                                <p class="card-text text-muted">Bilgini sına, en yüksek skoru sen yap ve liderlik tablosunda yerini al!</p>
                                <a href="quiz.php" class="btn btn-success btn-lg mt-3 px-5">Quiz'e Başla!</a>

                            <?php else: ?>
                                <div class="alert alert-warning mt-3">
                                <h4 class="alert-heading">Quiz Tamamlandı!</h4>
                                <p>Quiz'i daha önce tamamladınız. Tekrar çözebilmek için bir yöneticinin onay vermesi gereklidir.</p>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                             <div class="alert alert-info mt-3">Quize başlamak için lütfen giriş yapın veya kayıt olun.</div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer text-muted">
                        Tüm Hakları Saklıdır &copy; <?php echo date('Y'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
