<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensagens extends Model
{
    protected $table = 'mensagens';
    protected $fillable = ['mensagem', 'tipo'];

    public $timestamps = false;

}
