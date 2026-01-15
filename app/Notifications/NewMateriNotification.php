<?php

namespace App\Notifications;

use App\Models\Materi;
use App\Models\Matpel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMateriNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Materi $materi,
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
        $user = User::find($this->materi->created_by_user_id);
        $matpel = Matpel::find($this->materi->matpel_kode);
        return [
            'materi_id' => $this->materi->id,
            'type' => 'materi',
            'matpel' => $matpel->nama,
            'matpel_kode' => $this->materi->matpel_kode,
            'materi_title' => $this->materi->title,
            'guru' => $user->name,
            'link' => url('/orders/' . $this->materi->id),
        ];
    }
}
