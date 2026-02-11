# `PHP Monad` forked from [wiz-develop/php-monad](https://github.com/wiz-develop/php-monad)

[![Packagist Version](https://img.shields.io/packagist/v/endou-mame/php-monad)](https://packagist.org/packages/endou-mame/php-monad)
[![PHP Version](https://img.shields.io/packagist/php-v/endou-mame/php-monad)](https://packagist.org/packages/endou-mame/php-monad)
[![PHPStan](https://github.com/endou-mame/php-monad/actions/workflows/phpstan.yml/badge.svg)](https://github.com/endou-mame/php-monad/actions/workflows/phpstan.yml)
[![Documentation](https://github.com/endou-mame/php-monad/actions/workflows/deploy-docs.yml/badge.svg)](https://github.com/endou-mame/php-monad/actions/workflows/deploy-docs.yml)
[![License](https://img.shields.io/packagist/l/endou-mame/php-monad)](https://github.com/endou-mame/php-monad/blob/main/LICENSE)

関数型プログラミングのモナド概念を PHP で実装したライブラリです。Rust の `Option` / `Result` 型に着想を得ています。

## インストール

```bash
composer require endou-mame/php-monad
```

## 使用例

### Option

```php
use EndouMame\PhpMonad\Option;

$name = Option\fromValue($user['name'] ?? null)
    ->map(fn($n) => strtoupper($n))
    ->filter(fn($n) => strlen($n) > 0)
    ->unwrapOr('Anonymous');
```

### Result

```php
use EndouMame\PhpMonad\Result;

$result = Result\fromThrowable(
    fn() => json_decode($json, flags: JSON_THROW_ON_ERROR),
    fn($e) => "Parse error: {$e->getMessage()}"
);

$data = $result->map(fn($d) => $d['key'])->unwrapOr(null);
```

## ドキュメント

詳細なガイドと API リファレンスは [ドキュメントサイト](https://endou-mame.github.io/php-monad/) を参照してください。

## 要件

- PHP 8.3 以上

## ライセンス

MIT License
