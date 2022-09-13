<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Endpoint extends Model
{
    protected $fillable = ['dompul_chip_id', 'url', 'api_id', 'api_key'];
}
