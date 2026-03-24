# ヘルパー関数

PHP Monad は、モナドを簡単に作成・操作するためのヘルパー関数を提供しています。

## Option ヘルパー関数

```php
use EndouMame\PhpMonad\Option;

// 利用可能な関数
Option\some($value);
Option\none();
Option\fromValue($value, $noneValue = null);
Option\of($callback, $noneValue = null);
Option\tryOf($callback, $noneValue = null, $exceptionClass = Exception::class);
Option\flatten($option);
Option\transpose($option);
```

### some

値を持つ Some を作成します。

```php
/**
 * @template T
 * @param T $value
 * @return Some<T>
 */
function some(mixed $value): Some
```

#### 使用例

```php
$opt = Option\some(42);        // Some<int>
$opt = Option\some('hello');   // Some<string>
$opt = Option\some([1, 2, 3]); // Some<array>
```

### none

値を持たない None を作成します。

```php
function none(): None
```

#### 使用例

```php
$opt = Option\none();  // None
```

### fromValue

値を Option に変換します。指定した値（デフォルトは null）と等しい場合は None になります。

```php
/**
 * @template U
 * @template NoneValue
 * @param U $value
 * @param NoneValue|null $noneValue
 * @return Option<U>
 */
function fromValue($value, mixed $noneValue = null): Option
```

#### 使用例

```php
$opt = Option\fromValue($user);              // null なら None
$opt = Option\fromValue($count, 0);          // 0 なら None
$opt = Option\fromValue($name, '');          // 空文字なら None
$opt = Option\fromValue($data, false);       // false なら None
```

### of

関数を実行し、結果を Option に変換します。

```php
/**
 * @template U
 * @template NoneValue
 * @param callable(): U $callback
 * @param NoneValue|null $noneValue
 * @return Option<U>
 */
function of(callable $callback, mixed $noneValue = null): Option
```

#### 使用例

```php
$opt = Option\of(fn() => getUser($id));
$opt = Option\of(fn() => findByKey($array, $key), false);
```

### tryOf

関数を実行し、例外が発生した場合は None を返します。

```php
/**
 * @template U
 * @template NoneValue
 * @template E of Throwable
 * @param callable(): U $callback
 * @param NoneValue|null $noneValue
 * @param class-string<E> $exceptionClass
 * @return Option<U>
 * @throws Throwable 指定したクラス以外の例外は再スロー
 */
function tryOf(
    callable $callback,
    mixed $noneValue = null,
    string $exceptionClass = Exception::class
): Option
```

#### 使用例

```php
$date = Option\tryOf(
    fn() => new DateTimeImmutable($input),
    null,
    DateMalformedStringException::class
);

$json = Option\tryOf(
    fn() => json_decode($str, flags: JSON_THROW_ON_ERROR),
    null,
    JsonException::class
);
```

### flatten

`Option<Option<T>>` を `Option<T>` に平坦化します。

```php
/**
 * @template U
 * @param Option<Option<U>> $option
 * @return Option<U>
 */
function flatten(Option $option): Option
```

#### 使用例

```php
$nested = Option\some(Option\some(42));
$flat = Option\flatten($nested);  // Some(42)

$nested = Option\some(Option\none());
$flat = Option\flatten($nested);  // None
```

### transpose

`Option<Result<T, E>>` を `Result<Option<T>, E>` に変換します。

```php
/**
 * @template U
 * @template E
 * @param Option<Result<U, E>> $option
 * @return Result<Option<U>, E>
 */
function transpose(Option $option): Result
```

#### 使用例

```php
use EndouMame\PhpMonad\Result;

Option\transpose(Option\some(Result\ok(42)));    // Ok(Some(42))
Option\transpose(Option\some(Result\err('e')));  // Err('e')
Option\transpose(Option\none());                  // Ok(None)
```

## Option パイプライン関数 {#option-pipeline}

PHP 8.5 のパイプライン演算子（`|>`）で使用するための関数群です。
各関数は引数を受け取り、`Option` を処理する `Closure` を返します。

```php
use EndouMame\PhpMonad\Option;

// 利用可能な関数
Option\map($callback);
Option\andThen($callback);
Option\orElse($callback);
Option\filter($predicate);
Option\inspect($callback);
Option\unwrapOr($default);
Option\unwrapOrElse($callback);
Option\expect($message);
Option\okOr($err);
Option\okOrElse($err);
```

### map

Some の値を変換する `Closure` を返します。

```php
/**
 * @param Closure(mixed): U $callback
 * @return Closure(Option): Option<U>
 */
function map(Closure $callback): Closure
```

#### 使用例

