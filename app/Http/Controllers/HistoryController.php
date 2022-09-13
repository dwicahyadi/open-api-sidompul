<?php

namespace App\Http\Controllers;

use App\Models\DompulChip;
use App\Service\Sidompul;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function __invoke(Request $request)
    {
        if (!$request['msisdnA'] )
            return response('Parameter tidak lengkap',404);

        $chip = DompulChip::where('msisdn', $request['msisdnA'])->first();

        $sidompul = new Sidompul($chip);

        $trx = $sidompul->transactionHistory($request['period'] ?? 'today');

        return $trx;
    }

}
