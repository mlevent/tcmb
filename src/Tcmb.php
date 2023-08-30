<?php

declare(strict_types=1);

namespace Mlevent\Tcmb;

use DateTime;
use DateTimeZone;
use Mlevent\Tcmb\Exceptions\TcmbException;

class Tcmb
{
    private string $bulletinDate   = '';
    private string $bulletinNumber = '';
    private array  $exchangeRates  = [];

    /**
     * __construct
     */
    public function __construct(
        private DateTime $dateTime = new DateTime,
        private string   $timeZone = 'Europe/Istanbul',
    ) { 
        $this->dateTime->setTimeZone(new DateTimeZone($this->timeZone));
    }

    /**
     * getExchangeRates
     */
    public function getExchangeRates() : array {
        if (!sizeof($this->exchangeRates)) {
            $this->setExchangeRates($this->fetchExchangeRates());
        }
        return $this->exchangeRates;
    }

    /**
     * getCurrencies
     */
    public function getCurrencies() : array {
        return array_combine(
            array_column($this->getExchangeRates(), 'currencyCode'),
            array_column($this->getExchangeRates(), 'currencyAlias')
        );
    }

    /**
     * getBulletinDate
     */
    public function getBulletinDate() : string {
        return $this->bulletinDate;
    }

    /**
     * getBulletinNumber
     */
    public function getBulletinNumber() : string {
        return $this->bulletinNumber;
    }

    /**
     * setExchangeRates
     */
    public function setExchangeRates(string $data) : self {
        
        if (!$data = json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOWARNING | LIBXML_NOERROR)), true)) {
            throw new TcmbException('Xml data cannot be read properly.');
        }
        
        $this->bulletinDate   = $data['@attributes']['Tarih'];
        $this->bulletinNumber = $data['@attributes']['Bulten_No'];

        foreach ($data['Currency'] as &$currency) {
            foreach ($currency as $key => $value) {
                $currency[$key] = $key == '@attributes' || !is_array($value) ? $value : null;
            }
        }

        $this->exchangeRates['TRY'] = new Currency(
            currencyCode    : 'TRY',
            currencyName    : 'TURKISH LIRA',
            currencyAlias   : 'TÜRK LİRASI',
            forexBuying     : '1',
            forexSelling    : '1',
            banknoteBuying  : '1',
            banknoteSelling : '1',
        );

        foreach ($data['Currency'] as $currency) {
            $this->exchangeRates[$currency['@attributes']['CurrencyCode']] = new Currency(
                currencyCode    : $currency['@attributes']['CurrencyCode'],
                currencyName    : $currency['CurrencyName'],
                currencyAlias   : $currency['Isim'],
                forexBuying     : $currency['ForexBuying'],
                forexSelling    : $currency['ForexSelling'],
                banknoteBuying  : $currency['BanknoteBuying'],
                banknoteSelling : $currency['BanknoteSelling'],
                crossRateUSD    : $currency['CrossRateUSD'],
                crossRateOther  : $currency['CrossRateOther'],
            );
        }
        return $this;
    }

    /**
     * fetchExchangeRates
     */
    public function fetchExchangeRates() : string { 
        $requestUrl = !$this->isOutOfDate()
            ? 'https://www.tcmb.gov.tr/kurlar/today.xml'
            : "https://www.tcmb.gov.tr/kurlar/{$this->dateTime->format('Ym')}/{$this->dateTime->format('dmY')}.xml";
        return $this->request($requestUrl);
    }

    /**
     * request
     */
    private function request(string $url) : string {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_REFERER,        $url);
        curl_setopt($ch, CURLOPT_USERAGENT,      $_SERVER['HTTP_USER_AGENT']);
        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            throw new TcmbException('Request to server failed.');
        }
        return $response;
    }

    /**
     * setDate
     * !!! Tarih haftasonuna denk geliyorsa, bir önceki Cuma olacak şekilde değiştirilir !!!
     */
    public function setDate(string $date) : self {
        $this->dateTime = DateTime::createFromFormat('d/m/Y', $date, new DateTimeZone($this->timeZone));
        if ($this->dateTime->format('N') > 5 && $this->isOutOfDate()) {
            $this->dateTime->modify('Last Friday');
        }
        return $this;
    }

    /**
     * isOutOfDate
     */
    public function isOutOfDate() : bool {
        return $this->dateTime->diff((new DateTime)->setTimeZone(new DateTimeZone($this->timeZone)))->format('%r%a') > 0;
    }

    /**
     * get
     */
    public function get(string $currency) : Currency {
        $exchangeRates = $this->getExchangeRates();
        if (isset($exchangeRates[$currency])) {
            return $exchangeRates[$currency]; 
        }
        throw new TcmbException("Currency not found: {$currency}");
    }

    /**
     * convert
     */
    public function convert($from, $to, $amount = 1, $slot = 'forexSelling') : float {
        if (in_array($slot, ['forexBuying', 'forexSelling'])) {
            return round(($this->get($from)->{$slot} / $this->get($to)->{$slot}) * $amount, 4);
        }
        throw new TcmbException("Currency cannot be converted: {$slot}");
    }

    /**
     * __call
     */
    public function __call(string $name, $arguments) : Currency|float {
        $camelCaseSplit = preg_split('/(?<=\\w)(?=[A-Z])/', $name);
        if (str_starts_with($name, 'get')) {
            if (sizeof($camelCaseSplit) === 2) {
                return $this->get(strtoupper($camelCaseSplit[1]));
            }
        }
        if (str_starts_with($name, 'convert')) {
            if (sizeof($camelCaseSplit) === 3) {
                return $this->convert(strtoupper($camelCaseSplit[1]), strtoupper($camelCaseSplit[2]), ...$arguments);
            }
        }
        throw new TcmbException("There is no such method: {$name}");
    }
}