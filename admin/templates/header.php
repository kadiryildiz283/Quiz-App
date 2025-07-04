<?php
// admin/templates/header.php
?>
<!DOCTYPE html>
<html lang="tr" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $sayfa_basligi ?? 'Admin Paneli'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 960px; }
        .table-hover tbody tr:hover { background-color: #f1f1f1; cursor: pointer; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Quiz Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Soru Yönetimi</a></li>
                    <li class="nav-item"><a class="nav-link" href="skorlar.php">Skor Tablosu</a></li>
                    <li class="nav-item"><a class="nav-link" href="kullanici_yonetimi.php">Kullanıcılar</a></li>
                    <li class="nav-item"><a class="nav-link" href="ayarlar.php">Ayarlar</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="logout.php">Çıkış Yap</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container">
