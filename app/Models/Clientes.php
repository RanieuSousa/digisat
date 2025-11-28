<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'codigo',
        'nome',
        'email',
        'telefone',
        'loja',
        'valor_ultimacompra',
        'data_ultimacompra',
        'vendedor',
        'profissao',
        'cidade',
        'estado',
    ];
}
