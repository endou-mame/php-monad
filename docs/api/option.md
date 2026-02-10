# Option インターフェース

`Option<T>` は値の有無を表現する型です。Rust の `Option<T>` に着想を得ています。

## 定義

```php
namespace EndouMame\PhpMonad;

/**
 * @template T
 * @extends Monad<T>
 */
#[Sealed(permits: [Some::class, None::class])]
interface Option extends Monad
```

## 実装クラス

| クラス | 説明 |
|-------|------|
| `Some<T>` | 値 `T` を保持する |
| `None` | 値なし（enum） |

## 状態判定メソッド

### isSome

値があるかどうかを判定します。

```php
public function isSome(): bool;
```

PHPStan のアサーションにより、`isSome()` が `true` を返した後は `Some<T>` として型推論されます。

### isNone

値がないかどうかを判定します。

```php
public function isNone(): bool;
```

### isSomeAnd

値があり、かつ述語を満たすかどうかを判定します。

```php
/**
 * @param Closure(T): bool $predicate
 */
public function isSomeAnd(Closure $predicate): bool;
```

## 値取得メソッド

### expect

カスタムメッセージ付きで値を取得します。None の場合は RuntimeException をスローします。

```php
/**
 * @return T
 * @throws RuntimeException
 */
public function expect(string $message): mixed;
```

### unwrap

値を取得します。None の場合は RuntimeException をスローします。

```php
/**
 * @return T
 * @throws RuntimeException
 */
public function unwrap(): mixed;
```

### unwrapOr

値を取得するか、デフォルト値を返します。

```php
/**
 * @template U
 * @param U $default
 * @return T|U
 */
public function unwrapOr(mixed $default): mixed;
```

### unwrapOrElse

値を取得するか、関数を実行してデフォルト値を取得します。

```php
/**
 * @template U
 * @param Closure(): U $default
 * @return T|U
 */
public function unwrapOrElse(Closure $default): mixed;
```

### unwrapOrThrow

値を取得するか、指定した例外をスローします。

```php
/**
 * @template E of Throwable
 * @param E $exception
 * @return T
 * @throws E
 */
public function unwrapOrThrow(Throwable $exception): mixed;
```

## 変換メソッド

### map

値を変換します。None の場合はスキップされます。

```php
/**
 * @template U
 * @param Closure(T): U $callback
 * @return Option<U>
 */
public function map(Closure $callback): self;
```

### mapOr

値を変換するか、デフォルト値を返します。

```php
/**
 * @template U
 * @template V
 * @param Closure(T): U $callback
 * @param V $default
 * @return U|V
 */
public function mapOr(Closure $callback, mixed $default): mixed;
```

### mapOrElse

値を変換するか、関数を実行してデフォルト値を取得します。

```php
/**
 * @template U
 * @template V
 * @param Closure(T): U $callback
 * @param Closure(): V $default
 * @return U|V
 */
public function mapOrElse(Closure $callback, Closure $default): mixed;
```

### filter

述語を満たさない場合は None になります。

```php
/**
 * @param Closure(T): bool $predicate
 * @return Option<T>
 */
public function filter(Closure $predicate): self;
```

### inspect

値を検査（副作用を実行）し、自身を返します。

```php
/**
 * @param Closure(T): mixed $callback
 * @return $this
 */
public function inspect(Closure $callback): self;
```

## 論理演算メソッド

### and

両方が Some の場合、右側を返します。

```php
/**
 * @template U
 * @param Option<U> $right
 * @return Option<U>
 */
public function and(self $right): self;
```

### andThen

値を受け取り Option を返す関数を適用します。

```php
/**
 * @template U
 * @param Closure(T): Option<U> $right
 * @return Option<U>
 */
public function andThen(Closure $right): self;
```

### or

左が None なら右を返します。

```php
/**
 * @template U
 * @param Option<U> $right
 * @return Option<T|U>
 */
public function or(self $right): self;
```

### orElse

左が None なら関数を実行して右を取得します。

```php
/**
 * @template U
 * @param Closure(): Option<U> $right
 * @return Option<T|U>
 */
public function orElse(Closure $right): self;
```

### orThrow

None の場合に例外をスローし、Some の場合はそのまま返します。

```php
/**
 * @template F of Throwable
 * @param F $exception
 * @return $this
 * @throws F
 */
public function orThrow(Throwable $exception): self;
```

### xor

どちらか一方だけが Some の場合、その値を返します。

```php
/**
 * @template U
 * @param Option<U> $right
 * @return Option<T|U>
 */
public function xor(self $right): self;
```

## Result 変換メソッド

### okOr

Some を Ok に、None を Err に変換します。

```php
/**
 * @template E
 * @param E $err
 * @return Result<T, E>
 */
public function okOr(mixed $err): Result;
```

### okOrElse

Some を Ok に、None を関数で生成した Err に変換します。

```php
/**
 * @template E
 * @param Closure(): E $err
 * @return Result<T, E>
 */
public function okOrElse(Closure $err): Result;
```

## 関連項目

- [Option ヘルパー関数](/api/functions#option-ヘルパー関数)
- [Option モナド ガイド](/guide/option)
