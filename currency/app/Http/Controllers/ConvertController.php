<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Service\ConvertService;
use App\Dto\Currency\ConvertDto as CurrencyConvertDto;

class ConvertController extends Controller
{
    /**
     * @param ConvertService $convertService
     * @param Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function convert(ConvertService $convertService, Request $request)
    {
        $parameters = $this->validate($request, [
            'from' => 'required|string|min:3|max:5',
            'to' => 'required|string|min:3|max:5',
            'amount' => 'required|decimal:0,4'
        ]);
        $currencyConvertDto = app(CurrencyConvertDto::class, $parameters);
        $conversion = $convertService->convert(...$currencyConvertDto->toArray());
        return response()->json($conversion, Response::HTTP_OK);
    }
}