# TiiMigration plugin

CakePHP3(Phinx) の Migration を利用し、view や stored procedure を管理するためのプラグイン。

## Requirements

- CakePHP 3.4+

## Installation

現状、composer に対応できていないので、手動でインストールする必要があります。

まず、plugins に、tiichat\migrations フォルダを作成し、ダウンロードしたファイルをすべてコピーします。

composer.json の autoload に、`"Tiichat\\...` の行を追記します。
```json
    "autoload": {
        "psr-4": {
            "App\\": "src",
            "Tiichat\\Migrations\\": "./vendor/tiichat/migrations/src"
        }
    },
```

ターミナルで、以下コマンドを実行し、新しいオートローダーを作成します。
```
> composer dumpautoload
```

config\bootstrap.php に、プラグインをロードする記述を追記します。
```php
Plugin::load('Tiichat/Migrations');
```

vendor\cakephp-plugins.php に、`"Tiichat\\...` の行を追記します。
```php
    'plugins' => [
        'Bake' => $baseDir . '/vendor/cakephp/bake/',
        'DebugKit' => $baseDir . '/vendor/cakephp/debug_kit/',
        'Migrations' => $baseDir . '/vendor/cakephp/migrations/',
        'Tiichat/Migrations' => $baseDir . '/plugins/tiichat/migrations/'
    ]
```

## Usage
まず、マイグレーションファイルの作成ですが、通常の migration と同じ様に、`tii_migration` コマンドを叩きます。ビューの場合は、`ViewBars` の様に "View" を頭につける必要があります。`ViewBars` の場合、`view_bars` という名前でビューを作成します。ビューも複数形にしておかないと、bake で model など作成する際に、規約違反となってしまうので注意が必要です。
```command
$ROOT$> bin\cake bake tii_migration ViewBars
```
これで、View のマイグレーションファイルと、DDL ファイルが生成されます。
```command
\config\Migrations
		20170519014643_ViewBars.php
\config\Migrations\ddl
		view_bars_1.ddl
```
初回の DDL ファイル `view_bars_1.ddl` の中身は空なので、
作りたいビューの DDL を記述します。ただし、`create view view_bars as` までは、マイグレーションファイルの方に記述しているので（Drop との整合性を考えてそうしてみました・・）、DDL には、`select ...` から記述します。

作成したビューに変更が入るときは、再度 bake します。
```command
$ROOT$> bin\cake bake tii_migration ViewBars
```
すると、
```command
\config\Migrations
		20170519014643_ViewBars.php
		20170519015211_ViewBars.php
\config\Migrations\ddl
		view_bars_1.ddl
		view_bars_2.ddl
```
こんな感じになるので、`view_bars_2.ddl` を編集します。

マイグレーションは、普通に実行してあげればＯＫです。
```command
$ROOT$> bin\cake migrations migrate
```
