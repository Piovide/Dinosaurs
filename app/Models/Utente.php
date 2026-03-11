<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\VerificaEmailNotification;
use App\Notifications\ReimpostaPasswordNotification;

class Utente extends Authenticatable implements MustVerifyEmail {
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
     * Relazione con collezioni utente
     */
    public function collezione_utente(){
        return $this->hasMany(CollezioneUtente::class, 'utn_id_utente', 'id_utente');
    }

    public function sendEmailVerificationNotification(?string $url = null): void
    {
        $this->notify(new VerificaEmailNotification($url));
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ReimpostaPasswordNotification($token));
    }

    public function isAdmin(): bool
    {
        return $this->ruolo === 'admin';
    }

    public function isModerator(): bool
    {
        return $this->ruolo === 'moderatore' || $this->ruolo === 'admin';
    }
}
