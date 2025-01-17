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
        /**
         * @todo: Fazer consulta na api externa e atribuir a variavel $expected
         * obs: Por enquanto esta chumbado para não exceder a quantidade de requests da api externa
         */
        $json = '[{"code":"USD","codein":"BRL","name":"Dólar Americano/Real Brasileiro","high":"6.088","low":"5.9935","varBid":"0.0101","pctChange":"0.17","bid":"6.059","ask":"6.061","timestamp":"1737142381","create_date":"2025-01-17 16:33:01"}]';
        return json_decode($json, true);

        $response = Http::get($this->url . "{$currency}-{$currencyIn}");
        $result = $response->successful() ? $response->json() : [];

        return $result;
    }
}