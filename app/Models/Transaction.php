<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'dompul_chip_id',
        'msisdn',
        'product_code',
        'transaction_id',
        'status',
        'description',
        'raw',
    ];
}
