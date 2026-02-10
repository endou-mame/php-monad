# Option モナド

Option は「値の有無」を型で表現するモナドです。Rust の `Option<T>` に着想を得ています。

## 概要

Option には 2 つの状態があります。

- `Some<T>`: 値 `T` を保持している状態
- `None`: 値がない状態

```php
use EndouMame\PhpMonad\Option;

$some = Option\some(42);    // Some<int> - 値あり
$none = Option\none();      // None - 値なし
```

## Option を作成する

### some / none

最も基本的な作成方法です。

```php
use EndouMame\PhpMonad\Option;

$value = Option\some('hello');   // Some<string>
$empty = Option\none();          // None
```

### fromValue

既存の値を Option に変換します。第 2 引数で「None とみなす値」を指定できます。

```php
use EndouMame\PhpMonad\Option;

// null は None になる（デフォルト）
$opt1 = Option\fromValue($user);           // $user が null なら None、そうでなければ Some

// 0 を None とみなす
$opt2 = Option\fromValue($count, 0);       // $count が 0 なら None

// 空文字を None とみなす
$opt3 = Option\fromValue($name, '');       // $name が '' なら None
```

### of

関数の戻り値を Option に変換します。

```php
use EndouMame\PhpMonad\Option;

$opt = Option\of(fn() => getUser($id));

// 戻り値が null なら None、そうでなければ Some
```

### tryOf

関数を実行し、例外が発生した場合は None を返します。

```php
use EndouMame\PhpMonad\Option;

$date = Option\tryOf(
    fn() => new DateTimeImmutable($dateString),
    null,
    \DateMalformedStringException::class
);

// 日付のパースに失敗したら None
```

## 状態を判定する

### isSome / isNone

```php
$opt = Option\some(42);

if ($opt->isSome()) {
    // 値がある場合の処理
}

if ($opt->isNone()) {
    // 値がない場合の処理
}
```

### isSomeAnd

値があり、かつ述語を満たす場合に `true` を返します。

```php
$opt = Option\some(10);

$opt->isSomeAnd(fn($x) => $x > 5);   // true
$opt->isSomeAnd(fn($x) => $x > 15);  // false

Option\none()->isSomeAnd(fn($x) => true);   // false
```

## 値を取得する

### unwrap

値を取得します。None の場合は例外をスローします。

```php
$opt = Option\some(42);
$value = $opt->unwrap();  // 42

$none = Option\none();
$none->unwrap();  // RuntimeException をスロー
```

### expect

カスタムエラーメッセージで値を取得します。

```php
$opt = Option\some(42);
$value = $opt->expect('値が必要です');  // 42

$none = Option\none();
$none->expect('値が必要です');  // RuntimeException: 値が必要です
```

### unwrapOr

デフォルト値を指定して取得します。

```php
$opt = Option\some(42);
$opt->unwrapOr(0);   // 42

$none = Option\none();
$none->unwrapOr(0);  // 0
```

### unwrapOrElse

デフォルト値を遅延評価で取得します。

```php
$none = Option\none();
$none->unwrapOrElse(fn() => expensiveCalculation());

// None の場合のみ expensiveCalculation() が実行される
```

### unwrapOrThrow

None の場合に指定した例外をスローします。

```php
$none = Option\none();
$none->unwrapOrThrow(new NotFoundException('ユーザーが見つかりません'));
```

## 値を変換する

### map

値を変換します。None の場合はスキップされます。

```php
$opt = Option\some(5);

$result = $opt
    ->map(fn($x) => $x * 2)     // Some(10)
    ->map(fn($x) => "値: $x");  // Some("値: 10")

$none = Option\none();
$none->map(fn($x) => $x * 2);   // None（スキップ）
```

### mapOr

値を変換するか、デフォルト値を返します。

```php
$opt = Option\some(5);
$opt->mapOr(fn($x) => $x * 2, 0);   // 10

$none = Option\none();
$none->mapOr(fn($x) => $x * 2, 0);  // 0
```

### mapOrElse

値を変換するか、デフォルト値を遅延評価で取得します。

```php
$none = Option\none();
$result = $none->mapOrElse(
    fn($x) => $x * 2,
    fn() => calculateDefault()
);
```

### filter

