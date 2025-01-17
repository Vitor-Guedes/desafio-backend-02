<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Service\CurrencyService;
use Illuminate\Http\Request;
use App\Dto\Currency\DeleteDto as CurrencyDeleteDtop;

class CurrencyController extends Controller
{
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

    public function destroy(string $code, CurrencyService $currencyService)
    {
        $currencyDto = app(CurrencyDeleteDtop::class, ['code' => $code]);
        $currencyService->destroy(...$currencyDto->toArray());
        return response()->json([], Response::HTTP_OK);
    }

    public function quote()
}
