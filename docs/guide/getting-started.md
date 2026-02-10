# はじめに

PHP Monad は、関数型プログラミングのモナド概念を PHP で実装したライブラリです。Rust の `Option` 型と `Result` 型に着想を得ています。

## なぜモナドを使うのか

### null の問題

PHP では `null` がさまざまな問題を引き起こします。

```php
// 危険: $user が null だとエラー
$name = $user->getName();

// 従来の対処法: if 文のネスト
if ($user !== null) {
    $name = $user->getName();
    if ($name !== null) {
        $upperName = strtoupper($name);
    }
}
```

Option モナドを使うと、null チェックを型で表現できます。

```php
use EndouMame\PhpMonad\Option;

$upperName = Option\fromValue($user)
    ->map(fn($u) => $u->getName())
    ->map(fn($n) => strtoupper($n))
    ->unwrapOr('Unknown');
```

### 例外の問題

例外は制御フローを複雑にし、呼び出し元で適切にハンドリングされない可能性があります。

```php
// 従来の方法: try-catch が必要
try {
    $data = json_decode($json, flags: JSON_THROW_ON_ERROR);
    $result = processData($data);
} catch (JsonException $e) {
    $result = handleError($e);
}
```

Result モナドを使うと、成功と失敗を型で表現できます。

```php
use EndouMame\PhpMonad\Result;

$result = Result\fromThrowable(
    fn() => json_decode($json, flags: JSON_THROW_ON_ERROR),
    fn($e) => "パースエラー: {$e->getMessage()}"
)
->andThen(fn($data) => processData($data))
->unwrapOr(defaultValue());
```

## インストール

Composer を使用してインストールします。

```bash
composer require endou-mame/php-monad
```

## 要件

- PHP 8.3 以上

## 基本概念

### Option モナド

Option は「値の有無」を表現する型です。

- `Some<T>`: 値を保持している状態
- `None`: 値がない状態

```php
use EndouMame\PhpMonad\Option;

$some = Option\some(42);    // Some<int>
$none = Option\none();      // None
```

### Result モナド

Result は「成功または失敗」を表現する型です。

- `Ok<T>`: 成功値を保持している状態
- `Err<E>`: エラー値を保持している状態

```php
use EndouMame\PhpMonad\Result;

$ok = Result\ok(42);            // Ok<int>
$err = Result\err('error');     // Err<string>
```

### メソッドチェーン

モナドの値は、メソッドチェーンで変換できます。

```php
$result = Option\some(5)
    ->map(fn($x) => $x * 2)      // Some(10)
    ->filter(fn($x) => $x > 5)   // Some(10)
    ->map(fn($x) => "値: $x")    // Some("値: 10")
    ->unwrap();                   // "値: 10"
```

`None` や `Err` の場合、処理はスキップされます。

```php
$result = Option\none()
    ->map(fn($x) => $x * 2)      // None (スキップ)
    ->filter(fn($x) => $x > 5)   // None (スキップ)
    ->unwrapOr('デフォルト');     // "デフォルト"
```

## 次のステップ

- [Option モナド](/guide/option): Option の詳細な使い方
- [Result モナド](/guide/result): Result の詳細な使い方
- [実践例](/guide/examples): 実践的なユースケース
