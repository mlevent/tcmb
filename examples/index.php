<?php declare(strict_types=1); error_reporting(E_ALL); require dirname(__DIR__).'/vendor/autoload.php';

use Mlevent\Tcmb\Tcmb;

$tcmb = (new Tcmb);

echo $tcmb->convertUsdTry(10);

echo '<pre>';
print_r($tcmb->get('USD'));
echo '</pre>';

echo '<pre>';
print_r($tcmb->getExchangeRates());
echo '</pre>';

echo '<pre>';
print_r($tcmb->getCurrencies());
echo '</pre>';
