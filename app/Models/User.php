<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'role',
        'position',
        'birth_date',
        'zipcode',
        'street',
        'neighborhood',
        'city',
        'state',
        'number',
        'complement',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Retorna o administrador (gestor) que criou este funcionário.
     *
     * Relacionamento belongsTo com a própria tabela de usuários, filtrando para apenas usuários com papel de 'admin'.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'created_by')
            ->where('role', 'admin');
    }

    /**
     * Retorna os funcionários criados por este administrador.
     *
     * Relacionamento hasMany com a própria tabela de usuários, filtrando apenas os usuários com papel de 'employee'.
     */
    public function employees()
    {
        return $this->hasMany(User::class, 'created_by')
            ->where('role', 'employee');
    }

    /**
     * Retorna os registros de ponto (punches) associados a este usuário.
     *
     * Relacionamento hasMany com a tabela de punches, representando os horários de entrada e saída do funcionário.
     */
    public function punches()
    {
        return $this->hasMany(Punch::class);
    }
}
