<?php

namespace Tests;

use Tests\TestCase;

class CurrencyTest extends TestCase
{
    public function test_must_be_able_to_record_a_quote_for_a_new_currency()
    {
        $response->assertResponseOk();
    }

    public function test_should_fail_when_trying_to_register_a_quote_for_a_new_currency_with_invalid_parameters()
    {
        $response->assertStatus(422);
        $response->seeJson([
            'error' => [
                'message' => [

                ]
            ]
        ]);
    }

    public function test_must_be_able_to_bring_the_quote_of_existing_currencies_from_the_extetnal_api()
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