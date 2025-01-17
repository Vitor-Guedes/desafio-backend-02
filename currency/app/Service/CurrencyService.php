<?php

namespace App\Service;

use App\Models\Currency;

class CurrencyService
{
    public function store(array $data)
    {
        return Currency::create($data);
    }

    public function destroy(string $currency, string $currencyIn = '')
    {
        $builder = Currency::where('code', $currency);
        if ($currencyIn) {
            $builder->where('code_in', $currencyIn);
        }
        if (! $builder->count()) {
            return false;
        }
        return $builder->delete();
    }

    // protected array $quotes = [];

    // public function __construct(
    //     protected string $from,
    //     protected string $to,
    //     protected float $amount
    // )
    // {
    //     $this->loadCacheQuotation();
    // }

    protected function loadCacheQuotation()
    {
        $fromTo = "{$this->from}-{$this->to}";
        $key = "cache_quote_{$fromTo}";

        if (app('redis')->exists($key)) {
            $this->quotes = json_decode(app('redis')->get($key), true);
            return ;
        }

        $baseUrl = 'https://economia.awesomeapi.com.br/json/' . $fromTo; 

        $quote = file_get_contents($baseUrl);

        $this->quotes[$fromTo] = json_decode($quote, true)[0];
        
        app('redis')->set($key, json_encode($this->quotes));
        app('redis')->expire($key, 60 * 3);
    }

    public function convert()
    {
        return [
            'result' => $this->amount * $this->getBuyPrice()
        ];
    }

    protected function getBuyPrice()
    {
        return $this->quotes["{$this->from}-{$this->to}"]['ask'];
    }
}