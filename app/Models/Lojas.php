<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lojas extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class, 'lojas_usuarios', 'loja_id', 'usuario_id');
    }
}
