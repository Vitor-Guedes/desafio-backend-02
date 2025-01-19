<?php

namespace App\Service;

class ConvertService
{
    public function __construct(
        protected CacheService $cacheService,
        protected CurrencyService $currencyService
    ) {  }

    /**
     * @param string $from
     * @param string $to
     * @param float $amount
     */
    public function convert(string $from, string $to, float $amount): array
    {
        $key = "{$from}-{$to}-{$amount}";
        return $this->cacheService->fromCache($key, function () use ($from, $to, $amount) {
            $quote = $this->currencyService->getQuote($from, $to);
            if (isset($quote[0])) {
                $quote = current($quote);
            }

            $change = $amount * $quote['ask'];

            return [
                'from' => $from,
                'to' => $to,
                'amount' => $amount,
                'change' => $change
            ];
        });
    }
}