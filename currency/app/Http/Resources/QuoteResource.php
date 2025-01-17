<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
{
    /**
     * @param Request $request
     * 
     * @return array
     */
    public function toArray(Request $request)
    {
        return [
            'code' => $this['code'],
            'code_in' => $this['code_in'],
            'name' => $this['description'],
            'high' => '',
            'low' => '',
            'varBid' => 0,
            'pctChange' => 0,
            "bid" => $this['bid'],
            "ask" => $this['ask'],
            "timestamp" => $this['timestamp'],
        ];
    }
}