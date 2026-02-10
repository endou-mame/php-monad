# Result インターフェース

`Result<T, E>` は成功または失敗を表現する型です。Rust の `Result<T, E>` に着想を得ています。

## 定義

```php
namespace EndouMame\PhpMonad;

/**
 * @template T
 * @template E
 * @extends Monad<T>
 */
#[Sealed(permits: [Ok::class, Err::class])]
interface Result extends Monad
```

## 実装クラス

| クラス | 説明 |
|-------|------|
| `Ok<T>` | 成功値 `T` を保持する |
| `Err<E>` | エラー値 `E` を保持する |

## 状態判定メソッド

### isOk

成功かどうかを判定します。

```php
public function isOk(): bool;
```

PHPStan のアサーションにより、`isOk()` が `true` を返した後は `Ok<T>` として型推論されます。

### isErr

失敗かどうかを判定します。

```php
public function isErr(): bool;
```

### isOkAnd

成功かつ述語を満たすかどうかを判定します。

```php
/**
 * @param Closure(T): bool $predicate
 */
public function isOkAnd(Closure $predicate): bool;
```

### isErrAnd

失敗かつ述語を満たすかどうかを判定します。

```php
/**
 * @param Closure(E): bool $predicate
 */
public function isErrAnd(Closure $predicate): bool;
```

## 値取得メソッド

### expect

カスタムメッセージ付きで成功値を取得します。Err の場合は RuntimeException をスローします。

```php
/**
 * @return T
 * @throws RuntimeException
 */
public function expect(string $message): mixed;
```

### unwrap

成功値を取得します。Err の場合は例外をスローします。

```php
/**
 * @return T
 * @throws Throwable
 */
public function unwrap(): mixed;
```

### unwrapErr

エラー値を取得します。Ok の場合は RuntimeException をスローします。

```php
/**
 * @return E
 * @throws RuntimeException
 */
public function unwrapErr(): mixed;
```

### unwrapOr

成功値を取得するか、デフォルト値を返します。

```php
/**
 * @template U
 * @param U $default
 * @return T|U
 */
public function unwrapOr(mixed $default): mixed;
```

### unwrapOrElse

成功値を取得するか、エラー値から計算したデフォルト値を返します。

```php
/**
 * @template U
 * @param Closure(E): U $default
 * @return T|U
 */
public function unwrapOrElse(Closure $default): mixed;
```

### unwrapOrThrow

成功値を取得するか、指定した例外をスローします。

```php
/**
 * @template F of Throwable
 * @param F $exception
 * @return T
 * @throws F
 */
public function unwrapOrThrow(Throwable $exception): mixed;
```

## 変換メソッド

### map

成功値を変換します。Err の場合はスキップされます。

```php
/**
 * @template U
 * @param Closure(T): U $callback
 * @return Result<U, E>
 */
public function map(Closure $callback): self;
```

### mapErr

エラー値を変換します。Ok の場合はスキップされます。

```php
/**
 * @template F
 * @param Closure(E): F $callback
 * @return Result<T, F>
 */
public function mapErr(Closure $callback): self;
```

### mapOr

成功値を変換するか、デフォルト値を返します。

```php
/**
 * @template U
 * @param Closure(T): U $callback
 * @param U $default
 * @return U
 */
public function mapOr(Closure $callback, mixed $default): mixed;
```

### mapOrElse

成功値を変換するか、エラー値からデフォルト値を計算します。

```php
/**
 * @template U
 * @param Closure(T): U $callback
 * @param Closure(E): U $default
 * @return U
 */
public function mapOrElse(Closure $callback, Closure $default): mixed;
```

### inspect

成功値を検査（副作用を実行）し、自身を返します。

```php
/**
 * @param Closure(T): mixed $callback
 * @return $this
 */
public function inspect(Closure $callback): self;
```

### inspectErr

エラー値を検査（副作用を実行）し、自身を返します。

```php
/**
 * @param Closure(E): mixed $callback
 * @return $this
 */
public function inspectErr(Closure $callback): self;
```

## 論理演算メソッド

### and

両方が Ok の場合、右側を返します。

```php
/**
 * @template U
 * @param Result<U, E> $right
 * @return Result<U, E>
 */
public function and(self $right): self;
```

### andThen

成功値を受け取り Result を返す関数を適用します。

```php
/**
 * @template U
 * @template F
 * @param Closure(T): Result<U, F> $right
 * @return Result<U, E|F>
 */
public function andThen(Closure $right): self;
```

### or

左が Err なら右を返します。

```php
/**
 * @template F
 * @param Result<T, F> $right
 * @return Result<T, F>
 */
public function or(self $right): self;
```

### orElse

左が Err なら関数を実行して右を取得します。

```php
/**
 * @template F
 * @param Closure(E): Result<T, F> $right
 * @return Result<T, F>
 */
public function orElse(Closure $right): self;
```

### orThrow

Err の場合に例外をスローし、Ok の場合はそのまま返します。

```php
/**
 * @template F of Throwable
 * @param F $exception
 * @return $this
 * @throws F
 */
public function orThrow(Throwable $exception): self;
```

## Option 変換メソッド

### ok

Ok を Some に、Err を None に変換します。

```php
/**
 * @return Option<T>
 */
public function ok(): Option;
```

### err

Err を Some に、Ok を None に変換します。

```php
/**
 * @return Option<E>
 */
public function err(): Option;
```

## 廃止予定メソッド

### match

::: warning 廃止予定
このメソッドは非推奨です。代わりに `mapOrElse` を使用してください。
<!-- textlint-disable -->
:::
<!-- textlint-enable -->

```php
/**
 * @deprecated
 * @template U
 * @template V
 * @param Closure(T): U $okFn
 * @param Closure(E): V $errFn
 * @return U|V
 */
#[Deprecated]
public function match(Closure $okFn, Closure $errFn): mixed;
```

## 関連項目

- [Result ヘルパー関数](/api/functions#result-ヘルパー関数)
- [Result モナド ガイド](/guide/result)
