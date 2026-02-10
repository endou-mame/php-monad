# 実践例

このページでは、PHP Monad を使った実践的なユースケースを紹介します。

## null 安全な値の処理

### 配列からの安全な値取得

```php
use EndouMame\PhpMonad\Option;

function getConfig(array $config, string $key): Option {
    return Option\fromValue($config[$key] ?? null);
}

$timeout = getConfig($config, 'database.timeout')
    ->filter(fn($t) => is_numeric($t) && $t > 0)
    ->map(fn($t) => (int)$t)
    ->unwrapOr(30);
```

### ネストしたオブジェクトの安全なアクセス

```php
use EndouMame\PhpMonad\Option;

class User {
    public function __construct(
        public ?Profile $profile = null
    ) {}
}

class Profile {
    public function __construct(
        public ?Address $address = null
    ) {}
}

class Address {
    public function __construct(
        public string $city
    ) {}
}

$city = Option\fromValue($user)
    ->map(fn($u) => $u->profile)
    ->map(fn($p) => $p?->address)
    ->map(fn($a) => $a?->city)
    ->unwrapOr('Unknown');
```

## API レスポンスの処理

### HTTP リクエストの Result ラッピング

```php
use EndouMame\PhpMonad\Result;

function fetchUser(int $id): Result {
    return Result\fromThrowable(
        fn() => file_get_contents("https://api.example.com/users/$id"),
        fn($e) => ['type' => 'network_error', 'message' => $e->getMessage()]
    )
    ->andThen(fn($body) => Result\fromThrowable(
        fn() => json_decode($body, true, flags: JSON_THROW_ON_ERROR),
        fn($e) => ['type' => 'parse_error', 'message' => $e->getMessage()]
    ))
    ->andThen(fn($data) => isset($data['id'])
        ? Result\ok($data)
        : Result\err(['type' => 'invalid_response', 'message' => 'Missing id field'])
    );
}

// 使用例
$user = fetchUser(123)
    ->map(fn($data) => new UserDTO($data['id'], $data['name']))
    ->inspectErr(fn($e) => logger()->error('Failed to fetch user', $e))
    ->unwrapOr(null);
```

### レスポンスキャッシュとフォールバック

```php
use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Result;

function getCachedUser(int $id): Option {
    $cached = cache()->get("user:$id");
    return Option\fromValue($cached);
}

function fetchAndCacheUser(int $id): Result {
    return fetchUser($id)
        ->inspect(fn($user) => cache()->set("user:$id", $user, 3600));
}

// キャッシュを優先し、なければ API から取得
$user = getCachedUser($id)
    ->map(fn($data) => Result\ok($data))
    ->unwrapOrElse(fn() => fetchAndCacheUser($id))
    ->unwrapOr(null);
```

## フォーム検証

### 単一フィールドの検証

```php
use EndouMame\PhpMonad\Result;

function validateEmail(string $email): Result {
    if (empty($email)) {
        return Result\err('メールアドレスは必須です');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return Result\err('メールアドレスの形式が正しくありません');
    }
    return Result\ok($email);
}

function validatePassword(string $password): Result {
    if (strlen($password) < 8) {
        return Result\err('パスワードは 8 文字以上である必要があります');
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return Result\err('パスワードには大文字を含める必要があります');
    }
    if (!preg_match('/[0-9]/', $password)) {
        return Result\err('パスワードには数字を含める必要があります');
    }
    return Result\ok($password);
}
```

### フォーム全体の検証

```php
use EndouMame\PhpMonad\Result;

function validateRegistrationForm(array $data): Result {
    return Result\combine(
        validateEmail($data['email'] ?? ''),
        validatePassword($data['password'] ?? ''),
        validateName($data['name'] ?? '')
    );
}

// 使用例
$result = validateRegistrationForm($_POST);

if ($result->isErr()) {
    $errors = $result->unwrapErr();
    // エラーメッセージの配列を表示
    foreach ($errors as $error) {
        echo "<p class='error'>$error</p>";
    }
} else {
    // 登録処理
    createUser($_POST);
}
```

### 検証とデータ変換の組み合わせ

```php
use EndouMame\PhpMonad\Result;

class RegistrationData {
    public function __construct(
        public string $email,
        public string $password,
        public string $name
    ) {}
}

function validateAndTransform(array $data): Result {
    $emailResult = validateEmail($data['email'] ?? '');
    $passwordResult = validatePassword($data['password'] ?? '');
    $nameResult = validateName($data['name'] ?? '');

    // いずれかが失敗していればエラーを集約
    if ($emailResult->isErr() || $passwordResult->isErr() || $nameResult->isErr()) {
        $errors = array_filter([
            $emailResult->err()->unwrapOr(null),
            $passwordResult->err()->unwrapOr(null),
            $nameResult->err()->unwrapOr(null),
        ]);
        return Result\err($errors);
    }

    return Result\ok(new RegistrationData(
        $emailResult->unwrap(),
        $passwordResult->unwrap(),
        $nameResult->unwrap()
    ));
}
```

## データベース操作

### リポジトリパターン

