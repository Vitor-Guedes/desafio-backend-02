<?php

namespace App\Service;

use Closure;
use App\Models\Currency;
use App\Http\Resources\QuoteResource;
use Illuminate\Database\Eloquent\Model;

class CurrencyService
{
    public function __construct(
        protected ExternalService $externalService
    ) { }

    /**
     * @param array $data
     * 
     * @return Model
     */
    public function store(array $data): Model
    {
        return Currency::create($data);
    }

    /**
     * @param string $currency
     * @param string $currencyIn
     * 
     * @return bool
     */
    public function destroy(string $currency, string $currencyIn = ''): bool
    {
        $builder = Currency::where('code', $currency);
        
        if ($currencyIn) {
            $builder->where('code_in', $currencyIn);
        }

        return $builder->count() ? $builder->delete() : false; 
    }

    /**
     * @param string $currency
     * @param string $currencyIn
     * 
     * @return array
     */
    public function getQuote(string $currency, string $currencyIn = ''): array
    {
        return $this->cache("{$currency}-{$currencyIn}", function () use ($currency, $currencyIn) {
            if ($this->currenciesIsExternal($currency, $currencyIn)) {
                return $this->externalService()->getQuote($currency, $currencyIn);
            }

            return QuoteResource::collection(Currency::where('code', $currency)
                ->where('code_in', $currencyIn)
                ->orderBy('timestamp', 'desc')
                ->limit(1)
                ->get()
                ?->toArray() ?: []);
        });
    }

    /**
     * @param string $key
     * @param Closure
     * 
     * @return mixed
     */
    public function cache(string $key, Closure $resolve): mixed
    {
        $redis = app('redis');

        if ($redis->exists($key)) {
            return json_decode($redis->get($key), true);
        }

        $result = $resolve();

        $redis->set($key, json_encode($result));
        $redis->expire($key, 60 * 3);

        return $result;
    }

  /**
     * @param string $currency
     * @param string $currencyIn
     * 
     * @return bool
     */
    protected function currenciesIsExternal(string $currency, string $currencyIn = ''): bool
    {
        $externalCurrency = ['USD', 'BRL', 'EUR', 'BTC', 'ETH'];
        if ($currencyIn) {
            return in_array($currency, $externalCurrency) 
                && in_array($currencyIn, $externalCurrency);;    
        }
        return in_array($currency, $externalCurrency);
    }

    /**
     * @return object<\App\Service\ExternalService>
     */
    protected function externalService(): object
    {
        return $this->externalService;
    }
}