<?php

namespace App\Http\Controllers;

use App\Models\DompulChip;
use App\Service\Sidompul;
use Illuminate\Http\Request;

class TrxController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function __invoke(Request $request)
    {
        if (!$request['msisdnA'] || !$request['msisdnB'] || !$request['productCode'] )
            return response('Parameter tidak lengkap',404);

        $chip = DompulChip::where('msisdn', $request['msisdnA'])->first();

        $sidompul = new Sidompul($chip);

        $trx = $sidompul->exec($request['msisdnB'],$request['productCode']);

        return $trx;


    }
}
