<?php

namespace Tests\Traits;

trait CurrencyHelper
{
    protected function createNewCurrency()
    {
        $currency = [
            "code" => "USD",
            "code_in" => "D&D",
            "description" => "Dolar Americano/D&D Peça de ouro",
            "bid" => '2.2500',
            "ask" => '2.2500'
        ];
        return \App\Models\Currency::create($currency);
    }
}