```php
use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Result;

interface UserRepository {
    public function findById(int $id): Option;
    public function save(User $user): Result;
}

class PdoUserRepository implements UserRepository {
    public function __construct(private PDO $pdo) {}

    public function findById(int $id): Option {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row === false
            ? Option\none()
            : Option\some(User::fromArray($row));
    }

    public function save(User $user): Result {
        return Result\fromThrowable(
            function () use ($user) {
                $stmt = $this->pdo->prepare(
                    'INSERT INTO users (name, email) VALUES (?, ?)'
                );
                $stmt->execute([$user->name, $user->email]);
                return $this->pdo->lastInsertId();
            },
            fn($e) => "保存に失敗しました: {$e->getMessage()}"
        );
    }
}
```

### トランザクション処理

```php
use EndouMame\PhpMonad\Result;

function transferMoney(Account $from, Account $to, int $amount): Result {
    return Result\fromThrowable(
        function () use ($from, $to, $amount) {
            $this->pdo->beginTransaction();

            try {
                $from->withdraw($amount);
                $to->deposit($amount);

                $this->accountRepository->save($from);
                $this->accountRepository->save($to);

                $this->pdo->commit();
                return true;
            } catch (Throwable $e) {
                $this->pdo->rollBack();
                throw $e;
            }
        },
        fn($e) => "送金に失敗しました: {$e->getMessage()}"
    );
}
```

## ファイル操作

### 設定ファイルの読み込み

```php
use EndouMame\PhpMonad\Result;

function loadConfig(string $path): Result {
    if (!file_exists($path)) {
        return Result\err("設定ファイルが見つかりません: $path");
    }

    return Result\fromThrowable(
        fn() => file_get_contents($path),
        fn($e) => "ファイルの読み込みに失敗しました: {$e->getMessage()}"
    )
    ->andThen(fn($content) => Result\fromThrowable(
        fn() => json_decode($content, true, flags: JSON_THROW_ON_ERROR),
        fn($e) => "JSON のパースに失敗しました: {$e->getMessage()}"
    ))
    ->map(fn($data) => new Config($data));
}

// 使用例
$config = loadConfig('/etc/app/config.json')
    ->orElse(fn() => loadConfig('./config.json'))  // フォールバック
    ->orElse(fn() => Result\ok(Config::default()))        // デフォルト設定
    ->unwrap();
```

### ファイルの安全な書き込み

```php
use EndouMame\PhpMonad\Result;

function writeFile(string $path, string $content): Result {
    $dir = dirname($path);

    if (!is_dir($dir)) {
        return Result\fromThrowable(
            fn() => mkdir($dir, 0755, true),
            fn($e) => "ディレクトリの作成に失敗しました: {$e->getMessage()}"
        )->andThen(fn() => writeFile($path, $content));
    }

    return Result\fromThrowable(
        fn() => file_put_contents($path, $content),
        fn($e) => "ファイルの書き込みに失敗しました: {$e->getMessage()}"
    )->map(fn($bytes) => $bytes > 0);
}
```

## サービスクラスでの使用

### ユーザー登録サービス

```php
use EndouMame\PhpMonad\Result;

class UserRegistrationService {
    public function __construct(
        private UserRepository $users,
        private EmailService $email
    ) {}

    public function register(array $data): Result {
        // 1. バリデーション
        return $this->validate($data)
            // 2. 重複チェック
            ->andThen(fn($validated) => $this->checkDuplicate($validated))
            // 3. ユーザー作成
            ->andThen(fn($validated) => $this->createUser($validated))
            // 4. 確認メール送信
            ->andThen(fn($user) => $this->sendConfirmation($user));
    }

    private function validate(array $data): Result {
        return Result\combine(
            validateEmail($data['email'] ?? ''),
            validatePassword($data['password'] ?? ''),
            validateName($data['name'] ?? '')
        )->map(fn() => $data);
    }

    private function checkDuplicate(array $data): Result {
        return $this->users->findByEmail($data['email'])
            ->isSome()
            ? Result\err('このメールアドレスは既に登録されています')
            : Result\ok($data);
    }

    private function createUser(array $data): Result {
        $user = new User(
            email: $data['email'],
            password: password_hash($data['password'], PASSWORD_DEFAULT),
            name: $data['name']
        );

        return $this->users->save($user);
    }

    private function sendConfirmation(User $user): Result {
        return $this->email
            ->send($user->email, 'confirm', ['user' => $user])
            ->map(fn() => $user);
    }
}
```

## パイプライン処理

### データ変換パイプライン

```php
use EndouMame\PhpMonad\Option;

$result = Option\some($rawData)
    ->map(fn($data) => trim($data))
    ->filter(fn($data) => strlen($data) > 0)
    ->map(fn($data) => json_decode($data, true))
    ->filter(fn($data) => is_array($data))
    ->map(fn($data) => array_map('strtolower', $data))
    ->map(fn($data) => array_unique($data))
    ->unwrapOr([]);
```

### エラーリカバリーパイプライン

```php
use EndouMame\PhpMonad\Result;

$result = fetchFromPrimarySource($id)
    ->orElse(fn() => fetchFromSecondarySource($id))
    ->orElse(fn() => fetchFromCache($id))
    ->orElse(fn() => Result\ok(getDefaultValue()))
    ->inspect(fn($data) => updateCache($id, $data))
    ->unwrap();
```
