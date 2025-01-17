<?php

namespace App\Dto\Currency;

class DeleteDto
{
    public function __construct(
        protected string $code
    )
    { }

    public function toArray(): array
    {
        if (strpos($this->code, '-')) {{
            [$currency, $currencyIn] = explode('-', $this->code);
            return compact('currency', 'currencyIn');
        }}
        return ['currency' => $this->code];
    }
}