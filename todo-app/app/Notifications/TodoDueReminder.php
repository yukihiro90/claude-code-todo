<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class TodoDueReminder extends Notification
{
    use Queueable;

    protected Collection $todos;

    /**
     * Create a new notification instance.
     */
    public function __construct(Collection $todos)
    {
        $this->todos = $todos;
    }

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
        $message = (new MailMessage)
            ->subject('【Todoアプリ】明日が期限のタスクがあります')
            ->greeting($notifiable->name . 'さん')
            ->line('以下のTodoが明日期限を迎えます：');

        foreach ($this->todos as $todo) {
            $message->line('・ ' . $todo->title);
        }

        $message->action('Todoを確認する', url('/'))
                ->line('期限内に完了させましょう！');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'todo_ids' => $this->todos->pluck('id')->toArray(),
        ];
    }
}
