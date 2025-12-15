<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\TodoDueReminder;
use Illuminate\Console\Command;

class SendTodoReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'todos:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '明日が期限のTodoについてユーザーにリマインダーメールを送信';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('リマインダー送信を開始します...');

        // 明日が期限の未完了Todoを持つユーザーを取得（N+1問題を回避）
        $usersWithDueTodos = User::whereHas('todos', function ($query) {
            $query->dueTomorrow();
        })->with(['todos' => function ($query) {
            $query->dueTomorrow();
        }])->get();

        $sentCount = 0;

        foreach ($usersWithDueTodos as $user) {
            $dueTodos = $user->todos;

            if ($dueTodos->isNotEmpty()) {
                $user->notify(new TodoDueReminder($dueTodos));
                $sentCount++;
                $this->line("  → {$user->email} に {$dueTodos->count()} 件のリマインダーを送信");
            }
        }

        $this->info("完了: {$sentCount} 人のユーザーにリマインダーを送信しました。");

        return Command::SUCCESS;
    }
}
