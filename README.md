# Bilet Satın Alma - Docker Kurulumu

Bu proje, PHP, SQLite, HTMX ve mPDF kullanan bir bilet rezervasyon sistemidir ve Docker ile hızlıca çalıştırılabilir. 

## Yönetici Hesabı

```
E-posta: siteadmin@site.com
Şifre: aliuyanik
```

## Docker ile Kurulum

### 1. Projeyi klonlayın

```bash
git clone https://github.com/yourusername/bilet-satin-alma.git
cd bilet-satin-alma
```

### 2. Docker Compose ile çalıştırın

```bash
docker-compose up --build
```

* Sunucu `http://localhost:8080` adresinde çalışacaktır.

### 3. Veritabanı

* SQLite veritabanı konteyner içinde kullanılır. `pdo_sqlite` uzantısı Dockerfile içinde kuruludur.

## Notlar

* Oturum kullanımı için PHP sayfalarında `session_start()` ekli olmalıdır.
* Üretim ortamında hata raporlamayı kapatmak için `ini_set('display_errors', 0);` kullanılabilir.
