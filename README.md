
# FonePay Dynamic QR Laravel

This Laravel package allows merchants to generate and verify FonePay dynamic QR codes using Laravel Http.
## Installation

You can install this package via Composer:

```bash
  composer require shootkiran/fonepay-dynamic-qr-laravel
```
    
## Usage

```php
use ShootKiran\DynamicQrGeneratorFonepay\FonePayQR;

$qrcode = FonePayQR::generate(100, 'Order123', 'Test Payment', 'PRN123');

$status = FonePayQR::verify('PRN123');

```


## Authors

- [@shootkiran](https://www.github.com/shootkiran)


## License

[MIT](https://choosealicense.com/licenses/mit/)

