# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## プロジェクト概要

PHP で関数型プログラミングのモナド概念を実装したライブラリ。Rust の `Option` と `Result` 型に着想を得ている。

## 開発コマンド

### 静的解析

```bash
vendor/bin/phpstan --configuration=phpstan.neon.dist --memory-limit=1G
```

### テスト

```bash
# 全テスト実行
vendor/bin/phpunit --configuration=phpunit.xml.dist --testdox --colors=always

# 特定テストファイルの実行
vendor/bin/phpunit tests/Unit/Option/SomeTest.php

# 特定テストメソッドの実行
vendor/bin/phpunit --filter testMethodName
```

### コードフォーマット

```bash
vendor/bin/php-cs-fixer fix
```

### ドキュメント

```bash
# textlint チェック
cd docs && npm run textlint

# textlint 自動修正
cd docs && npm run textlint:fix

# VitePress 開発サーバー
cd docs && npm run dev

# VitePress ビルド
cd docs && npm run build
```

## アーキテクチャ

### モナド階層

```
Monad (interface)
├── Option (interface) - Maybe モナド
│   ├── Some<T>        - 値を保持
│   └── None           - 値なし (enum)
└── Result (interface) - Either モナド
    ├── Ok<T>          - 成功値
    └── Err<E>         - エラー値
```

### ファイル構成

- `src/Monad.php` - 基底インターフェース (`unit`, `andThen`)
- `src/Option.php`, `src/Result.php` - sealed インターフェース
- `src/Option/`, `src/Result/` - 具象クラスとヘルパー関数
- `src/functions.php` - 全ヘルパー関数のオートロード

### ヘルパー関数の使用法

```php
use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Result;

// Option
Option\some(42);
Option\none();
Option\fromValue($value);
Option\of($callback);
Option\tryOf($callback, null, Exception::class);
Option\flatten($nested);
Option\transpose($optionOfResult);

// Result
Result\ok(42);
Result\err('error');
Result\fromThrowable($closure, $errorHandler);
Result\flatten($nested);
Result\transpose($resultOfOption);
Result\combine(...$results);
```

## コーディング規約

- PHP 8.3 以上必須
- PHPStan レベル max
- `#[Sealed]` 属性で継承制限
- `#[Override]` 属性必須
- `readonly class` で不変性を保証
- テンプレート型による型安全性 (`@template T`)

## テスト構成

- `tests/Unit/Option/` - Option 関連のテスト (Some, None, functions)
- `tests/Unit/Result/` - Result 関連のテスト (Ok, Err, functions)
- 各具象クラスに対応するテストファイルが存在
