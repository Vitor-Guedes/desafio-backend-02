<?php

namespace Tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CurrencyTest extends TestCase
{
    use DatabaseMigrations;

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

    protected function createNewCurrency()
    {
        $currency = [
            "code" => "USD",
            "code_in" => "D&D",
            "description" => "Dolar Americano/D&D Peça de ouro",
            "bid" => 2.2500,
            "ask" => 2.2500
        ];
        return \App\Models\Currency::create($currency);
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
        
        $response = $this->json('GET', route('api.currency.quote', ['code' => $combine]));

        $response->assertResponseOk();
        $response->seeJson([
            [
                "code" => $code,
                "codein" => $codeIn,
                "name" => "",
                "high" => "",
                "low" => "",
                "varBid" => 0.0000,
                "pctChange" => 0.0000,
                "bid" => 0.0000,
                "ask" => 0.0000,
                "timestamps" => "",
                "create_date" => "yyyy-mm-dd hh:ii:ss"
            ]
        ]);
    }

    public function test_must_be_able_to_bring_the_quotation_of_new_currencies_registered_in_the_database()
    {
        $response->asserResponseOk();
        $response->seeJson([
            [
                "code" => $code,
                "codein" => $codeIn,
                "name" => "",
                "high" => "",
                "low" => "",
                "varBid" => 0.0000,
                "pctChange" => 0.0000,
                "bid" => 0.0000,
                "ask" => 0.0000,
                "timestamps" => "",
                "create_date" => "yyyy-mm-dd hh:ii:ss"
            ]
        ]);
    }

    public function test_must_be_able_to_convert_the_value_entered_between_the_given_currencies()
    {
        $response->asserResponseOk();
        $response->seeJson([
            "from" => $from,
            "to" => $to,
            "amount" => 1.0000,
            "cahnge" => 3.0000
        ]);

    }

    public function test_should_fail_when_trying_to_convert_the_value_with_invalid_parameters()
    {
        $response->assertStatus(422);
        $response->seeJson([
            'error' => [
                'message' => [

                ]
            ]
        ]);
    }

    // public function test_must_be_able_to_perform_the_conversion()
    // {
    //     $params = [
    //         "from" => 'USD',
    //         "to" => 'BRL',
    //         "amount" => 1.00
    //     ];
    //     $quote = file_get_contents('https://economia.awesomeapi.com.br/json/USD-BRL/1');
    //     $expectedResult = $params['amount'] * json_decode($quote, true)[0]['ask'];

    //     $response = $this->json('GET', route('api.convert'), $params);

    //     $response->assertResponseOk();
    //     $response->seeJson([
    //         'result' => $expectedResult
    //     ]);
    // }
}