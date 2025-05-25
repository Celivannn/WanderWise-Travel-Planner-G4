<?php

namespace App\Http\Controllers;

use App\Services\CurrencyService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    protected CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function convert(Request $request)
    {
        $from = strtoupper($request->query('from', 'USD'));
        $to = strtoupper($request->query('to', 'EUR'));
        $amount = (float) $request->query('amount', 1);

        return $this->currencyService->convert($from, $to, $amount);
    }
}
