<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;

class ExternalService
{
    protected $url = 'https://economia.awesomeapi.com.br/json/';

    /**
     * @param string $currency
     * @param string $currencyIn
     * 
     * @return array
     */
    public function getQuote(string $currency, string $currencyIn = '')
    {
        return json_decode(
            file_get_contents($this->url . "{$currency}-{$currencyIn}"),
            true
        );
    }
}