```php
$result = Option\some(5)
    |> Option\map(fn($x) => $x * 2);  // Some(10)

Option\none()
    |> Option\map(fn($x) => $x * 2);  // None
```

### andThen

Option を返す関数でチェーンする `Closure` を返します。

```php
/**
 * @param Closure(mixed): Option<U> $callback
 * @return Closure(Option): Option<U>
 */
function andThen(Closure $callback): Closure
```

#### 使用例

```php
$result = Option\some(5)
    |> Option\andThen(fn($x) => $x > 0 ? Option\some($x) : Option\none());
// Some(5)
```

### orElse

None の場合に代替の Option を返す `Closure` を返します。

```php
/**
 * @param Closure(): Option<U> $callback
 * @return Closure(Option): Option
 */
function orElse(Closure $callback): Closure
```

#### 使用例

```php
$result = Option\none()
    |> Option\orElse(fn() => Option\some(42));  // Some(42)
```

### filter

述語を満たさない場合は None にする `Closure` を返します。

```php
/**
 * @param Closure(mixed): bool $predicate
 * @return Closure(Option): Option
 */
function filter(Closure $predicate): Closure
```

#### 使用例

```php
$result = Option\some(10)
    |> Option\filter(fn($x) => $x > 5);   // Some(10)

$result = Option\some(3)
    |> Option\filter(fn($x) => $x > 5);   // None
```

### inspect

Some の値で副作用を実行し、Option をそのまま返す `Closure` を返します。

```php
/**
 * @param Closure(mixed): mixed $callback
 * @return Closure(Option): Option
 */
function inspect(Closure $callback): Closure
```

#### 使用例

```php
$result = Option\some(42)
    |> Option\inspect(fn($x) => logger()->info("値: $x"))
    |> Option\map(fn($x) => $x * 2);
```

### unwrapOr

Some の値またはデフォルト値を返す `Closure` を返します。

```php
/**
 * @param U $default
 * @return Closure(Option): mixed
 */
function unwrapOr(mixed $default): Closure
```

#### 使用例

```php
Option\some(42) |> Option\unwrapOr(0);  // 42
Option\none()   |> Option\unwrapOr(0);  // 0
```

### unwrapOrElse

Some の値またはデフォルト値を遅延評価で返す `Closure` を返します。

```php
/**
 * @param Closure(): mixed $callback
 * @return Closure(Option): mixed
 */
function unwrapOrElse(Closure $callback): Closure
```

#### 使用例

```php
Option\none() |> Option\unwrapOrElse(fn() => expensiveDefault());
```

### expect

Some の値を返すか、メッセージ付きの例外をスローする `Closure` を返します。

```php
/**
 * @return Closure(Option): mixed
 */
function expect(string $message): Closure
```

#### 使用例

```php
Option\some(42) |> Option\expect('値が必要です');  // 42
Option\none()   |> Option\expect('値が必要です');  // RuntimeException
```

### okOr

Option を Result に変換する `Closure` を返します。

```php
/**
 * @param E $err
 * @return Closure(Option): Result<mixed, E>
 */
function okOr(mixed $err): Closure
```

#### 使用例

```php
Option\some(42) |> Option\okOr('not found');  // Ok(42)
Option\none()   |> Option\okOr('not found');  // Err('not found')
```

### okOrElse

Option を Result に変換する `Closure` を返します。エラー値は遅延評価です。

```php
/**
 * @param Closure(): E $err
 * @return Closure(Option): Result<mixed, E>
 */
function okOrElse(Closure $err): Closure
```

#### 使用例

```php
Option\none() |> Option\okOrElse(fn() => new NotFoundException());
```

## Result ヘルパー関数

```php
use EndouMame\PhpMonad\Result;

// 利用可能な関数
Result\ok($value = true);
Result\err($value);
Result\fromThrowable($closure, $errorHandler);
Result\flatten($result);
Result\transpose($result);
Result\map_all($fn, ...$results);
Result\flat_map_all($fn, ...$results);
Result\combine(...$results);
```

### ok

成功値を持つ Ok を作成します。

```php
/**
 * @template U
 * @param U $value
 * @return Ok<U>
 */
function ok(mixed $value = true): Ok
```

#### 使用例

```php
$result = Result\ok(42);       // Ok<int>
$result = Result\ok('data');   // Ok<string>
$result = Result\ok();         // Ok<true>
```

### err

エラー値を持つ Err を作成します。

```php
/**
 * @template F
 * @param F $value
 * @return Err<F>
 */
function err(mixed $value): Err
```

#### 使用例

```php
$result = Result\err('エラー');                // Err<string>
$result = Result\err(['code' => 'E001']);      // Err<array>
$result = Result\err(new Exception('失敗'));   // Err<Exception>
```

