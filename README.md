# PHP Dinamik Quiz Uygulaması

Bu proje, geleneksel (saf) PHP, MySQL ve modern frontend teknolojileri kullanılarak geliştirilmiş, tam fonksiyonlu ve dinamik bir quiz web uygulamasıdır. Kullanıcıların kayıt olup test çözebildiği, adminlerin ise soru havuzunu, kullanıcıları ve genel ayarları yönetebildiği kapsamlı bir platformdur.

 *(Not: Bu linki kendi ekran görüntünüzle güncelleyin)*

-----

## ✨ Özellikler

### 👤 Kullanıcı Arayüzü

  - **Modern ve Mobil Uyumlu Tasarım:** Bootstrap 5 ile geliştirilmiş, tüm cihazlarda harika görünen arayüz.
  - **Kullanıcı Kayıt ve Giriş Sistemi:** Oyuncular kendi hesaplarını oluşturabilir ve giriş yapabilir.
  - **Dinamik Soru Havuzu:** Sorular veritabanından rastgele çekilir, her quiz deneyimi farklıdır.
  - **Akıcı Soru Geçişleri (AJAX):** Sorular arasında sayfa yenilenmeden, anında geçiş yapılır.
  - **Soru Başına Zamanlayıcı:** Her soru için admin panelinden ayarlanabilen bir geri sayım sayacı bulunur. Süre dolduğunda otomatik olarak sonraki soruya geçer.
  - **Tek Seferlik Çözme Hakkı:** Bir quizi başlatan kullanıcı, bitirene kadar yeni bir quize başlayamaz.
      - **Yarım Bırakma Tespiti:** Quizi yarım bırakan kullanıcı, admin sıfırlayana kadar sisteme kilitlenir.
  - **Detaylı Sonuç Ekranı:** Quiz sonunda başarı yüzdesi, doğru/yanlış sayısı ve tüm soruların doğru cevapları gösterilir.

### ⚙️ Admin Paneli

  - **Güvenli Giriş:** Adminler için ayrı ve güvenli bir giriş paneli.
  - **Soru Yönetimi (CRUD):** Adminler kolayca yeni soru ekleyebilir, mevcut soruları ve seçeneklerini düzenleyebilir veya silebilir.
  - **Kullanıcı Yönetimi:** Tüm kayıtlı oyuncuları listeleyebilir, quiz durumlarını (Çözebilir, Bitirdi, Yarım Bıraktı) görebilir ve kilitlenen kullanıcıların test çözme iznini sıfırlayabilir.
  - **Skor Tablosu:** Tüm oyuncuların aldıkları skorları, başarı yüzdelerini ve tarihlerini içeren genel bir liderlik tablosu.
  - **Genel Ayarlar:** Soru başına zamanlayıcı süresi gibi uygulama genelindeki ayarlar panelden kolayca değiştirilebilir.

-----

## 🛠️ Kullanılan Teknolojiler

  * **Backend:** PHP 8+
  * **Veritabanı:** MySQL / MariaDB
  * **Frontend:**
      * HTML5
      * CSS3
      * Bootstrap 5
      * JavaScript (ES6)
      * jQuery (AJAX işlemleri için)
      * AJAX

-----

## 🚀 Kurulum

Bu projeyi kendi yerel sunucunuzda çalıştırmak için aşağıdaki adımları izleyin.

### Gereksinimler

  - PHP 7.4 veya üstü
  - MySQL veya MariaDB veritabanı
  - XAMPP, WAMP veya MAMP gibi bir yerel sunucu ortamı (Windows için), ya da php -S [port] (Genel yöntem)

### Adım Adım Kurulum

1.  **Projeyi Klonlayın veya İndirin:**

    ```bash
    git clone https://github.com/kadiryildiz283/Quiz-App/
    cd Quiz-App
    ```

2.  **Veritabanını Oluşturun:**

      - `phpMyAdmin` veya benzeri bir araç kullanarak `quiz_uygulamasi` adında yeni bir veritabanı oluşturun. Karşılaştırma (collation) ayarını `utf8mb4_turkish_ci` olarak seçmeniz tavsiye edilir.

