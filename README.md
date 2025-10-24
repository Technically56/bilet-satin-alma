# Bilet Satın Alma - Docker Kurulumu

Bu proje, PHP, SQLite, HTMX ve mPDF kullanan bir bilet rezervasyon sistemidir ve Docker ile hızlıca çalıştırılabilir. 

## Yönetici Hesabı

```
/admin/login.php dizini ile giriş yapılacaktır. Firma admini için aynı girişten /admin/company/dashboard.php dizinine girilecektir.


E-posta: siteadmin@site.com
Rol:admin
Şifre: aliuyanik
```

## Docker ile Kurulum

### 1. Projeyi klonlayın

```bash
git clone git@github.com:Technically56/bilet-satin-alma.git
cd bilet-satin-alma
```

### 2. Docker Compose ile çalıştırın

```bash
docker-compose up --build
```

* Sunucu `http://localhost:8080` adresinde çalışacaktır.

### 3. Veritabanı

* SQLite veritabanı konteyner içinde kullanılır. `pdo_sqlite` uzantısı Dockerfile içinde kuruludur.
* Veritabanı modeli /database dizinindeki schema.sql dosyasında mevcuttur. 

## Notlar

* Oturum kullanımı için PHP sayfalarında `session_start()` ekli olmalıdır.
* Üretim ortamında hata raporlamayı kapatmak için `ini_set('display_errors', 0);` kullanılabilir.
