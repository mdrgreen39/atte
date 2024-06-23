# Atte(アッテ：勤怠管理アプリケーション)
概要説明(どんなアプリか)
- 従業員の出退勤や休憩時間、労働時間を記録・管理するためのツールです。

## 作成した目的
- 従来の手作業による勤怠記録の煩雑さやエラーを解消し、効率的かつ正確な勤怠管理を実現するために作成しました。これを利用することで、管理側は労働時間の適正管理や労働コストの削減を図ることができ、従業員にとっても利便性の高い自己管理ツールとして機能します。

## 他のリポジトリ

## 機能一覧
- ユーザー登録
- ログイン
- ログアウト
- メールアドレス認証(検証)
- 勤務開始登録
- 勤務終了登録
- 休憩開始登録
- 休憩終了登録
- 日付別勤怠表示
- ユーザー一覧表示、検索
- ユーザー別勤怠表表示、検索

## 使用技術
- php7.4.9
- Laravel8.83.8
- mysql8.0.26

## テーブル設計
![art](table.png)

## ER図
![art](atte-er.drawio.png)

## 環境構築
**Dockerビルド**
1. `git@github.com:mdrgreen39/atte.git`
2. DockerDesktopアプリを立ち上げる
3. `docker-compose up -d --build`

> *MacのM1・M2チップのPCの場合、`no matching manifest for linux/arm64/v8 in the manifest list entries`のメッセージが表示されビルドができないことがあります。
エラーが発生する場合は、docker-compose.ymlファイルの「mysql」内に「platform」の項目を追加で記載してください*
``` bash
mysql:
    platform: linux/x86_64(この文追加)
    image: mysql:8.0.26
    environment:
```

**Laravel環境構築**
1. `docker-compose exec php bash`
2. `composer install`
3. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成
4. .envに以下の環境変数を追加
``` text
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
``` text
MAIL_MAILER=smtp
MAIL_HOST=           //メールサーバーアドレス入力
MAIL_PORT=           //メッセージの送信ポート
MAIL_USERNAME=       //送信元のユーザーネーム
MAIL_PASSWORD=       //メールサーバーのパスワード
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=  //送信元のメールアドレス
MAIL_FROM_NAME=     //メールの送信者に表示される名前
```

5. アプリケーションキーの作成
``` bash
php artisan key:generate
```

6. マイグレーションの実行
``` bash
php artisan migrate
```

7. シーディングの実行
``` bash
php artisan db:seed
```

## 他
- ユーザー一覧とユーザー別勤怠表表示では権限により閲覧を制限していますが、今回はユーザー登録時に全員に権限を付与し、閲覧可能としています。
- ユーザー別勤怠表の表示では、該当するユーザーが複数いる場合、検索に手動でインプットに入力し、検索ボタンを押してください。