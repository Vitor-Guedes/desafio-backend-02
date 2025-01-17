<?php

namespace App\Http\Controllers;

use App\Dto\CurrencyDto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Service\CurrencyService;
use App\Dto\Currency\DeleteDto as CurrencyDeleteDto;

class CurrencyController extends Controller
{
    /**
     * Register new currency quotation
     * 
     * @param CurrencyService $currencyService
     * @param Request $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CurrencyService $currencyService, Request $request)
    {
        $validated = $this->validate($request, [
            'code' => 'required|string|min:3|max:5',
            'code_in' => 'required|string|min:3|max:5',
            'description' => 'required|string|max:100',
            'bid' => 'required|decimal:2,4',
            'ask' => 'required|decimal:2,4'
        ]);
        $currencyService->store($validated);
        return response()->json([], Response::HTTP_CREATED);
    }

    /**
     * Delete currency or currencies quotes
     * 
     * @param string $code ":code-:code_in" - "USD-D&D"
     * @param CurrencyService $currencyService
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $code, CurrencyService $currencyService)
    {
        $currencyDto = app(CurrencyDeleteDto::class, ['code' => $code]);
        $currencyService->destroy(...$currencyDto->toArray());
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * Retrive currency quote 
     * 
     * @param string $code ":code-:code_in" - "USD-D&D"
     * @param CurrencyService $currencyService
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function quote(string $code, CurrencyService $currencyService)
    {
        $currencyDto = app(CurrencyDto::class, ['code' => $code]);
        $quotes = $currencyService->getQuote(...$currencyDto->toArray());
        return response()->json($quotes, 200);
    }
}
