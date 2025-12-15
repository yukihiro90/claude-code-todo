<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use App\Notifications\TodoDueReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendTodoRemindersTest extends TestCase
{
    use RefreshDatabase;

    public function test_reminder_is_sent_to_users_with_due_tomorrow_todos(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        Todo::factory()->dueTomorrow()->create(['user_id' => $user->id]);

        $this->artisan('todos:send-reminders')
             ->expectsOutput('リマインダー送信を開始します...')
             ->assertSuccessful();

        Notification::assertSentTo($user, TodoDueReminder::class);
    }

    public function test_reminder_is_not_sent_when_no_todos_due_tomorrow(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        // 今日期限のTodo（明日ではない）
        Todo::factory()->dueToday()->create(['user_id' => $user->id]);

        $this->artisan('todos:send-reminders')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_reminder_is_not_sent_for_completed_todos(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        // 明日期限だが完了済み
        Todo::factory()->dueTomorrow()->completed()->create(['user_id' => $user->id]);

        $this->artisan('todos:send-reminders')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_reminder_includes_multiple_todos(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        Todo::factory()->dueTomorrow()->count(3)->create(['user_id' => $user->id]);

        $this->artisan('todos:send-reminders')->assertSuccessful();

        Notification::assertSentTo($user, TodoDueReminder::class, function ($notification) {
            $array = $notification->toArray(new User());
            return count($array['todo_ids']) === 3;
        });
    }

    public function test_multiple_users_receive_their_own_reminders(): void
    {
        Notification::fake();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Todo::factory()->dueTomorrow()->create(['user_id' => $user1->id]);
        Todo::factory()->dueTomorrow()->count(2)->create(['user_id' => $user2->id]);

        $this->artisan('todos:send-reminders')->assertSuccessful();

        Notification::assertSentTo($user1, TodoDueReminder::class);
        Notification::assertSentTo($user2, TodoDueReminder::class);
    }
}
