<?php

namespace Tests;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Tests\Traits\CurrencyHelper;
use Laravel\Lumen\Testing\DatabaseMigrations;

class CurrencyTest extends TestCase
{
    use DatabaseMigrations,
        CurrencyHelper;

    public function test_must_be_able_to_record_a_quote_for_a_new_currency()
    {
        $payload = [
            "code" => "USD",
            "code_in" => "D&D",
            "description" => "Dolar Americano/D&D Peça de ouro",
            "bid" => 2.2500,
            "ask" => 2.2500
        ];

        $response = $this->json('POST', route('api.currency.store'), $payload);

        $response->seeStatusCode(201);
        $this->seeInDatabase('quotes', $payload);
    }

    public function test_must_be_able_to_remove_a_coin_from_the_application()
    {   
        $currency = $this->createNewCurrency();

        $response = $this->json('DELETE', route('api.currency.destroy', ['code' => $currency->code]));

        $response->assertResponseOk();
        $this->missingFromDatabase('quotes', $currency->toArray());
    }

    public function test_must_be_able_to_delete_all_quotes_for_a_combination_of_currencies()
    {
        $currency = $this->createNewCurrency();

        $combine = "{$currency->code}-{$currency->code_in}";
        $response = $this->json('DELETE', route('api.currency.destroy', ['code' => $combine]));

        $response->assertResponseOk();
        $this->missingFromDatabase('quotes', $currency->toArray());
    }

    public function test_should_fail_when_trying_to_register_a_quote_for_a_new_currency_with_invalid_parameters()
    {
        $payload = [];

        $response = $this->json('POST', route('api.currency.store'), $payload);

        $response->seeStatusCode(422);
        $response->seeJson([
            "ask" => [
                "The ask field is required."
            ],
            "bid" => [
                "The bid field is required."
            ],
            "code" => [
                "The code field is required."
            ],
            "code_in" => [
                "The code in field is required."
            ],
            "description" => [
                "The description field is required."
            ]
        ]);
    }

    public function test_must_be_able_to_bring_the_quote_of_existing_currencies_from_the_extetnal_api()
    {
        $code = "USD";
        $codeIn = "BRL";
        $combine = "{$code}-{$codeIn}";
        $expected = file_get_contents('https://economia.awesomeapi.com.br/json/' . $combine);

        $response = $this->json('GET', route('api.currency.quote', ['code' => $combine]));

        $response->assertResponseOk();
        $response->seeJson(json_decode($expected, true));
    }

    public function test_must_be_able_to_bring_the_quotation_of_new_currencies_registered_in_the_database()
    {
        $currency = $this->createNewCurrency();
        $currency->refresh();

        $combine = "{$currency->code}-{$currency->code_in}";
        app('redis')->del($combine);

        $response = $this->json('GET', route('api.currency.quote', ['code' => $combine]));

        $response->assertResponseOk();
        $response->seeJson([
            [
                "code" => $currency->code,
                "code_in" => $currency->code_in,
                "name" => $currency->description,
                "high" => "",
                "low" => "",
                "varBid" => 0,
                "pctChange" => 0,
                "bid" => $currency->bid,
                "ask" => $currency->ask,
                "timestamp" => $currency->timestamp
            ]
        ]);
    }

    public function test_must_be_able_to_convert_the_value_entered_between_the_given_currencies()
    {
        $from = 'USD';
        $to = 'BRL';
        $amount = 20.0000;
        $parameters = [
            'from' => $from,
            'to' => $to,
            'amount' => $amount
        ];
        $url = route('api.convert.get') . '?'. http_build_query($parameters);

        $combine = "{$from}-{$to}";
        $quote = $this->json('GET', route('api.currency.quote', ['code' => $combine]));
        $quote = json_decode($quote->response->getContent(), true);
        
        $response = $this->json('GET', $url);
        
        $response->assertResponseOk();
        $response->seeJson([
            "from" => $from,
            "to" => $to,
            "amount" => $amount,
            "change" => $amount * $quote[0]['ask']
        ]);

    }

    public function test_should_fail_when_trying_to_convert_the_value_with_invalid_parameters()
    {
        $parameters = [];
        $url = route('api.convert.get') . '?'. http_build_query($parameters);
        
        $response = $this->json('GET', $url);

        $response->seeStatusCode(422);
        $response->seeJson([
            "amount" => [
                "The amount field is required."
            ],
            "from" => [
                "The from field is required."
            ],
            "to" => [
                "The to field is required."
            ]
        ]);
    }
}