<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'utente';
    protected $primaryKey = 'id_utente';
    protected $keyType = 'int';
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nome',
        'cognome',
        'username',
        'email',
        'password',
        'preferences',
        'ruolo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'preferences' => 'json',
        ];
    }

    /**
     * Metodi Helper per verificare il ruolo
     */
    public function isAdmin(): bool
    {
        return $this->ruolo === 'admin';
    }

    public function isModerator(): bool
    {
        return $this->ruolo === 'moderatore';
    }

    public function isUser(): bool
    {
        return $this->ruolo === 'utente';
    }

    public function hasRole(string $role): bool
    {
        return $this->ruolo === $role;
    }

    /**
     * Relazione con collezioni utente
     */
    public function collezioniUtenti()
    {
        return $this->hasMany(CollezioneUtente::class, 'utn_id_utente', 'id_utente');
    }
    /**
     * Relazione con collezioni utente
     */
    public function collezione_utente()
    {
        return $this->hasMany(CollezioneUtente::class, 'utn_id_utente', 'id_utente');
    }
}
