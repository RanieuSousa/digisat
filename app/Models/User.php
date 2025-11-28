<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'telefone', // Adicionado para permitir salvar o telefone
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'profile_photo_url',
        'grupo_nome',
    ];

    /**
     * Relacionamento: um usuário pode ter vários grupos.
     */
    public function grupos()
    {
        return $this->belongsToMany(Grupos::class, 'grupos_usuarios', 'usuario_id', 'grupo_id');
    }

    /**
     * Relacionamento: um usuário pode ter várias lojas.
     */
    public function lojas()
    {
        return $this->belongsToMany(Lojas::class, 'lojas_usuarios', 'usuario_id', 'loja_id');
    }

    /**
     * Acessor para retornar o(s) nome(s) do grupo como string.
     */
    public function getGrupoNomeAttribute()
    {
        // Carrega os nomes dos grupos e junta com vírgula
        return $this->grupos->pluck('nome')->join(', ');
    }
}
