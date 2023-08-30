<?php

declare(strict_types=1);

namespace Mlevent\Tcmb;

class Currency
{
    public function __construct(
        public string  $currencyCode,
        public string  $currencyName,
        public string  $currencyAlias,
        public string  $forexBuying,
        public string  $forexSelling,
        public ?string $banknoteBuying  = null,
        public ?string $banknoteSelling = null,
        public ?string $crossRateUSD    = null,
        public ?string $crossRateOther  = null,
    ){}

    /**
     * toJson
     */
    public function toJson(): string {
        return json_encode($this, JSON_PRETTY_PRINT);
    }
}