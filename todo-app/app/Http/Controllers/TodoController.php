<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $todos = auth()->user()->todos()
            ->orderByRaw('due_date IS NULL, due_date ASC')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('todos.index', compact('todos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);

        auth()->user()->todos()->create([
            'title' => $request->title,
            'completed' => false,
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('todos.index')->with('success', 'Todoが作成されました！');
    }

    public function update(Request $request, Todo $todo)
    {
        if ($todo->user_id !== auth()->id()) {
            abort(403);
        }

        $todo->update([
            'completed' => !$todo->completed,
        ]);

        return redirect()->route('todos.index')->with('success', 'Todoを更新しました！');
    }

    public function destroy(Todo $todo)
    {
        if ($todo->user_id !== auth()->id()) {
            abort(403);
        }

        $todo->delete();

        return redirect()->route('todos.index')->with('success', 'Todoを削除しました！');
    }
}
