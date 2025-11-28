<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instancia extends Model
{
    protected $table = 'instancias';

    protected $fillable = [
        'instanceName',
        'hash',
        'status',
        'url',
        'integration'
    ];
}
