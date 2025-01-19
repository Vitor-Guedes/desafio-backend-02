<?php

namespace App\Dto\Currency;

class ConvertDto
{
    public function __construct(
        public string $from,
        public string $to,
        public float $amount,
    ) {
        $this->from = strtoupper($this->from);
        $this->to = strtoupper($this->to);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}