<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'completed', 'user_id', 'due_date'];

    protected $casts = [
        'completed' => 'boolean',
        'due_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 明日が期限の未完了Todoを取得するスコープ
     */
    public function scopeDueTomorrow($query)
    {
        return $query->where('completed', false)
                     ->whereDate('due_date', now()->addDay()->toDateString());
    }

    /**
     * 期限のステータスを取得するアクセサ
     */
    public function getDueStatusAttribute(): ?string
    {
        if (!$this->due_date) {
            return null;
        }

        $today = now()->startOfDay();

        if ($this->due_date->lt($today)) {
            return 'overdue';
        }
        if ($this->due_date->eq($today)) {
            return 'today';
        }
        if ($this->due_date->eq($today->copy()->addDay())) {
            return 'tomorrow';
        }

        return null;
    }

    /**
     * 期限のラベルを取得するアクセサ
     */
    public function getDueLabelAttribute(): ?string
    {
        return match ($this->due_status) {
            'overdue' => '期限切れ',
            'today' => '今日',
            'tomorrow' => '明日',
            default => null,
        };
    }
}
