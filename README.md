<h2 align="center">💸 TCMB Döviz Kurları</h2>
<p align="center">TCMB'ın yayınladığı döviz kuru bilgilerine günlük ya da geçmişe dönük ulaşabilir, para birimlerini dönüştürebilirsiniz.</p>
<p align="center">
<img src="https://img.shields.io/packagist/dependency-v/mlevent/fatura/php?style=plastic"/>
<img src="https://img.shields.io/packagist/v/mlevent/fatura?style=plastic"/>
<img src="https://img.shields.io/github/last-commit/mlevent/fatura?style=plastic"/>
<img src="https://img.shields.io/github/issues/mlevent/fatura?style=plastic"/>
<img src="https://img.shields.io/packagist/dt/mlevent/fatura?style=plastic"/>
<img src="https://img.shields.io/github/stars/mlevent/fatura?style=plastic"/>
<img src="https://img.shields.io/github/forks/mlevent/fatura?style=plastic"/>
</p>

### Kurulum

🛠️ Paketi composer ile projenize dahil edin;

```bash
composer require mlevent/tcmb
```

### Örnek Kullanım

```php
use Mlevent\Tcmb\Tcmb;

$tcmb = new Tcmb;

// Dolar kuruna ait detaylar
var_dump($tcmb->get('USD'));

// Bu kullanım da aynı sonucu verecektir
var_dump($tcmb->getUsd());
```

Bu örnek, aşağıdaki gibi bir `Currency` nesnesi döndürecektir;

```php
Mlevent\Tcmb\Currency Object
(
    [currencyCode]    => USD
    [currencyName]    => US DOLLAR
    [currencyAlias]   => ABD DOLARI
    [forexBuying]     => 27.0254
    [forexSelling]    => 27.0741
    [banknoteBuying]  => 27.0065
    [banknoteSelling] => 27.1147
    [crossRateUSD]    =>
    [crossRateOther]  =>
)
```

Nesne elemanlarına ulaşmak için;

```php
echo $tcmb->getUsd()->forexSelling; // 27.0741
```

### Geçmiş Tarihli Veriler

Tcmb'nin sağladığı geçmiş tarihli kur verilerine de ulaşabilirsiniz;

```php
$tcmb = (new Tcmb)->setDate('16/05/2022');
```

> Belirtilen tarih haftasonu veya resmi tatillere denk geliyorsa, kur bilgisi dönmeyecektir.

### Kur Dönüştürme

Para birimlerini dönüştürmek için;

```php
echo $tcmb->convert('USD', 'TRY'); // 27.0741
```

Aşağıdaki şu kullanım da aynı sonucu verecektir;

```php
echo $tcmb->convertUsdTry(10); // 270.741
```

### Cache

Performans iyileştirmesi için önbellek kullanmak isteyebilirsiniz. Aşağıdaki örnek [mlevent/file-cache](https://github.com/mlevent/file-cache) sınıfı kullanılmıştır. Farklı bir önbellekleme yapısı kullanıyorsanız aynı yolu takip edebilirsiniz;

```php
use Mlevent\FileCache\FileCache;
use Mlevent\Tcmb\Tcmb;

$tcmb = new Tcmb;

// Veriler 60 saniye boyunca diskte saklanacak
$data = (new FileCache)->refresh('exchange-rates', function () use ($tcmb) {
    return $tcmb->fetchExchangeRates();
}, 60);

// Veri içe aktarılıyor
$tcmb->setExchangeRates($data);

echo $tcmb->getUsd()->forexSelling; // 27.0741
```

### Ekstra

Kullanılabilecek diğer metodlar;

```php
/**
 * Döviz Listesi
 * @return array
 */
$tcmb->getCurrencies();

/**
 * Tüm Döviz Cinslerine Ait Kur Bilgileri
 * @return array
 */
$tcmb->getExchangeRates();

/**
 * TCMB Bülten Yayınlanma Tarihi
 * @return string
 */
$tcmb->getBulletinDate();

/**
 * TCMB Bülten Numarası
 * @return string
 */
$tcmb->getBulletinNumber();
```

### Para Birimleri

TCMB'ın verilerini yayınladığı para birimleri;

|     | Adı                    | Kodu |     |     | Adı                               | Kodu |
| :-: | :--------------------- | :--: | --- | :-: | :-------------------------------- | :--: |
| 🇺🇸  | ABD Doları             | USD  |     | 🇦🇿  | Azerbaycan Yeni Manata            | AZN  |
| 🇪🇺  | Euro                   | EUR  |     | 🇦🇪  | Birleşik Arap Emirlikleri Dirhemi | AED  |
| 🇬🇧  | İngiliz Sterlini       | GBP  |     | 🇩🇰  | Danimarka Kronu                   | DKK  |
| 🇯🇵  | Japon Yeni             | JPY  |     | 🇸🇪  | İsveç Kronu                       | SEK  |
| 🇷🇺  | Rus Rublesi            | RUB  |     | 🇰🇼  | Kuveyt Dinarı                     | KWD  |
| 🇸🇦  | Suudi Arabistan Riyali | SAR  |     | 🇳🇴  | Norveç Kronu                      | NOK  |
| 🇨🇭  | İsviçre Frangı         | CHF  |     | 🇷🇴  | Rumen Leyi                        | RON  |
| 🇦🇹  | Avustralya Doları      | AUD  |     | 🇮🇷  | İran Riyali                       | IRR  |
| 🇨🇦  | Kanada Doları          | CAD  |     | 🇵🇰  | Pakistan Rupisi                   | PKR  |
| 🇨🇳  | Çin Yuanı              | CNY  |     | 🇶🇦  | Katar Riyali                      | QAR  |
| 🇧🇬  | Bulgar Levası          | BGN  |     | 🇰🇷  | Güney Kore Wonu                   | KRW  |

### 📧İletişim

İletişim için ghergedan@gmail.com adresine e-posta gönderin.
