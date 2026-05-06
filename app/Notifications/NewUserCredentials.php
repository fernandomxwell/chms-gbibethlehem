<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserCredentials extends Notification
{
    public function __construct(private string $plainPassword) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = app()->getLocale();
        $appName = config('app.name');

        if ($locale === 'id') {
            return (new MailMessage)
                ->subject('Akun Anda di ' . $appName)
                ->greeting('Halo, ' . $notifiable->name . '!')
                ->line('Akun Anda telah dibuat di ' . $appName . '.')
                ->line('Gunakan kredensial berikut untuk masuk:')
                ->line('**Email:** ' . $notifiable->email)
                ->line('**Kata Sandi:** ' . $this->plainPassword)
                ->action('Masuk Sekarang', route('login'))
                ->line('Demi keamanan, segera ubah kata sandi Anda setelah masuk pertama kali menggunakan fitur "Lupa Kata Sandi".')
                ->salutation('Salam, ' . $appName);
        }

        return (new MailMessage)
            ->subject('Your Account at ' . $appName)
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line('Your account has been created at ' . $appName . '.')
            ->line('Use the following credentials to log in:')
            ->line('**Email:** ' . $notifiable->email)
            ->line('**Password:** ' . $this->plainPassword)
            ->action('Log In Now', route('login'))
            ->line('For security, please change your password after your first login using the "Forgot Password" feature.')
            ->salutation('Regards, ' . $appName);
    }
}
