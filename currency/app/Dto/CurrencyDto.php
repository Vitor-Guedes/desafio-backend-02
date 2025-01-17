<?php

namespace App\Dto;

class CurrencyDto
{
    public function __construct(
        public string $code
    )
    { 
        $this->code = strtoupper($this->code);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        if (strpos($this->code, '-')) {{
            [$currency, $currencyIn] = explode('-', $this->code);
            return compact('currency', 'currencyIn');
        }}
        return ['currency' => $this->code];
    }
}