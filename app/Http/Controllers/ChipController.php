<?php

namespace App\Http\Controllers;

use App\Models\DompulChip;
use App\Service\Sidompul;
use Illuminate\Http\Request;

class ChipController extends Controller
{
    public function register(Request $request)
    {
        if (!$request['msisdn'] || !$request['pin'] || !$request['id'] || !$request['secret'])
            return response('Parameter tidak lengkap',404);


        $chip = DompulChip::create([
            'msisdn' => $request['msisdn'],
            'client_id' => $request['id'],
            'client_secret' => $request['secret'],
            'pin' => 'not set'
        ]);

        $sidompul = new Sidompul($chip);

        $sidompul->updatePin($request['pin']);

        return response('success', 201);
    }

    public function addEndpoint(Request $request)
    {
        if (!$request['msisdn'] || !$request['pin'] || !$request['url']  || !$request['id'] || !$request['key'])
            return response('Parameter tidak lengkap',404);


        $chip = DompulChip::where('msisdn', $request['msisdn'])->first();

        $sidompul = new Sidompul($chip);

        if ($sidompul->checkPin($request['pin']))
            return response('PIN Salah',403);

        $chip->endpoints()->create([
            'url' => $request['url'],
            'api_id' => $request['id'],
            'api_key' => $request['key'],
        ]);

        return response('success', 201);
    }
}
