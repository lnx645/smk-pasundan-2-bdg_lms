<?php

namespace App\Notifications;

use App\Models\Matpel;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTugasNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Tugas $tugas,
        public User $user
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $matpel = Matpel::find($this->tugas->matpel_kode);
        return [
            'type' => 'tugas',
            'tugas_id' => $this->tugas->id,
            'title' => $this->tugas->title,
            'matpel' => $matpel->nama,
            'guru' => $this->user->name,
            'message' => 'Memberikan tugas baru',
            'created_at' => now(),
        ];
    }
}
