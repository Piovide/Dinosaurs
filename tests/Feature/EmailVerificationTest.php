<?php

namespace Tests\Feature;

use App\Models\Utente;
use App\Notifications\VerificaEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    // ─── VerificaEmailNotification ────────────────────────────────────────────

    public function test_notification_uses_custom_url_when_provided(): void
    {
        $user = Utente::factory()->create(['email_verified_at' => null]);

        $customUrl = 'https://example.com/email/verify/custom';
        $notification = new VerificaEmailNotification($customUrl);

        $mail = $notification->toMail($user);

        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertSame($customUrl, $mail->actionUrl);
    }

    public function test_notification_falls_back_to_generated_url_when_no_url_provided(): void
    {
        $user = Utente::factory()->create(['email_verified_at' => null]);

        $notification = new VerificaEmailNotification();

        $mail = $notification->toMail($user);

        $this->assertInstanceOf(MailMessage::class, $mail);
    }

    // ─── Utente::sendEmailVerificationNotification ───────────────────────────

    public function test_send_email_verification_notification_dispatches_notification(): void
    {
        Notification::fake();

        $user = Utente::factory()->create(['email_verified_at' => null]);
        $user->sendEmailVerificationNotification();

        Notification::assertSentTo($user, VerificaEmailNotification::class);
    }

    public function test_send_email_verification_notification_passes_custom_url(): void
    {
        Notification::fake();

        $user = Utente::factory()->create(['email_verified_at' => null]);
        $customUrl = 'https://example.com/verify/test';
        $user->sendEmailVerificationNotification($customUrl);

        Notification::assertSentTo(
            $user,
            VerificaEmailNotification::class,
            fn(VerificaEmailNotification $notification) => (
                $notification->toMail($user)->actionUrl === $customUrl
            )
        );
    }

    // ─── Verification route ──────────────────────────────────────────────────

    public function test_valid_signed_url_verifies_email_and_logs_in(): void
    {
        $user = Utente::factory()->create(['email_verified_at' => null]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id_utente, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->get($url);

        $response->assertRedirect(route('home'));
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_hash_returns_403(): void
    {
        $user = Utente::factory()->create(['email_verified_at' => null]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id_utente, 'hash' => 'wronghash']
        );

        $response = $this->get($url);

        $response->assertForbidden();
    }

    public function test_tampered_url_returns_403(): void
    {
        $user = Utente::factory()->create(['email_verified_at' => null]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id_utente, 'hash' => sha1($user->getEmailForVerification())]
        );

        // tamper with the URL by appending an extra query parameter
        $tamperedUrl = $url . '&extra=1';

        $response = $this->get($tamperedUrl);

        $response->assertForbidden();
    }

    public function test_expired_url_returns_403(): void
    {
        $user = Utente::factory()->create(['email_verified_at' => null]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->subMinute(),
            ['id' => $user->id_utente, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->get($url);

        $response->assertForbidden();
    }

    public function test_already_verified_user_is_still_logged_in_and_redirected(): void
    {
        $user = Utente::factory()->create(['email_verified_at' => now()]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id_utente, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->get($url);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }
}