述語を満たさない場合は None になります。

```php
$opt = Option\some(10);

$opt->filter(fn($x) => $x > 5);   // Some(10)
$opt->filter(fn($x) => $x > 15);  // None
```

### inspect

値を検査（副作用を実行）し、自身を返します。デバッグに便利です。

```php
$result = Option\some(42)
    ->inspect(fn($x) => var_dump($x))  // int(42) を出力
    ->map(fn($x) => $x * 2)
    ->unwrap();
```

## 論理演算

### and

両方が Some の場合、右側の Option を返します。

```php
$a = Option\some(1);
$b = Option\some(2);

$a->and($b);  // Some(2)
$a->and(Option\none());  // None

Option\none()->and($b);  // None
```

### andThen

値を受け取り Option を返す関数を適用します（flatMap）。

```php
function parsePositive(int $x): Option {
    return $x > 0 ? Option\some($x) : Option\none();
}

$opt = Option\some(5);
$opt->andThen(fn($x) => parsePositive($x));  // Some(5)

$opt2 = Option\some(-1);
$opt2->andThen(fn($x) => parsePositive($x));  // None
```

### or

左が None なら右を返します。

```php
$a = Option\some(1);
$b = Option\some(2);

$a->or($b);       // Some(1)
Option\none()->or($b);   // Some(2)
```

### orElse

左が None なら右を遅延評価します。

```php
$none = Option\none();
$none->orElse(fn() => Option\some(fetchDefault()));
```

### orThrow

None の場合に例外をスローし、Some の場合はそのまま返します。

```php
$opt = Option\some(42);
$opt->orThrow(new Exception('エラー'));  // Some(42)

$none = Option\none();
$none->orThrow(new Exception('エラー'));  // Exception をスロー
```

### xor

どちらか一方だけが Some の場合、その値を返します。

```php
$a = Option\some(1);
$b = Option\some(2);

$a->xor($b);        // None（両方 Some なので）
$a->xor(Option\none());    // Some(1)
Option\none()->xor($b);    // Some(2)
Option\none()->xor(Option\none()); // None
```

## Result への変換

### okOr

Some を Ok に、None を指定したエラーの Err に変換します。

```php
use EndouMame\PhpMonad\Option;

$opt = Option\some(42);
$result = $opt->okOr('エラー');  // Ok(42)

$none = Option\none();
$result = $none->okOr('エラー');  // Err('エラー')
```

### okOrElse

エラー値を遅延評価します。

```php
$none = Option\none();
$result = $none->okOrElse(fn() => createError());
```

## ユーティリティ関数

### flatten

`Option<Option<T>>` を `Option<T>` に平坦化します。

```php
use EndouMame\PhpMonad\Option;

$nested = Option\some(Option\some(42));
Option\flatten($nested);  // Some(42)

$nested2 = Option\some(Option\none());
Option\flatten($nested2);  // None
```

### transpose

`Option<Result<T, E>>` を `Result<Option<T>, E>` に変換します。

```php
use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Result;

Option\transpose(Option\some(Result\ok(42)));    // Ok(Some(42))
Option\transpose(Option\some(Result\err('e')));  // Err('e')
Option\transpose(Option\none());                  // Ok(None)
```

## イテレーション

Option は `IteratorAggregate` を実装しているため、foreach で使用できます。

```php
$opt = Option\some(42);

foreach ($opt as $value) {
    echo $value;  // 42
}

$none = Option\none();

foreach ($none as $value) {
    // 実行されない
}
```

## 典型的なパターン

### null チェックの置き換え

```php
// Before
$name = null;
if ($user !== null) {
    $profile = $user->getProfile();
    if ($profile !== null) {
        $name = $profile->getName();
    }
}
$displayName = $name ?? 'Anonymous';

// After
$displayName = Option\fromValue($user)
    ->map(fn($u) => $u->getProfile())
    ->map(fn($p) => $p->getName())
    ->unwrapOr('Anonymous');
```

### 配列からの安全な取得

```php
function getArrayValue(array $arr, string $key): Option {
    return Option\fromValue($arr[$key] ?? null);
}

$value = getArrayValue($config, 'timeout')
    ->filter(fn($t) => $t > 0)
    ->unwrapOr(30);
```
