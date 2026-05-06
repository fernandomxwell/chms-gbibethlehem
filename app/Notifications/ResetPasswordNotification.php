<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends Notification
{
    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expireMinutes = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

        return $this->buildMailMessage($url, $expireMinutes);
    }

    protected function buildMailMessage(string $url, int $expireMinutes): MailMessage
    {
        $locale = app()->getLocale();

        if ($locale === 'id') {
            return (new MailMessage)
                ->subject('Notifikasi Reset Kata Sandi')
                ->greeting('Halo!')
                ->line('Anda menerima email ini karena kami menerima permintaan reset kata sandi untuk akun Anda.')
                ->action('Reset Kata Sandi', $url)
                ->line("Tautan reset kata sandi ini akan kedaluwarsa dalam {$expireMinutes} menit.")
                ->line('Jika Anda tidak meminta reset kata sandi, tidak diperlukan tindakan lebih lanjut.')
                ->salutation('Salam, ' . config('app.name'));
        }

        return (new MailMessage)
            ->subject('Reset Password Notification')
            ->greeting('Hello!')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $url)
            ->line("This password reset link will expire in {$expireMinutes} minutes.")
            ->line('If you did not request a password reset, no further action is required.')
            ->salutation('Regards, ' . config('app.name'));
    }
}
