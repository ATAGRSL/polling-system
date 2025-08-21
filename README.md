# Akıllı Anket Sistemi

Modern, güvenli ve özellik dolu PHP tabanlı anket sistemi. Kullanıcıların anketlere katılmasını ve sonuçları görüntülemesini sağlayan gelişmiş bir platform.

## 🌟 Özellikler

### 📊 Anket Yönetimi
- Çoklu seçenekli anketler
- Gerçek zamanlı sonuç görüntüleme
- IP tabanlı çoklu oy önleme
- Kolay anket oluşturma ve düzenleme

### 🔒 Güvenlik
- IP log sistemi ile oy sahteciliği önleme
- XSS koruması
- Güvenli dosya işlemleri
- Timestamp tabanlı kontrol

### 📈 Sonuç Takibi
- Anlık sonuç güncellemeleri
- Grafiksel sonuç gösterimi
- Detaylı istatistikler
- Admin paneli ile yönetim

### 💾 Veri Yönetimi
- Dosya tabanlı veri saklama
- Kolay yedekleme ve taşıma
- Structured data format
- Log sistemi

## 🚀 Kurulum ve Çalıştırma

### Gereksinimler
- PHP 7.4+
- Web sunucusu (Apache/Nginx)
- Composer

### Kurulum Adımları
1. Projeyi indirin veya klonlayın:
   ```bash
   git clone https://github.com/ATAGRSL/polling-system.git
   cd polling-system
   ```

2. Bağımlılıkları yükleyin:
   ```bash
   composer install
   ```

3. Web sunucusunu başlatın:
   ```bash
   composer run serve
   ```

4. Tarayıcınızda açın:
   ```
   http://localhost:8000
   ```

## 💻 Kullanım

### Ana Sayfa
- Mevcut anketi görüntüleme
- Oy verme işlemi
- Anlık sonuçları görme

### Admin Paneli
- Anket sonuçlarını detaylı görüntüleme
- IP loglarını kontrol etme
- Sistem yönetimi

## 🏗️ Teknik Detaylar

### Kullanılan Teknolojiler
- **PHP 7.4+** - Backend geliştirme
- **Composer** - Bağımlılık yönetimi
- **HTML5/CSS3** - Frontend arayüz
- **JavaScript** - Dinamik özellikler

### Dosya Yapısı
```
polling-system/
├── public/          # Web erişilebilir dosyalar
├── src/            # PHP kaynak kodları
├── config/         # Yapılandırma dosyaları
├── data/           # Veri dosyaları
│   ├── anket.txt   # Anket sorusu ve seçenekler
│   ├── oylar.txt   # Oy sayıları
│   └── ip_log.txt  # IP logları
├── logs/           # Sistem logları
├── composer.json   # Proje bağımlılıkları
└── README.md       # Proje dokümantasyonu
```

### Veri Formatları

#### anket.txt
```
En sevdiğiniz kodlama dili hangisi?
PHP
JavaScript
Java
Python
```

#### oylar.txt
```
0
0
2
1
```

#### ip_log.txt
```
192.168.1.100|0|1755811872
192.168.1.200|1|1755811872
```

## 🔧 Geliştirme

### Yeni Özellik Eklemek
1. `src/` dizininde yeni PHP sınıfı oluşturun
2. `config/config.php` dosyasında yapılandırma ekleyin
3. `public/` dizininde gerekli web dosyalarını ekleyin

### Test Etme
```bash
composer run test
```

### Kod Kalitesi
```bash
composer run lint
```

## 🛡️ Güvenlik

- IP tabanlı çoklu oy önleme
- Dosya işlemleri için güvenli fonksiyonlar
- Input validation ve sanitization
- Log sistemi ile izlenebilirlik

## 📊 Mevcut Anket

**Soru:** En sevdiğiniz kodlama dili hangisi?

**Seçenekler:**
- PHP (0 oy)
- JavaScript (0 oy)
- Java (2 oy)
- Python (1 oy)

## 🤝 Katkıda Bulunma

1. Fork edin
2. Feature branch oluşturun (`git checkout -b feature/yeni-ozellik`)
3. Commit edin (`git commit -am 'Yeni özellik eklendi'`)
4. Push edin (`git push origin feature/yeni-ozellik`)
5. Pull Request oluşturun

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

## 👨‍💻 Geliştirici

**Ata Gürsel**
- **GitHub**: https://github.com/ATAGRSL
- **LinkedIn**: https://www.linkedin.com/in/atagursel/

---

⭐ **Bu projeyi beğendiyseniz yıldız vermeyi unutmayın!**
