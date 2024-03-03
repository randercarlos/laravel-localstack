<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileSavedInS3 extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private string $uploadedFilePath) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $s3UploadedFile = Str::of(Storage::disk('s3')->url($this->uploadedFilePath))->replaceFirst('localstack', 'localhost');
        return (new MailMessage)
                    ->subject('Arquivo salvo')
                    ->greeting('Olá!')
                    ->line("O arquivo $this->uploadedFilePath foi salvo com sucesso no S3")
                    ->action('Baixar Arquivo', $s3UploadedFile)
                    ->line('Obrigado por usar a nossa aplicação');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    public function routeNotificationForMail(Notification $notification): array|string
    {
        // Return email address and name...
//        return [$this->email_address => $this->name];
        return ['teste@teste.com' => $this->name];
    }
}
