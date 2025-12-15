# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Laravel 10ベースのTodoアプリケーション。ユーザー認証（Laravel Breeze）とユーザーごとのTodo管理機能を実装。

## Commands

すべてのコマンドは`todo-app/`ディレクトリで実行する。

```bash
# 開発サーバー起動
php artisan serve

# フロントエンドビルド（Vite）
npm run dev      # 開発
npm run build    # 本番

# データベース
php artisan migrate
php artisan migrate:fresh --seed  # リセット＆シード

# テスト
php artisan test                  # 全テスト実行
php artisan test --filter=TodoTest  # 特定テスト実行
php artisan test tests/Feature/Auth  # ディレクトリ指定

# コード品質
./vendor/bin/pint                 # Laravel Pint（コードフォーマット）

# その他
php artisan tinker                # REPL
php artisan route:list            # ルート一覧
```

## Architecture

### Models & Relations
- `User` hasMany `Todo`
- `Todo` belongsTo `User`
- Todoは`title`（string）、`completed`（boolean）、`user_id`（FK）を持つ

### Authentication
- Laravel Breezeによる認証（セッションベース）
- 認証が必要なルートは`auth`ミドルウェアで保護
- `TodoController`はコンストラクタで認証ミドルウェアを適用

### Key Files
- [routes/web.php](todo-app/routes/web.php) - Webルート定義
- [routes/auth.php](todo-app/routes/auth.php) - 認証ルート
- [app/Http/Controllers/TodoController.php](todo-app/app/Http/Controllers/TodoController.php) - Todo CRUD
- [app/Models/Todo.php](todo-app/app/Models/Todo.php) - Todoモデル
- [resources/views/todos/index.blade.php](todo-app/resources/views/todos/index.blade.php) - Todoリスト画面

### Authorization
TodoControllerの`update`/`destroy`メソッドで所有者チェックを実施：
```php
if ($todo->user_id !== auth()->id()) {
    abort(403);
}
```