3.  **SQL Dosyasını İçeri Aktarın (Import):**

      - Aşağıdaki tüm SQL kodunu kopyalayıp bir `.sql` dosyasına kaydedin veya doğrudan `phpMyAdmin`'deki SQL sekmesine yapıştırıp çalıştırın. Bu komutlar, gerekli tüm tabloları oluşturacak ve varsayılan admin kullanıcısını ekleyecektir.

    \<details\>
    \<summary\>\<strong\>Veritabanı Kurulum SQL Kodunu Göster\</strong\>\</summary\>

    ```sql
    -- Tablolar
    CREATE TABLE `adminler` ( `id` int(11) NOT NULL AUTO_INCREMENT, `kullanici_adi` varchar(50) NOT NULL, `sifre` varchar(255) NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `kullanici_adi` (`kullanici_adi`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    CREATE TABLE `ayarlar` ( `ayar_adi` varchar(50) NOT NULL, `ayar_degeri` varchar(255) NOT NULL, PRIMARY KEY (`ayar_adi`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    CREATE TABLE `kullanicilar` ( `id` int(11) NOT NULL AUTO_INCREMENT, `kullanici_adi` varchar(50) NOT NULL, `email` varchar(100) NOT NULL, `sifre` varchar(255) NOT NULL, `quiz_cozebilir` tinyint(1) NOT NULL DEFAULT 1, `quiz_durumu` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: Bosta, 1: Quiz Icinde', `kayit_tarihi` timestamp NOT NULL DEFAULT current_timestamp(), PRIMARY KEY (`id`), UNIQUE KEY `kullanici_adi` (`kullanici_adi`), UNIQUE KEY `email` (`email`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    CREATE TABLE `sorular` ( `id` int(11) NOT NULL AUTO_INCREMENT, `soru_metni` text NOT NULL, `dogru_secenek_id` int(11) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    CREATE TABLE `secenekler` ( `id` int(11) NOT NULL AUTO_INCREMENT, `soru_id` int(11) NOT NULL, `secenek_metni` varchar(255) NOT NULL, PRIMARY KEY (`id`), KEY `soru_id` (`soru_id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    CREATE TABLE `skorlar` ( `id` int(11) NOT NULL AUTO_INCREMENT, `kullanici_id` int(11) NOT NULL, `skor` int(11) NOT NULL, `toplam_soru` int(11) NOT NULL, `tarih` timestamp NOT NULL DEFAULT current_timestamp(), PRIMARY KEY (`id`), KEY `kullanici_id` (`kullanici_id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Varsayılan Veriler
    INSERT INTO `adminler` (`kullanici_adi`, `sifre`) VALUES ('admin', '$2y$10$9xVf3h0n7k/2s.Y.Q8.eW.RABp7f7g/Pz8.C5.aE2.qE8.b.rU6.q');
    INSERT INTO `ayarlar` (`ayar_adi`, `ayar_degeri`) VALUES ('soru_suresi', '20');

    -- İlişkiler (Foreign Keys)
    ALTER TABLE `secenekler` ADD CONSTRAINT `secenekler_ibfk_1` FOREIGN KEY (`soru_id`) REFERENCES `sorular` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
    ALTER TABLE `sorular` ADD CONSTRAINT `sorular_ibfk_1` FOREIGN KEY (`dogru_secenek_id`) REFERENCES `secenekler` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
    ALTER TABLE `skorlar` ADD CONSTRAINT `skorlar_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE;
    ```

    \</details\>

4.  **Veritabanı Bağlantısını Ayarlayın:**

      - Proje içindeki `includes/db_baglantisi.php` dosyasını açın.
      - Eğer varsayılan XAMPP ayarlarından farklı bir kullanıcı adı veya şifre kullanıyorsanız, bu dosyadaki değişkenleri kendi ayarlarınızla güncelleyin.
        ```php
        $sunucu = "localhost";
        $kullanici = "root";
        $sifre = ""; 
        $veritabani = "quiz_uygulamasi";
        ```

5.  **Çalıştırın:**

      - Proje klasörünü yerel sunucunuzun `htdocs` veya `www` dizinine koyun.
      - Tarayıcınızdan `http://localhost/PROJE_KLASOR_ADI/` adresine gidin.

-----

## 🚀 Kullanım

  - **Admin Girişi:** `http://localhost/PROJE_KLASOR_ADI/admin/` adresine gidin.

      - **Kullanıcı Adı:** `admin`
      - **Şifre:** `123456`

  - **Oyuncu Girişi:** Ana sayfadan "Kayıt Ol" butonuna tıklayarak yeni bir hesap oluşturun ve ardından giriş yapın.

-----

## 🤝 Katkıda Bulunma

Katkılarınız projeyi daha da iyi hale getirecektir\! Lütfen fork edip pull request göndermekten çekinmeyin. Hata bildirimleri ve özellik istekleri için "Issues" bölümünü kullanabilirsiniz.

1.  Projeyi Fork'layın.
2.  Yeni bir özellik dalı oluşturun (`git checkout -b ozellik/yeni-bir-ozellik`).
3.  Değişikliklerinizi Commit'leyin (`git commit -m 'Yeni bir özellik eklendi'`).
4.  Dalınızı Push'layın (`git push origin ozellik/yeni-bir-ozellik`).
5.  Bir Pull Request açın.

-----

## 📄 Lisans

Bu proje MIT Lisansı ile lisanslanmıştır. Detaylar için `LICENSE` dosyasına bakınız.

-----

## 📬 İletişim

kadir yıldız - dev@kadiryildiz.com.tr
