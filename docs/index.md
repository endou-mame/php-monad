---
layout: home

hero:
  name: PHP Monad
  text: 関数型プログラミングのモナド概念を PHP で実装
  tagline: Rust の Option / Result 型に着想を得た、型安全で堅牢なエラーハンドリング
  actions:
    - theme: brand
      text: はじめに
      link: /guide/getting-started
    - theme: alt
      text: API リファレンス
      link: /api/monad

features:
  - icon: 🛡️
    title: null 安全
    details: Option モナドにより、null 参照エラーを型レベルで防止できます。Some と None を明示的に扱うことで、安全なコードを書けます。
  - icon: ⚡
    title: 例外なしのエラーハンドリング
    details: Result モナドにより、例外を使わずにエラーを扱えます。Ok と Err を返すことで、エラーの伝播を型安全に行えます。
  - icon: 🔗
    title: メソッドチェーン
    details: map、filter、andThen などのメソッドをチェーンして、宣言的にデータを変換できます。
  - icon: 🔒
    title: 型安全
    details: PHPStan レベル max で静的解析をサポート。テンプレート型による型推論で、IDE の補完も効きます。
---

## クイックスタート

### インストール

```bash
composer require endoumame/php-monad
```

### Option の使用例

```php
use EndouMame\PhpMonad\Option;

// 値を Option でラップ
$value = Option\some(42);           // Some<int>
$empty = Option\none();             // None

// null かもしれない値を安全に処理
$name = Option\fromValue($user['name'] ?? null);

$result = $name
    ->map(fn($n) => strtoupper($n))      // 値があれば変換
    ->filter(fn($n) => strlen($n) > 0)   // 条件で絞り込み
    ->unwrapOr('Anonymous');              // None なら代替値
```

### Result の使用例

```php
use EndouMame\PhpMonad\Result;

// 成功 / 失敗を明示的に表現
$success = Result\ok(42);           // Ok<int>
$failure = Result\err('error');     // Err<string>

// 例外を Result に変換
$result = Result\fromThrowable(
    fn() => json_decode($json, flags: JSON_THROW_ON_ERROR),
    fn(Throwable $e) => "JSON パースエラー: {$e->getMessage()}"
);

$data = $result
    ->map(fn($decoded) => $decoded['key'])
    ->unwrapOr(null);
```

## アーキテクチャ

```mermaid
classDiagram
    class Monad {
        <<interface>>
        +unit(value) self
        +andThen(fn) self
    }

    class Option {
        <<interface>>
        +isSome() bool
        +isNone() bool
        +map(fn) Option
        +unwrap() T
    }

    class Result {
        <<interface>>
        +isOk() bool
        +isErr() bool
        +map(fn) Result
        +unwrap() T
    }

    class Some {
        +value T
    }

    class None {
        <<enum>>
    }

    class Ok {
        +value T
    }

    class Err {
        +error E
    }

    Monad <|-- Option
    Monad <|-- Result
    Option <|.. Some
    Option <|.. None
    Result <|.. Ok
    Result <|.. Err
```

## 要件

- PHP 8.3 以上

## ライセンス

MIT License
