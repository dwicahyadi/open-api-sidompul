<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DompulChip extends Model
{
    protected $fillable = [
        'msisdn', 'pin', 'access_token', 'token_expired_at', 'client_id', 'client_secret'
    ];

    public function endpoints()
    {
        return $this->hasMany(Endpoint::class);
    }
}
