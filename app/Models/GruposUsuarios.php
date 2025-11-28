<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GruposUsuarios extends Model
{
    protected $table = 'grupos_usuarios';

    protected $fillable = [
        'usuario_id',
        'grupo_id',
    ];

    public $timestamps = false;
}
