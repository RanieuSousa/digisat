<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contas extends Model
{
    protected $table = 'contas';
    protected $fillable = [
        'codigo',
        'codigo_venda',
        'codigo_cliente',
        'valor',
        'data_vencimento',
        'data_envio',
        'cilcus',
        'tipo',
        'loja',
    ];
}