### fromThrowable

例外をスローする可能性のある処理を Result に変換します。

```php
/**
 * @template T
 * @template E
 * @param Closure(): T $closure
 * @param Closure(Throwable): E $errorHandler
 * @return Result<T, E>
 */
function fromThrowable(Closure $closure, Closure $errorHandler): Result
```

#### 使用例

```php
$result = Result\fromThrowable(
    fn() => json_decode($json, flags: JSON_THROW_ON_ERROR),
    fn($e) => "パースエラー: {$e->getMessage()}"
);

$result = Result\fromThrowable(
    fn() => file_get_contents($path),
    fn($e) => ['type' => 'io_error', 'message' => $e->getMessage()]
);
```

### flatten

`Result<Result<T, E>, E>` を `Result<T, E>` に平坦化します。

```php
/**
 * @template T
 * @template E
 * @param Result<Result<T, E>, E> $result
 * @return Result<T, E>
 */
function flatten(Result $result): Result
```

#### 使用例

```php
$nested = Result\ok(Result\ok(42));
$flat = Result\flatten($nested);  // Ok(42)

$nested = Result\ok(Result\err('inner'));
$flat = Result\flatten($nested);  // Err('inner')

$nested = Result\err('outer');
$flat = Result\flatten($nested);  // Err('outer')
```

### transpose

`Result<Option<T>, E>` を `Option<Result<T, E>>` に変換します。

```php
/**
 * @template U
 * @template F
 * @param Result<Option<U>, F> $result
 * @return Option<Result<U, F>>
 */
function transpose(Result $result): Option
```

#### 使用例

```php
use EndouMame\PhpMonad\Option;

Result\transpose(Result\ok(Option\some(42)));   // Some(Ok(42))
Result\transpose(Result\ok(Option\none()));     // None
Result\transpose(Result\err('error'));          // Some(Err('error'))
```

### map_all

複数の Result がすべて Ok の場合にコールバックを適用し、結果を Ok で返します。1 つでも Err があれば最初の Err を返します。

```php
/**
 * @template E
 * @param Closure $fn
 * @param Result<mixed, E> ...$results
 * @return Result<mixed, E>
 */
function map_all(Closure $fn, Result ...$results): Result
```

#### 使用例

```php
// すべて Ok の場合、コールバックの結果が Ok で返される
$result = Result\map_all(
    fn(int $a, int $b, int $c) => $a + $b + $c,
    Result\ok(1),
    Result\ok(2),
    Result\ok(3),
);
// Ok(6)

// 1 つでも Err があれば最初の Err を返す
$result = Result\map_all(
    fn(int $a, int $b) => $a + $b,
    Result\ok(1),
    Result\err('エラー1'),
    Result\err('エラー2'),
);
// Err('エラー1')
```

### flat_map_all

複数の Result がすべて Ok の場合に Result を返すコールバックを適用します。1 つでも Err があれば最初の Err を返します。

`map_all` との違いは、コールバック自体が Result を返す点です。

```php
/**
 * @template E
 * @param Closure $fn
 * @param Result<mixed, E> ...$results
 * @return Result<mixed, E>
 */
function flat_map_all(Closure $fn, Result ...$results): Result
```

#### 使用例

```php
// コールバックが Ok を返す場合
$result = Result\flat_map_all(
    fn(int $a, int $b) => Result\ok($a + $b),
    Result\ok(1),
    Result\ok(2),
);
// Ok(3)

// コールバックが Err を返す場合
$result = Result\flat_map_all(
    fn(int $a, int $b) => Result\err('計算エラー'),
    Result\ok(1),
    Result\ok(2),
);
// Err('計算エラー')
```

### combine

複数の Result を検証し、全て成功なら Ok、1 つでも失敗なら全エラーを Err で返します。

```php
/**
 * @template T
 * @template E
 * @param Result<T, E> ...$results
 * @return Result<bool, non-empty-list<E>>
 */
function combine(Result ...$results): Result
```

#### 使用例

```php
// 全て成功
$result = Result\combine(Result\ok(1), Result\ok(2), Result\ok(3));
$result->isOk();  // true
$result->unwrap();  // true

// 一部失敗
$result = Result\combine(
    Result\ok(1),
    Result\err('エラー1'),
    Result\ok(2),
    Result\err('エラー2')
);

$result->isErr();  // true
$result->unwrapErr();  // ['エラー1', 'エラー2']
```

## Result パイプライン関数 {#result-pipeline}

PHP 8.5 のパイプライン演算子（`|>`）で使用するための関数群です。
各関数は引数を受け取り、`Result` を処理する `Closure` を返します。

