<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LojasUsuarios extends Model
{
    protected $table = 'lojas_usuarios'; // se não estiver padrão

    protected $fillable = [
        'usuario_id',
        'loja_id',
    ];

    public $timestamps = false; // se não tiver created_at/updated_at
}
