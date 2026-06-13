# Mini 4WD — WordPress (Docker)

Docker で動く WordPress 環境です。サイト制作・運用向けに、WordPress 本体と DB を永続ボリュームで保持します。送信メールは MailHog で確認できます。

## 構成

| サービス     | イメージ                  | 役割                       | アクセス                       |
| ------------ | ------------------------- | -------------------------- | ------------------------------ |
| `wordpress`  | `wordpress:php8.3-apache` | WordPress 本体 (PHP/Apache)| http://localhost:27080         |
| `db`         | `mysql:8.4`               | データベース               | （内部のみ）                   |
| `mailhog`    | `mailhog/mailhog`         | 送信メールのキャプチャ     | http://localhost:27025         |

> ポートは衝突しにくい **27xxx 帯**にしています。変更したい場合は `.env` の `WORDPRESS_PORT` / `MAILHOG_PORT` を編集してください。

## サイト（カスタムテーマ mini4wd）

「ミニ四駆学習会 from Nichinan City」のデザインを再現したカスタムテーマを `wordpress/themes/mini4wd/` に同梱しています（ホストから直接編集可・保存で即反映）。

| ページ | URL | テンプレート |
| --- | --- | --- |
| TOP | http://localhost:27080/ | `front-page.php` |
| アーカイブ一覧 | http://localhost:27080/archive/ | `archive-spec.php` |
| アーカイブ詳細 | http://localhost:27080/archive/spec11/ | `single-spec.php` |
| お知らせ一覧 | （TOPの「お知らせ一覧」から） | `index.php` |

- **アーカイブ（Spec）** はカスタム投稿タイプ `spec`。管理画面の「アーカイブ」から追加・編集できます（開催年・Spec番号・開催日・会場・各種URLは投稿画面の「開催情報」欄）。
- **お知らせ** は通常の投稿（カテゴリ「お知らせ」）。
- **ヒーロー文言・NEXT ROUND（次回情報）** は `front-page.php` 冒頭の `$hero` / `$next` を編集します。
- ポスター画像・ギャラリーは現在 CSS のプレースホルダーです。投稿のアイキャッチ画像を設定すると、そちらが優先表示されます。

## 管理画面（WordPress 管理者）

- URL: http://localhost:27080/wp-admin/
- ユーザー名: `admin`
- パスワード: `mini4wd-admin-2026`

> ローカル開発用の初期パスワードです。運用時は管理画面から必ず変更してください。

## 必要なもの

- Docker / Docker Compose（Docker Desktop など）

## 起動

```bash
# 初回 & 起動（バックグラウンド）
docker compose up -d

# 状態確認
docker compose ps

# ログ追従（初回は DB 初期化と WordPress 展開に数十秒）
docker compose logs -f wordpress
```

起動したらブラウザで以下にアクセスします。

- **WordPress**: http://localhost:27080 → 画面の案内に従って初期セットアップ（言語・サイト名・管理者ユーザー作成）
- **MailHog（メール確認）**: http://localhost:27025

## メール送信のテスト

WordPress から送られるメール（ユーザー登録・パスワードリセット・お問い合わせ等）はすべて **MailHog** に届きます。実際のメールは外部に送信されません。

- 仕組み: `wordpress/mu-plugins/mailhog.php`（must-use plugin）が `wp_mail()` を `mailhog:1025` 宛の SMTP 送信に切り替えています。
- 確認: http://localhost:27025 を開く

## よく使うコマンド

```bash
# 停止（データは保持）
docker compose stop

# 再開
docker compose start

# 停止 + コンテナ削除（ボリュームは残る = データ保持）
docker compose down

# WordPress コンテナ内でシェル操作
docker compose exec wordpress bash

# DB に接続
docker compose exec db mysql -u mini4wd -pmini4wd_pass mini4wd
```

## データの保存場所

- WordPress 本体（テーマ/プラグイン/アップロード画像）: 名前付きボリューム `wp_data`
- データベース: 名前付きボリューム `db_data`

`docker compose down` ではデータは消えません。**完全にリセット**したい場合のみ:

```bash
docker compose down -v   # ⚠ ボリューム削除 = WordPress とDBの全データを削除
```

## バックアップ（任意）

```bash
# DB ダンプ
docker compose exec db mysqldump -u mini4wd -pmini4wd_pass mini4wd > backup.sql

# wp-content（テーマ/アップロード等）を取り出す
docker compose cp wordpress:/var/www/html/wp-content ./wp-content-backup
```

## 設定

接続情報・ポートは `.env` で管理しています（`.env.example` がテンプレート）。
本番運用する場合は最低限 `MYSQL_ROOT_PASSWORD` と `WORDPRESS_DB_PASSWORD` を変更してください。

## 補足

- **Apple Silicon (arm64)**: MailHog は arm64 ネイティブイメージが無いため `platform: linux/amd64`（エミュレーション）で動かしています。起動は問題ありませんが、ネイティブが良ければ `mailhog/mailhog` を `axllent/mailpit`（同等機能・arm64対応・SMTP 1025 / UI 8025）に置き換え可能です。
- **サイトURL**: `WP_HOME` / `WP_SITEURL` を `http://localhost:27080` に固定しています。ポートを変えたら `.env` を直して `docker compose up -d` で再作成してください。
```