```php
use EndouMame\PhpMonad\Result;

// 利用可能な関数
Result\map($callback);
Result\mapErr($callback);
Result\andThen($callback);
Result\orElse($callback);
Result\inspect($callback);
Result\inspectErr($callback);
Result\unwrapOr($default);
Result\unwrapOrElse($callback);
Result\expect($message);
```

### map

Ok の値を変換する `Closure` を返します。

```php
/**
 * @param Closure(mixed): U $callback
 * @return Closure(Result): Result<U, mixed>
 */
function map(Closure $callback): Closure
```

#### 使用例

```php
$result = Result\ok(5)
    |> Result\map(fn($x) => $x * 2);  // Ok(10)

Result\err('error')
    |> Result\map(fn($x) => $x * 2);  // Err('error')
```

### mapErr

Err の値を変換する `Closure` を返します。

```php
/**
 * @param Closure(mixed): F $callback
 * @return Closure(Result): Result<mixed, F>
 */
function mapErr(Closure $callback): Closure
```

#### 使用例

```php
$result = Result\err('not found')
    |> Result\mapErr(fn($e) => strtoupper($e));  // Err('NOT FOUND')
```

### andThen

Result を返す関数でチェーンする `Closure` を返します。

```php
/**
 * @param Closure(mixed): Result<U, F> $callback
 * @return Closure(Result): Result<U, mixed>
 */
function andThen(Closure $callback): Closure
```

#### 使用例

```php
$result = Result\ok(10)
    |> Result\andThen(fn($x) => $x > 0 ? Result\ok($x) : Result\err('負の値'));
// Ok(10)
```

### orElse

Err からリカバリする `Closure` を返します。

```php
/**
 * @param Closure(mixed): Result<mixed, F> $callback
 * @return Closure(Result): Result<mixed, F>
 */
function orElse(Closure $callback): Closure
```

#### 使用例

```php
$result = Result\err('primary failed')
    |> Result\orElse(fn($e) => Result\ok('fallback'));  // Ok('fallback')
```

### inspect

Ok の値で副作用を実行し、Result をそのまま返す `Closure` を返します。

```php
/**
 * @param Closure(mixed): mixed $callback
 * @return Closure(Result): Result
 */
function inspect(Closure $callback): Closure
```

#### 使用例

```php
$result = Result\ok(42)
    |> Result\inspect(fn($x) => logger()->info("成功: $x"))
    |> Result\map(fn($x) => $x * 2);
```

### inspectErr

Err の値で副作用を実行し、Result をそのまま返す `Closure` を返します。

```php
/**
 * @param Closure(mixed): mixed $callback
 * @return Closure(Result): Result
 */
function inspectErr(Closure $callback): Closure
```

#### 使用例

```php
$result = Result\err('error')
    |> Result\inspectErr(fn($e) => logger()->error("失敗: $e"));
```

### unwrapOr

Ok の値またはデフォルト値を返す `Closure` を返します。

```php
/**
 * @param U $default
 * @return Closure(Result): mixed
 */
function unwrapOr(mixed $default): Closure
```

#### 使用例

```php
Result\ok(42)       |> Result\unwrapOr(0);  // 42
Result\err('error') |> Result\unwrapOr(0);  // 0
```

### unwrapOrElse

Ok の値またはデフォルト値を遅延評価で返す `Closure` を返します。

```php
/**
 * @param Closure(mixed): mixed $callback
 * @return Closure(Result): mixed
 */
function unwrapOrElse(Closure $callback): Closure
```

#### 使用例

```php
Result\err('error') |> Result\unwrapOrElse(fn($e) => "recovered: $e");
```

### expect

Ok の値を返すか、メッセージ付きの例外をスローする `Closure` を返します。

```php
/**
 * @return Closure(Result): mixed
 */
function expect(string $message): Closure
```

#### 使用例

```php
Result\ok(42)       |> Result\expect('値が必要です');  // 42
Result\err('error') |> Result\expect('値が必要です');  // RuntimeException
```

::: tip バリデーションに便利
`combine` はフォームバリデーションで全エラーを収集するのに便利です。

```php
$result = Result\combine(
    validateEmail($email),
    validatePassword($password),
    validateAge($age)
);

if ($result->isErr()) {
    $errors = $result->unwrapErr();
    // 全てのエラーメッセージを表示
}
```

バリデーション結果から値を合成したい場合は `map_all` / `flat_map_all` を使います。

```php
$user = Result\map_all(
    fn(string $email, string $password, int $age) => new User($email, $password, $age),
    validateEmail($email),
    validatePassword($password),
    validateAge($age),
);
```
:::
