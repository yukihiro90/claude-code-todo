<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todoアプリ</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        h1 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            padding: 30px;
            margin-bottom: 20px;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
        }

        input[type="text"] {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-danger {
            background: #dc3545;
            color: white;
            padding: 6px 12px;
            font-size: 14px;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        input[type="date"] {
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
            min-width: 150px;
        }

        input[type="date"]:focus {
            outline: none;
            border-color: #667eea;
        }

        .due-date {
            font-size: 12px;
            color: #666;
            margin-left: 10px;
        }

        .due-date.overdue {
            color: #dc3545;
            font-weight: 600;
        }

        .due-date.today {
            color: #fd7e14;
            font-weight: 600;
        }

        .due-date.tomorrow {
            color: #ffc107;
            font-weight: 600;
        }

        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }

        .todo-list {
            list-style: none;
        }

        .todo-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s;
        }

        .todo-item:hover {
            background-color: #f8f9fa;
        }

        .todo-item:last-child {
            border-bottom: none;
        }

        .todo-checkbox {
            display: flex;
            align-items: center;
            flex: 1;
            cursor: pointer;
        }

        .todo-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 15px;
            cursor: pointer;
        }

        .todo-title {
            font-size: 16px;
            color: #333;
        }

        .todo-title.completed {
            text-decoration: line-through;
            color: #999;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .user-info {
            color: white;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            backdrop-filter: blur(10px);
        }

        .user-name {
            font-weight: 600;
        }

        .btn-logout {
            background: rgba(220, 53, 69, 0.9);
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-logout:hover {
            background: rgba(200, 35, 51, 0.9);
            transform: translateY(-2px);
        }

        @media (max-width: 640px) {
            h1 {
                font-size: 2rem;
            }

            .card {
                padding: 20px;
            }

            .header {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📝 Todoアプリ</h1>
            <div class="user-info">
                <span class="user-name">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout">ログアウト</button>
                </form>
            </div>
        </div>

        <div class="card">
            @if(session('success'))
                <div class="alert">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('todos.store') }}" method="POST">
                @csrf
                <div class="input-group">
                    <input
                        type="text"
                        name="title"
                        placeholder="新しいTodoを入力..."
                        required
                        autofocus
                    >
                    <input
                        type="date"
                        name="due_date"
                        min="{{ date('Y-m-d') }}"
                    >
                    <button type="submit" class="btn btn-primary">追加</button>
                </div>
                @error('due_date')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </form>

            @if($todos->count() > 0)
                <ul class="todo-list">
                    @foreach($todos as $todo)
                        <li class="todo-item">
                            <form action="{{ route('todos.update', $todo) }}" method="POST" class="todo-checkbox">
                                @csrf
                                @method('PATCH')
                                <input
                                    type="checkbox"
                                    {{ $todo->completed ? 'checked' : '' }}
                                    onchange="this.form.submit()"
                                >
                                <span class="todo-title {{ $todo->completed ? 'completed' : '' }}">
                                    {{ $todo->title }}
                                    @if($todo->due_date)
                                        @php
                                            $dueDate = $todo->due_date;
                                            $today = now()->startOfDay();
                                            $class = '';
                                            if ($dueDate->lt($today)) {
                                                $class = 'overdue';
                                            } elseif ($dueDate->eq($today)) {
                                                $class = 'today';
                                            } elseif ($dueDate->eq($today->copy()->addDay())) {
                                                $class = 'tomorrow';
                                            }
                                        @endphp
                                        <span class="due-date {{ $class }}">
                                            {{ $dueDate->format('Y/m/d') }}
                                            @if($class === 'overdue')（期限切れ）@endif
                                            @if($class === 'today')（今日）@endif
                                            @if($class === 'tomorrow')（明日）@endif
                                        </span>
                                    @endif
                                </span>
                            </form>

                            <form action="{{ route('todos.destroy', $todo) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('本当に削除しますか?')">
                                    削除
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p>まだTodoがありません<br>上のフォームから追加してみましょう！</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
