# FonePay Dynamic QR Laravel

This Laravel package allows merchants to generate and verify FonePay dynamic QR codes.

## Installation

You can install this package via Composer:

```sh
composer require shootkiran/fonepay-dynamic-qr-laravel

## Usage
```sh
FonePayQR::generate(100, 'Order123', 'Test Payment', 'PRN123');

```sh
$status = FonePayQR::verify('PRN123');
