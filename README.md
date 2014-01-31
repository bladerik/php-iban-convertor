##Slovensky IBAN convertor
##========================

Pre ziskanie IBAN staci vediet kod banky a cislo uctu, prip. predcislie uctu.

Pouzitie triedy je primitivne

```php
<?php
require_once ("/path/to/iban-convertor/IbanConvertor.php");

$bankCode = "0200";
$accountNumber = "1234567890";
//predcislie uctu - napr v mBank 520600, vacsinou prazdne
$accountSuffix = "520600";

$convertor = new IbanConvertor();
$iban = $convertor->convert($bankCode, $accountNumber, $accountSuffix);
```

###INSTALACIA

pre spravnu funkcnost je potrebne po stiahnuti v adresari rozbalit PHP IBAN https://code.google.com/p/php-iban/downloads/list

pripadne odstranit IF na riadku 35 - tymto krokom ale odstrelite kontrolu formatu vygenerovaneho IBAN-u