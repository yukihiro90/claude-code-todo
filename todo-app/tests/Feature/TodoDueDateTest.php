<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoDueDateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_todo_with_due_date(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/todos', [
            'title' => 'テストTodo',
            'due_date' => now()->addDays(3)->toDateString(),
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('todos', [
            'title' => 'テストTodo',
            'user_id' => $user->id,
        ]);

        $todo = Todo::where('title', 'テストTodo')->first();
        $this->assertEquals(now()->addDays(3)->toDateString(), $todo->due_date->toDateString());
    }

    public function test_user_can_create_todo_without_due_date(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/todos', [
            'title' => '期限なしTodo',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('todos', [
            'title' => '期限なしTodo',
            'due_date' => null,
        ]);
    }

    public function test_due_date_cannot_be_in_past(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/todos', [
            'title' => '過去日Todo',
            'due_date' => now()->subDay()->toDateString(),
        ]);

        $response->assertSessionHasErrors('due_date');
    }

    public function test_due_tomorrow_scope_returns_correct_todos(): void
    {
        $user = User::factory()->create();

        // 明日期限の未完了Todo（対象）
        $dueTomorrow = Todo::factory()->dueTomorrow()->create(['user_id' => $user->id]);

        // 今日期限（対象外）
        Todo::factory()->dueToday()->create(['user_id' => $user->id]);

        // 明日期限だが完了済み（対象外）
        Todo::factory()->dueTomorrow()->completed()->create(['user_id' => $user->id]);

        // 期限なし（対象外）
        Todo::factory()->create(['user_id' => $user->id]);

        $result = Todo::dueTomorrow()->get();

        $this->assertCount(1, $result);
        $this->assertEquals($dueTomorrow->id, $result->first()->id);
    }

    public function test_todos_are_sorted_by_due_date(): void
    {
        $user = User::factory()->create();

        // 期限なし
        $noDate = Todo::factory()->create(['user_id' => $user->id, 'title' => '期限なし']);

        // 3日後
        $threeDays = Todo::factory()->create([
            'user_id' => $user->id,
            'title' => '3日後',
            'due_date' => now()->addDays(3)->toDateString(),
        ]);

        // 明日
        $tomorrow = Todo::factory()->dueTomorrow()->create(['user_id' => $user->id, 'title' => '明日']);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSeeInOrder(['明日', '3日後', '期限なし']);
    }
}
